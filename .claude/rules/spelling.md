# Spelling

Use American English in code, comments, docs, and UI text.

- `-ize` endings, not `-ise`: organize, normalize, customize, recognize, summarize, prioritize.
- `-or` endings, not `-our`: color, behavior, honor, favor.
- `-er` endings, not `-re`: center, meter, theater.
- `analyze`, `catalog`, and `inquiry`, not `analyse`, `catalogue`, and `enquiry`.

**One exception: `cancelled`.** The codebase spells it with two `l`s in method names, enum cases,
notifications, and UI text (`isCancelled()`, `EventOccurrenceCancelled`, "Cancelled"). Keep
`cancelled`, `cancelling`, and `cancellation` so new code matches what it calls.

## Quick Sweep

Run this against new files before committing:

```bash
grep -rniE --include='*.php' --include='*.md' --include='*.blade.php' \
  "customis|normalis|standardis|organise|organising|organised|organisation|recognis|analyse|analysing|analysed|favour|colour|behaviour|honour|centre|realise|realising|realised|prioritis|summaris|catalogue|enquir" \
  path/to/files
```

No output means no slips.
