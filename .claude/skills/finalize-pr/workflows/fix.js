export const meta = {
  name: 'finalize-pr-fix',
  description: 'Apply the finalize-pr findings the user selected, one agent per disjoint file group, then re-run the gates',
  whenToUse: 'Launched by the finalize-pr skill after the user picks which findings to fix',
  phases: [
    { title: 'Fix', detail: 'one agent per group of findings that share no file with another group' },
    { title: 'Format', detail: 'Pint over every file the fixes touched' },
    { title: 'Gates', detail: 'only the gates the fixes could have moved' },
  ],
}

/*
 * args: {
 *   root: string, base: string,
 *   findings: [{ id, area, sev, file, line, locations?, files, consequence, fix }],
 *   decisions: { [id: string]: string },     // the user's answer to each D finding being fixed
 *   gates: { phpstan: boolean, rector: boolean, tests: string[], lineLengthScript: string|null },
 * }
 */
const A = args

const MAX_GROUPS = 8
// A group carrying any behavior fix gets the session model; a group of rule, doc, and
// quality fixes is mechanical enough for Sonnet. Formatting and gates only relay a command.
const BEHAVIOR_AREAS = ['security', 'data', 'correctness', 'spec', 'review']
const MODELS = { behavior: undefined, mechanical: 'sonnet', command: 'haiku' }
// Each stage moves the next one's target: a rewritten method moves the line a docblock finding pointed at.
const APPLY_ORDER = ['quality', 'security', 'data', 'correctness', 'spec', 'review', 'conventions', 'docs']

const OUTCOMES = {
  type: 'object',
  properties: {
    results: {
      type: 'array',
      items: {
        type: 'object',
        properties: {
          id: { type: 'string' },
          outcome: { type: 'string', enum: ['fixed', 'skipped', 'no_change_needed'] },
          note: { type: 'string' },
        },
        required: ['id', 'outcome', 'note'],
      },
    },
    touched: { type: 'array', items: { type: 'string' } },
  },
  required: ['results', 'touched'],
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

// ── Group findings so no two groups edit the same file: union-find over shared files. ────────

const parent = A.findings.map((_, i) => i)
const find = i => (parent[i] === i ? i : (parent[i] = find(parent[i])))
const owner = {}
A.findings.forEach((f, i) => {
  const files = f.files.length ? f.files : [f.file]
  files.forEach(path => {
    if (path in owner) {
      parent[find(i)] = find(owner[path])
    } else {
      owner[path] = i
    }
  })
})
const byRoot = {}
A.findings.forEach((f, i) => {
  const r = find(i)
  ;(byRoot[r] = byRoot[r] || []).push(f)
})

// Disjoint groups merge freely, so fold the smallest together until the agent count fits.
let groups = Object.values(byRoot).sort((a, b) => b.length - a.length)
while (groups.length > MAX_GROUPS) {
  const smallest = groups.pop()
  groups[groups.length - 1] = groups[groups.length - 1].concat(smallest)
  groups.sort((a, b) => b.length - a.length)
}
log(`${A.findings.length} findings in ${groups.length} file-disjoint groups`)

const describe = f => {
  const where = (f.locations && f.locations.length ? f.locations : [`${f.file}:${f.line}`]).join(', ')
  const answer = A.decisions[f.id] ? `\n  The user decided: ${A.decisions[f.id]}` : ''
  return `- **${f.id}** (${f.sev}, ${f.area}) ${where}\n  Breaks: ${f.consequence}\n  Fix: ${f.fix}${answer}`
}

phase('Fix')
const applied = await parallel(groups.map((group, g) => () => {
  const ordered = group.slice().sort((a, b) => APPLY_ORDER.indexOf(a.area) - APPLY_ORDER.indexOf(b.area))
  return agent(
    `Apply these review findings in this repository (${A.root}), in the order listed:\n\n` +
    ordered.map(describe).join('\n') +
    '\n\nBefore each one, re-read the code: an earlier fix in this list may have moved the line or made the finding ' +
    'moot — then report no_change_needed rather than applying it blind. Skip, with the reason, any fix that turns ' +
    'out to need a decision the user has not made. Follow the project rules that govern each file. Edit only the ' +
    `files these findings name: ${[...new Set(ordered.flatMap(f => f.files.length ? f.files : [f.file]))].join(', ')}. ` +
    'Other agents are editing other files at the same moment, so a write outside that list can silently drop ' +
    'theirs. When a behavior fix needs a test in a file not on the list, apply the fix and name the test needed ' +
    'in the note instead of writing it. Do not commit, push, run Pint, or run the ' +
    'full suite — formatting and gates run after every group finishes. Return every file you edited in "touched".',
    {
      label: `fix:${ordered.map(f => f.id).join(',')}`,
      phase: 'Fix',
      schema: OUTCOMES,
      model: ordered.some(f => BEHAVIOR_AREAS.includes(f.area)) ? MODELS.behavior : MODELS.mechanical,
      agentType: 'general-purpose',
    })
}))

const results = []
const touched = new Set()
applied.forEach((r, g) => {
  if (!r) {
    groups[g].forEach(f => results.push({ id: f.id, outcome: 'skipped', note: 'the fix agent failed' }))
    return
  }
  results.push(...r.results)
  r.touched.forEach(p => touched.add(p))
})

// ── Format, then re-gate only what the fixes could have moved. ──────────────────────────────

const php = [...touched].filter(p => p.endsWith('.php'))
const codeChanged = php.length > 0

if (php.length) {
  phase('Format')
  await agent(
    `From ${A.root}, run exactly this and report only its exit status:\n\nprintf '%s\\n' ${php.map(f => `'${f}'`).join(' ')} | xargs vendor/bin/pint --parallel`,
    { label: 'pint', phase: 'Format', model: MODELS.command, effort: 'low', agentType: 'general-purpose' })
}

const GATE_CONTRACT = 'Run exactly the command(s) below from the repository root and nothing else. Change no file. ' +
  'Report "pass" if it ran clean, "fail" if it ran and reported failures, "unverified" if it never produced a ' +
  'trustworthy result — and why.'

const gateRuns = []
if (codeChanged && A.gates.phpstan) {
  gateRuns.push(['PHPStan', `composer analyze`])
}
if (codeChanged && A.gates.rector) {
  gateRuns.push(['Rector', `vendor/bin/rector --dry-run`])
}
if (codeChanged && A.gates.tests.length) {
  gateRuns.push(['Tests', A.gates.tests.join('\n') + '\n(one at a time, in sequence)'])
}
if (codeChanged && A.gates.lineLengthScript) {
  gateRuns.push(['Line length', `bash ${A.gates.lineLengthScript}\n` +
    '(it prints one `file:line: …` per line over the limit: "pass" when it prints nothing, "fail" with each ' +
    'printed line in failures otherwise)'])
}

phase('Gates')
const gates = (await parallel(gateRuns.map(([name, cmd]) => () => agent(
  `${GATE_CONTRACT}\n\nRepository root: ${A.root}\nCommand(s):\n${cmd}`,
  { label: `gate:${name.toLowerCase()}`, phase: 'Gates', schema: GATE, model: MODELS.command, effort: 'low', agentType: 'general-purpose' }))))
  .map((r, i) => ({ gate: gateRuns[i][0], ...(r || { status: 'unverified', note: 'the gate agent failed' }) }))

return {
  results,
  touched: [...touched],
  gates,
  regated: codeChanged ? 'code changed — gates re-run' : 'only docs changed — gates not re-run',
}
