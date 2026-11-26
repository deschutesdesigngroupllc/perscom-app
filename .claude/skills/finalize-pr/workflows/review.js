export const meta = {
  name: 'finalize-pr-review',
  description: 'Gate, review, and triage one branch diff into a normalized, ID-stable finding list (read-only)',
  whenToUse: 'Launched by the finalize-pr skill with the args it builds in its preflight step',
  phases: [
    { title: 'Gates', detail: 'PHPStan, Rector, and targeted tests, concurrently with review' },
    { title: 'Review', detail: 'two risk lenses, the spec, the review ledger, the simplifier, one agent per area' },
    { title: 'Triage', detail: 'merge, rate, verify every S1/S2/D, assign IDs' },
  ],
}

/*
 * args: {
 *   root: string, base: string, files: string[], // the scope: the whole branch, or a review round since its review
 *   branchBase: string|null, branchFiles: string[]|null, // the whole branch, in a round; null otherwise
 *   areas: [{ name: string, files: string[], sources: string[], skills: string[] }],
 *   newRules: string[],                       // rule files / CLAUDE.md this branch changes
 *   gates: { phpstan: boolean, rector: boolean, tests: string[], testsSkipped: string|null },
 *   simplifierAgentType: string|null,         // the laravel-simplifier plugin's agent type, when enabled
 *   simplifierPromptFile: string|null,        // else the Boost MCP laravel-code-simplifier prompt, saved to a file
 *   coverageNotes: string[],                  // partial coverage the preflight already knows about
 *   standardsSections: string[],              // every ##/### heading of .claude/rules/coding-standards.md
 *   lineLength: string[]|null,                // the preflight's `file:line: …` width lines; null when not measured
 *   spec: { file: string, issues: number[], criteria: number }|null, // the preflight's spec file and its criterion count; null when none
 *   specSkipped: string|null,                 // why spec is null, when the preflight knows
 *   ledger: { file: string, items: number, author: string }|null, // the PR's review comments, R1…; null with no open PR
 * }
 */
const A = { areas: [], newRules: [], coverageNotes: [], standardsSections: [], simplifierAgentType: null, simplifierPromptFile: null, lineLength: null, spec: null, specSkipped: null, ledger: null, branchBase: null, branchFiles: null, ...args }

const WHOLE = { base: A.branchBase || A.base, files: A.branchFiles || A.files }

const MEASURED_SECTIONS = ['Line Length', 'Checking It']
const checkedSections = A.lineLength === null
  ? A.standardsSections
  : A.standardsSections.filter(h => !MEASURED_SECTIONS.includes(h))

// Spend the expensive model only where a miss is costly. Gates run a command and relay its
// verdict; rule passes compare a diff against written rules, and triage re-checks anything
// S2 or worse. The lenses and triage hunt for what nothing written down describes.
const MODELS = { gate: 'haiku', rules: 'sonnet', lens: undefined, triage: undefined }

const LENS_CAP = 12
const AREA_CAP = 15

const AREA_ORDER = ['security', 'data', 'correctness', 'spec', 'review', 'conventions', 'docs', 'quality']
const SEV_ORDER = ['S1', 'S2', 'D', 'S3', 'S4']

const SEVERITY = `| Sev | Means |
|-----|-------|
| S1 | Exploitable, leaks or destroys data, or breaks every deploy. Blocks the merge |
| S2 | A user hits it on a normal path — a 500, a wrong number, an unreachable record. Blocks the merge |
| S3 | A project rule is broken, or a doc now states something false |
| S4 | Quality only — simpler, clearer, less duplicated. Changes no behavior |
| D  | Not a defect — correct or broken depending on an answer only the user has |`

const AREAS = `security (authorization, cross-tenant reach, privilege escalation) · data (central and tenant
migrations, seeders, one-time operations, integrity) · correctness (runtime errors, wrong results on a normal path) · spec (an acceptance
criterion missing, partial, or untested; a later issue decision the diff contradicts; work the spec did not
ask for; a reader of a changed value left disagreeing) · review (a PR review comment neither fixed nor
answered, or the pattern it names repeated elsewhere) · conventions (a
.claude/rules or skill rule the diff broke) · docs (CLAUDE.md, .claude/rules, .ai/rules, other companion
files) · quality (simplification, duplication)`

const GATE_CONTRACT = `Run exactly the command(s) below from the repository root and nothing else. Change no file and fix
nothing, whatever the output says. Report status "pass" if it ran clean, "fail" if it ran and reported
failures, and "unverified" if it never produced a trustworthy result — crashed, ran out of memory, timed
out, or failed before running anything. An unverified gate is never "pass" and never "fail"; say why.`

function producerContract(cap) {
  return `Read-only — change no file. Review only lines this branch adds or changes; a pre-existing problem in an
untouched line is out of scope unless the change makes it newly reachable (then set outsideDiff).
Return at most ${cap} findings, most severe first. For each: the file and line a reader would go to act;
"consequence" names what breaks for a user, a deploy, or a reader in one clause — "any user can read any
tenant's members", never "the query lost a where clause"; "fix" is an imperative phrase; "rule" is the
path of the rule, skill, or CLAUDE.md it breaks, when it breaks one. List in "notReached" every in-scope
file you did not get to — an omitted file reads as reviewed. List in "clean" each category you checked
and found clean, in a few words each.`
}

function scope(files, sources, skills, base = A.base) {
  const lines = [
    `Repository root: ${A.root}`,
    `Diff: \`git diff ${base} -- <path>\` (includes uncommitted work; an untracked file is new in full).`,
    'Files in scope:',
    ...files.map(f => `- ${f}`),
  ]
  if (A.newRules.length) {
    lines.push('', 'This branch changes these rule sources. Read their diff first and check the branch against its own new rules before the old ones:', ...A.newRules.map(f => `- ${f}`))
  }
  if (sources.length) {
    lines.push('', 'Read these in full before reviewing — a written project rule beats a generic instinct:', ...sources.map(f => `- ${f}`))
  }
  if (skills.length) {
    lines.push('', `Invoke each of these skills with the Skill tool and check the diff against it: ${skills.join(', ')}.`)
  }
  return lines.join('\n')
}

const FINDINGS = {
  type: 'object',
  properties: {
    findings: {
      type: 'array',
      items: {
        type: 'object',
        properties: {
          file: { type: 'string' },
          line: { type: 'integer' },
          consequence: { type: 'string' },
          fix: { type: 'string' },
          rule: { type: 'string' },
          outsideDiff: { type: 'boolean' },
        },
        required: ['file', 'line', 'consequence', 'fix'],
      },
    },
    clean: { type: 'array', items: { type: 'string' } },
    notReached: { type: 'array', items: { type: 'string' } },
  },
  required: ['findings', 'notReached'],
}

const SPEC_KINDS = ['gap', 'untested', 'unclear', 'contradiction', 'scope', 'reader']
const CRITERION_STATUS = ['met', 'untested', 'partial', 'missing', 'unclear']

const SPEC_FINDINGS = {
  type: 'object',
  properties: {
    criteria: {
      type: 'array',
      items: {
        type: 'object',
        properties: {
          id: { type: 'string' },
          issue: { type: 'integer' },
          text: { type: 'string' },
          derived: { type: 'boolean' },
          status: { type: 'string', enum: CRITERION_STATUS },
          code: { type: 'array', items: { type: 'string' } },
          tests: { type: 'array', items: { type: 'string' } },
          note: { type: 'string' },
        },
        required: ['id', 'text', 'status', 'code', 'tests'],
      },
    },
    findings: {
      type: 'array',
      items: {
        type: 'object',
        properties: {
          ...FINDINGS.properties.findings.items.properties,
          kind: { type: 'string', enum: SPEC_KINDS },
          criterion: { type: 'string' },
          question: { type: 'string' },
          options: { type: 'array', items: { type: 'string' } },
        },
        required: [...FINDINGS.properties.findings.items.required, 'kind'],
      },
    },
    clean: FINDINGS.properties.clean,
    notReached: FINDINGS.properties.notReached,
  },
  required: ['criteria', 'findings', 'notReached'],
}

const LEDGER_KINDS = ['outstanding', 'pattern', 'disputed', 'external']
const ITEM_STATUS = ['fixed', 'replied', 'outstanding', 'disputed', 'external']

const LEDGER_FINDINGS = {
  type: 'object',
  properties: {
    items: {
      type: 'array',
      items: {
        type: 'object',
        properties: {
          id: { type: 'string' },
          where: { type: 'string' },
          blocking: { type: 'boolean' },
          status: { type: 'string', enum: ITEM_STATUS },
          evidence: { type: 'string' },
        },
        required: ['id', 'where', 'status', 'evidence'],
      },
    },
    findings: {
      type: 'array',
      items: {
        type: 'object',
        properties: {
          ...FINDINGS.properties.findings.items.properties,
          locations: { type: 'array', items: { type: 'string' } },
          kind: { type: 'string', enum: LEDGER_KINDS },
          item: { type: 'string' },
          blocking: { type: 'boolean' },
          question: { type: 'string' },
          options: { type: 'array', items: { type: 'string' } },
        },
        required: [...FINDINGS.properties.findings.items.required, 'kind', 'item'],
      },
    },
    clean: FINDINGS.properties.clean,
    notReached: FINDINGS.properties.notReached,
  },
  required: ['items', 'findings', 'notReached'],
}

// Area agents must account for every coding-standards section: a skipped read otherwise
// returns the same "clean" as a careful one, and nothing downstream can tell them apart.
const AREA_FINDINGS = {
  ...FINDINGS,
  properties: {
    ...FINDINGS.properties,
    standards: {
      type: 'array',
      items: {
        type: 'object',
        properties: {
          section: { type: 'string' },
          verdict: { type: 'string', enum: ['clean', 'findings', 'not-applicable'] },
        },
        required: ['section', 'verdict'],
      },
    },
  },
  required: [...FINDINGS.required, 'standards'],
}

const STANDARDS = '.claude/rules/coding-standards.md'

function standardsChecklist() {
  return `Before anything else, Read ${STANDARDS} in full with the Read tool — it is long, and it is the ` +
    'rule most of this diff answers to. Then walk the diff against each of its sections below and return one ' +
    '"standards" entry per section: "findings" if you reported any under it, "clean" if the diff touches what ' +
    'it governs and obeys it, "not-applicable" if the diff contains nothing it governs. Every section needs an ' +
    'entry, its section name copied exactly as listed; a missing one is reported as unchecked.\n' +
    checkedSections.map(h => `- ${h}`).join('\n') +
    (A.lineLength === null ? '' : '\n\nLine widths were measured before this review; report none.')
}

const GATE = {
  type: 'object',
  properties: {
    status: { type: 'string', enum: ['pass', 'fail', 'unverified'] },
    note: { type: 'string' },
    failures: { type: 'array', items: { type: 'string' } },
  },
  required: ['status', 'note'],
}

const TRIAGED = {
  type: 'object',
  properties: {
    findings: {
      type: 'array',
      items: {
        type: 'object',
        properties: {
          area: { type: 'string', enum: AREA_ORDER },
          sev: { type: 'string', enum: SEV_ORDER },
          verified: { type: 'boolean' },
          file: { type: 'string' },
          line: { type: 'integer' },
          locations: { type: 'array', items: { type: 'string' } },
          files: { type: 'array', items: { type: 'string' } },
          consequence: { type: 'string' },
          fix: { type: 'string' },
          src: { type: 'array', items: { type: 'string' } },
          outsideDiff: { type: 'boolean' },
          criterion: { type: 'string' },
          item: { type: 'string' },
          question: { type: 'string' },
          options: { type: 'array', items: { type: 'string' } },
        },
        required: ['area', 'sev', 'verified', 'file', 'line', 'files', 'consequence', 'fix', 'src', 'outsideDiff'],
      },
    },
    dropped: {
      type: 'array',
      items: { type: 'object', properties: { summary: { type: 'string' }, reason: { type: 'string' } }, required: ['summary', 'reason'] },
    },
  },
  required: ['findings', 'dropped'],
}

// ── Gates: started now, awaited last. Nothing in review depends on them. ─────────────────────

phase('Gates')
const gateRuns = []
if (A.gates.phpstan) {
  gateRuns.push(['PHPStan', () => agent(
    `${GATE_CONTRACT}\n\nRepository root: ${A.root}\nCommand: vendor/bin/phpstan analyse --memory-limit=3G\n\n` +
    'The raised limit is deliberate: other gates run concurrently, and under that contention a worker at the default ' +
    '512M dies with "PHPStan process crashed because it reached configured PHP memory limit" — zero findings and a ' +
    'non-zero exit. If that still happens, report unverified with that reason, never pass.',
    { label: 'gate:phpstan', phase: 'Gates', schema: GATE, model: MODELS.gate, effort: 'low', agentType: 'general-purpose' })])
}
if (A.gates.rector) {
  gateRuns.push(['Rector', () => agent(
    `${GATE_CONTRACT}\n\nRepository root: ${A.root}\nCommand: vendor/bin/rector --dry-run\n\n` +
    'Not `composer rector`, which applies its changes.',
    { label: 'gate:rector', phase: 'Gates', schema: GATE, model: MODELS.gate, effort: 'low', agentType: 'general-purpose' })])
}
if (A.gates.tests.length) {
  gateRuns.push(['Tests', () => agent(
    `${GATE_CONTRACT}\n\nRepository root: ${A.root}\nRun these in sequence, one at a time — \`artisan test\` takes exactly one path:\n` +
    A.gates.tests.map(c => `- ${c}`).join('\n') +
    '\n\nA first parallel run after a schema change can deadlock during setup: re-run that one command once before ' +
    'calling it a failure. If every error is setup-shaped (missing table or column, unknown database, deadlock) with ' +
    'zero assertion failures, another test run on this machine is sharing the worker databases — report unverified ' +
    'with that reason and do not keep re-running.',
    { label: 'gate:tests', phase: 'Gates', schema: GATE, model: MODELS.gate, effort: 'low', agentType: 'general-purpose' })])
}
const gatesDone = parallel(gateRuns.map(([, run]) => run))

// ── Review: every producer at once. Triage needs all of them, so this is a real barrier. ─────

const producers = [
  {
    key: 'bugs', src: 'bug', cap: LENS_CAP, model: MODELS.lens,
    prompt: 'Find correctness and data-integrity defects in this branch: runtime errors, wrong results, broken ' +
      'migrations (a tenant table belongs in database/migrations/tenant), a queued job or listener that loses ' +
      'tenancy, an observer firing where it should not, missing companion registrations. Activate the ' +
      'laravel-best-practices skill. Prioritize the riskiest files first if you cannot read every one.\n\n' +
      scope(A.files, [], []),
  },
  {
    key: 'security', src: 'sec', cap: LENS_CAP, model: MODELS.lens,
    prompt: 'Find authorization and tenancy defects in this branch. Each tenant has its own database (stancl/tenancy), ' +
      'so look for: a central-connection query or route reaching tenant data, a tenant reaching another ' +
      'tenant\'s records; a policy skipped or a raw permission string checked instead of a policy ability; a ' +
      'member-facing surface gated on viewAny (the admin list) or an admin-only one left open; a Filament page, ' +
      'action, relation manager, or widget missing its authorization; an API (Orion) endpoint, MCP tool, signed ' +
      'URL, or feed token widening access; a client-settable field trusted. Ignore everything else.\n\n' + scope(A.files, [], []),
  },
  ...A.areas.map(area => ({
    key: `area:${area.name}`, src: 'rules', cap: AREA_CAP, model: MODELS.rules,
    prompt: `Check the "${area.name}" slice of this branch against the project rules that govern it. Look in particular ` +
      'for what a diff reader skips: a docblock or comment the change made stale; a CLAUDE.md line the change made ' +
      'false (contradiction is a finding, thinness is not); and a rule demanding a companion change in the same ' +
      'commit (a permission added to ShieldSeeder and a one-time operation for existing tenants, a new rule file ' +
      'and its row in .ai/rules/index.md, a new resource and its policy). Find companions by reading the rule, ' +
      'not from this list.\n\n' +
      scope(area.files, area.sources.filter(f => f !== STANDARDS), area.skills) +
      (area.files.some(f => f.endsWith('.php')) ? '\n\n' + standardsChecklist() : ''),
    schema: area.files.some(f => f.endsWith('.php')) ? AREA_FINDINGS : FINDINGS,
    standards: area.files.some(f => f.endsWith('.php')),
  })),
]
if (A.spec) {
  producers.push({
    key: 'spec', src: 'spec', cap: LENS_CAP, model: MODELS.lens, schema: SPEC_FINDINGS,
    prompt: `Review this branch against its spec. Read ${A.spec.file} in full first. It numbers the acceptance ` +
      'criteria C1…, each quoted from an issue (marked "(derived)" when the issue held none and the user asked for ' +
      'them to be derived), and quotes every decision posted on the issue after them.\n\n' +
      '1. Criteria. Return one "criteria" entry for every criterion in the file, none skipped, with its id, issue, ' +
      'text, and derived flag copied from the file, a status, the code that implements it, and the tests that prove ' +
      'it (`path::method`). Status: "met" — implemented, and a test fails if it breaks; "untested" — implemented, ' +
      'but no test would fail if it broke; "partial" — some of what it asks is missing, named in "note"; "missing" ' +
      '— not implemented; "unclear" — you cannot tell what it asks. A test proves a criterion only when it ' +
      'exercises the exact case named: a named boundary needs a test at that boundary, and a criterion naming ' +
      'several kinds (termination and purchase, a window and a single date) needs each kind. Every criterion not ' +
      '"met" also gets a finding of kind "gap" (partial or missing), "untested", or "unclear", with its id in ' +
      '"criterion".\n' +
      '2. Later decisions. Read each one against the diff. A decision that changes, narrows, or overturns what ' +
      'the branch does is a finding of kind "contradiction" that quotes it, with a "question" for the user and 2-4 ' +
      'short "options".\n' +
      '3. Readers. For every value whose meaning or output the diff changes — a column, a computed date, a title ' +
      'or category string, a method\'s return — search the code outside the diff for what else reads it: report ' +
      'sources, BI views, widgets, calendar and iCal output, workflow tokens, MCP tools, exports, notifications. A ' +
      'reader that now disagrees with what the diff shows elsewhere is a finding of kind "reader" naming both ' +
      'places, and so is an identifier built from the changed value (a UID or key hashed from a title), since ' +
      'changing the value changes the identity. The branch caused these, so set outsideDiff false even though ' +
      'the file is outside the diff.\n' +
      '4. Scope. A behavior change no criterion or later decision asks for is a finding of kind "scope", with a ' +
      '"question" whether it belongs in this branch and 2-4 "options".' +
      (A.ledger
        ? ` A change a reviewer asked for is not scope creep: ${A.ledger.file} lists, under "Settled in review", ` +
          'the resolved review threads, and a change one of them requested is cited, never raised.'
        : '') + '\n\n' +
      scope(WHOLE.files, A.ledger ? [A.spec.file, A.ledger.file] : [A.spec.file], [], WHOLE.base),
  })
} else {
  log(`Spec stage skipped — ${A.specSkipped || 'the preflight found no spec'}`)
}
if (A.ledger) {
  producers.push({
    key: 'ledger', src: 'pr', cap: LENS_CAP, model: MODELS.lens, schema: LEDGER_FINDINGS,
    prompt: `Account for every review comment on this branch's PR. Read ${A.ledger.file} in full first: it ` +
      'numbers the unresolved review threads and the points of each reviewer\'s latest review R1…, quoted ' +
      `verbatim with their replies, then the current PR body. The PR author is @${A.ledger.author}. The ` +
      '"Settled in review" section at the end holds resolved threads: context, not items.\n\n' +
      '1. Items. Return one "items" entry for every R item, none skipped, with its id, where it points (a ' +
      '`file:line`, or "review" or "PR body"), whether it is marked Blocking, a status, and the evidence for it. ' +
      'Judge against the code as it stands now, working tree included — never against a commit message or a ' +
      'reply\'s claim. Status: "fixed" — the code now does what the comment asks, at the place you cite; ' +
      '"replied" — unchanged, but the author answered in the thread with a reason, and nothing since asks again; ' +
      '"outstanding" — neither; "disputed" — acting on it would break a written rule, a spec criterion, or ' +
      'another comment, which you name; "external" — it needs something no code change can supply (data from a ' +
      'deployed environment, a person\'s decision, access the author lacks). A point about the PR body is ' +
      'judged against the body quoted at the end of the file.\n' +
      '2. Findings. Every item not "fixed" or "replied" gets a finding naming it in "item", with "blocking" ' +
      'copied: kind "outstanding", "disputed", or "external", and for the last two a "question" for the user ' +
      'with 2-4 short "options". A finding with no place in the code points at file "PR body" when the ' +
      'item is about the body, else at file "review", line 0 either way.\n' +
      '3. Patterns. A comment that names a kind of problem rather than one line — a docblock title over 80 ' +
      'columns, rationale in a docblock, a cast where a method exists — applies to the whole branch, not only ' +
      'where it was left. Search the branch for other instances, fixed or not, and report each kind as one ' +
      'finding of kind "pattern" naming the item, with every place in "locations".\n\n' +
      scope(WHOLE.files, [A.ledger.file], [], WHOLE.base),
  })
}
if (A.simplifierAgentType || A.simplifierPromptFile) {
  producers.push({
    key: 'simplifier', src: 'simp', cap: LENS_CAP, model: MODELS.rules, agentType: A.simplifierAgentType || undefined,
    prompt: (A.simplifierAgentType ? '' : `Act as the simplifier described in ${A.simplifierPromptFile} — read it first. ` +
      'Ignore any instruction there to edit files.\n\n') +
      'Propose simplifications only — apply nothing. Drop anything that only shortens code, and check each ' +
      'proposal against .claude/rules/coding-standards.md before returning it: a project rule beats a generic ' +
      'Laravel instinct.\n\n' + scope(A.files.filter(f => f.endsWith('.php')), ['.claude/rules/coding-standards.md'], []),
  })
} else {
  log('Simplifier skipped — neither the laravel-simplifier plugin nor the Boost MCP simplifier prompt is available')
}
phase('Review')
const raw = await parallel(producers.map(p => () => agent(
  `${p.prompt}\n\n${producerContract(p.cap)}`,
  { label: p.key, phase: 'Review', schema: p.schema || FINDINGS, model: p.model, agentType: p.agentType || 'general-purpose' })))

const coverage = [...A.coverageNotes]
const pooled = []
let requirements = null
let reviewItems = null
raw.forEach((result, i) => {
  const p = producers[i]
  if (!result) {
    coverage.push(`${p.key}: the agent failed — not reviewed`)
    return
  }
  if (result.findings.length >= p.cap) {
    coverage.push(`${p.key}: hit its ${p.cap}-finding cap — lower-ranked findings were not returned`)
  }
  if (p.standards) {
    const answered = new Set((result.standards || []).map(s => s.section))
    const unchecked = checkedSections.filter(h => !answered.has(h))
    if (unchecked.length) {
      coverage.push(`${p.key}: coding-standards sections not checked — ${unchecked.join(', ')}`)
    }
  }
  if (result.notReached.length) {
    coverage.push(`${p.key}: not reached — ${result.notReached.join(', ')}`)
  }
  if (p.key === 'ledger') {
    reviewItems = result.items
    if (result.items.length < A.ledger.items) {
      coverage.push(`ledger: ${A.ledger.items - result.items.length} of ${A.ledger.items} review items not returned — unchecked`)
    }
  }
  if (p.key === 'spec') {
    requirements = result.criteria
    if (result.criteria.length < A.spec.criteria) {
      coverage.push(`spec: ${A.spec.criteria - result.criteria.length} of ${A.spec.criteria} criteria not returned — unchecked`)
    }
  }
  result.findings.forEach(f => pooled.push({ ...f, src: p.src }))
})
if (!A.spec) {
  coverage.push(`spec: not reviewed — ${A.specSkipped || 'no spec was found for this branch'}`)
}
log(`${pooled.length} raw findings from ${raw.filter(Boolean).length}/${producers.length} producers`)

const widths = new Map()
;(A.lineLength || []).forEach(text => {
  const m = text.match(/^(.+?):(\d+): (.*)$/)
  if (!m) return
  const [, file, line, what] = m
  if (!widths.has(file)) widths.set(file, [])
  widths.get(file).push({ line: Number(line), what })
})
widths.forEach((lines, file) => {
  pooled.push({
    file,
    line: lines[0].line,
    locations: lines.map(l => `${file}:${l.line} (${l.what})`),
    consequence: `${lines.length} added line(s) over the width limit`,
    fix: 'Wrap per the Line Length rule',
    rule: '.claude/rules/coding-standards.md',
    outsideDiff: false,
    src: 'len',
  })
})

// ── Triage: one agent merges, rates, and verifies. IDs are assigned here in code, once. ──────

phase('Triage')
let triaged = { findings: [], dropped: [] }
let triageRan = false
if (pooled.length) {
  const result = await agent(
    `You are normalizing a pre-PR review into one list. Repository root: ${A.root}. Diff base: ${A.base}.\n\n` +
    'Raw findings from several independent sources, each tagged with its source:\n' +
    '```json\n' + JSON.stringify(pooled, null, 1) + '\n```\n\n' +
    'Do these, in order:\n\n' +
    '1. Merge duplicates. Two sources reaching the same defect is one finding with both tags in "src". This is ' +
    'common — the lenses and the area passes overlap on purpose.\n' +
    '2. Roll up repetition. The same rule broken in nine places, where a reader takes the same action at each, is ' +
    'one finding: put every place in "locations" and the first in file/line.\n' +
    '3. Set severity yourself from impact, never from a source\'s own wording:\n\n' + SEVERITY + '\n\n' +
    'Use D when a finding is correct only under an answer you do not have — a migration edited in place is a broken ' +
    'deploy or routine housekeeping depending on whether the environment is about to be reset. A D carries a ' +
    '"question" asked plainly and 2-4 short "options". Never guess an S1 where the honest answer is D.\n' +
    (A.spec
      ? 'Findings from "spec" carry a "kind" and come from the issue, not from impact you can see in the code, ' +
        'so rate them by kind: a "gap" on a quoted criterion is S2; a "gap" on a criterion marked derived, and ' +
        'every "unclear", "contradiction", and "scope", is D, keeping its question and options; an "untested" ' +
        'is S3; a "reader" is rated by its impact like any other defect. Keep "criterion" on every finding that ' +
        'carries one, and merge a spec finding with another only when both name the same defect at the same ' +
        `place. Verifying one means reading ${A.spec.file} and the code, not the code alone.\n`
      : '') +
    (A.ledger
      ? 'Findings from "pr" are review comments the branch has not settled; rate them by kind: an ' +
        '"outstanding" item marked blocking is S2, an unmarked one S3 unless what it describes is worse; a ' +
        '"pattern" is S3; a "disputed" or "external" item is D, keeping its question and options. Keep "item" ' +
        'on every finding that carries one. A "pattern" and a rules finding at the same places are one finding ' +
        `with both sources. Verifying one means reading ${A.ledger.file} and the code.\n`
      : '') +
    `4. Assign an area: ${AREAS}.\n` +
    '5. Verify every S1, S2, and D yourself: open the file at the line and try to refute the claim. Refuted → move ' +
    'it to "dropped" with the reason, or downgrade it if a lesser claim survives. verified=true only for findings ' +
    'you opened and confirmed. Leave S3/S4 unverified unless you happened to confirm them; never drop a finding ' +
    'just because it is unverified.\n' +
    '6. "files" lists every file the fix would edit, including companions (a lang file, a config map).\n\n' +
    'Keep "consequence" to one clause with no trailing period and "fix" to an imperative phrase — both must fit a ' +
    'table cell.',
    { label: 'triage', phase: 'Triage', schema: TRIAGED, model: MODELS.triage, effort: 'high', agentType: 'general-purpose' })
  if (result) {
    triaged = result
    triageRan = true
  }
}

const rank = f => [
  AREA_ORDER.indexOf(f.area),
  f.outsideDiff ? 1 : 0,
  SEV_ORDER.indexOf(f.sev),
  f.verified ? 0 : 1,
]
const ownedByBranch = f => (f.src || []).some(s => s === 'spec' || s === 'pr')
const findings = triaged.findings
  .map(f => (ownedByBranch(f) ? { ...f, outsideDiff: false } : f))
  .sort((a, b) => {
    const ra = rank(a), rb = rank(b)
    for (let i = 0; i < ra.length; i++) {
      if (ra[i] !== rb[i]) return ra[i] - rb[i]
    }
    return 0
  })
  .map((f, i) => ({ id: `F${i + 1}`, ...f }))

const gateResults = (await gatesDone).map((r, i) => ({
  gate: gateRuns[i][0],
  ...(r || { status: 'unverified', note: 'the gate agent failed or returned nothing' }),
}))
if (!A.gates.tests.length) {
  gateResults.push({ gate: 'Tests', status: 'unverified', note: A.gates.testsSkipped || 'no test path covers the diff' })
}
if (A.lineLength !== null) {
  const widthFindings = findings.filter(f => (f.src || []).includes('len'))
  if (!A.lineLength.length) {
    gateResults.push({ gate: 'Line length', status: 'pass', note: '' })
  } else if (!triageRan) {
    gateResults.push({
      gate: 'Line length',
      status: 'fail',
      note: `${A.lineLength.length} line(s) in ${widths.size} file(s), before triage — triage returned nothing`,
    })
  } else if (widthFindings.length) {
    gateResults.push({
      gate: 'Line length',
      status: 'fail',
      note: `${widthFindings.map(f => f.id).join(', ')} — ` +
        `${new Set(widthFindings.flatMap(f => f.files || [f.file])).size} file(s)`,
    })
  } else {
    gateResults.push({
      gate: 'Line length',
      status: 'pass',
      note: `triage dropped all ${A.lineLength.length} preflight line(s)`,
    })
  }
}

const cites = (value, id) => String(value || '').split(/[\s,;]+/).includes(id)
const requirementRows = requirements && requirements.map(c => ({
  ...c,
  findings: findings.filter(f => cites(f.criterion, c.id)).map(f => f.id),
}))

const reviewRows = reviewItems && reviewItems.map(r => ({
  ...r,
  findings: findings.filter(f => cites(f.item, r.id)).map(f => f.id),
}))

const count = sev => findings.filter(f => f.sev === sev).length
return {
  requirements: requirementRows,
  reviewItems: reviewRows,
  gates: gateResults,
  counts: { total: findings.length, S1: count('S1'), S2: count('S2'), S3: count('S3'), S4: count('S4'), D: count('D') },
  blockers: findings.filter(f => (f.sev === 'S1' || f.sev === 'S2') && !f.outsideDiff).map(f => f.id),
  decisions: findings.filter(f => f.sev === 'D').map(f => f.id),
  findings,
  dropped: triaged.dropped,
  coverage,
}
