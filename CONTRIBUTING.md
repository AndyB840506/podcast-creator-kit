# Contributing to Claude Code Skills

Thanks for wanting to improve these skills! This guide explains how to fork, branch, and submit improvements.

---

## Getting Started

### 1. Fork the Repository
Click **Fork** on GitHub to create your own copy: `yourusername/podcast-creator-kit`

### 2. Clone Your Fork
```bash
git clone https://github.com/yourusername/podcast-creator-kit.git
cd podcast-creator-kit
git remote add upstream https://github.com/AndyB840506/podcast-creator-kit.git
```

### 3. Create a Branch
Branch names should describe the improvement:
```bash
git checkout -b improve/prompt-reviewer-clarity
git checkout -b fix/btq-typo-in-script
git checkout -b add/new-skill-lead-scorer
```

**Branch naming:**
- `improve/` — enhancing existing skills
- `fix/` — bug fixes or corrections
- `add/` — new skills or major additions
- `docs/` — documentation updates

---

## Making Changes

### What Can You Improve?

✅ **Do improve:**
- Clarity of instructions
- Missing edge cases
- Better examples
- Typos or grammar
- More specific triggers
- Better error handling
- Validated against real use

❌ **Don't:**
- Change tone/voice without reason
- Remove rules without justifying
- Make it more complex (simpler is better)
- Add dependencies without necessity

### Testing Your Changes

Before submitting, test that your changes work:

1. **If you modified a skill:** Simulate using it. Are the instructions clear? Can someone follow them without confusion?
2. **If you added a new skill:** Does it trigger correctly? Do all the steps make sense?
3. **Check formatting:** Frontmatter valid? Markdown consistent?

---

## Submitting Your Changes

### 1. Commit with Clarity
```bash
git commit -m "improve/prompt-reviewer: add concrete BTQ examples

- Added 3 example scenarios (guión, artwork, social copy)
- Shows symptom fixes vs. root cause fixes
- Helps users understand depth of analysis needed"
```

### 2. Push to Your Fork
```bash
git push origin improve/prompt-reviewer-clarity
```

### 3. Create a Pull Request
On GitHub, click **"New Pull Request"** and describe:

**Title:** `improve/prompt-reviewer: better BTQ examples`

**Description:**
```markdown
## What changed?
- Added concrete examples from BTQ podcast workflow
- Clarified difference between symptom and root cause fixes

## Why?
The "Think Like Mario" section was too abstract. These examples show it in action.

## Testing
- Read through the examples
- Verified they make sense for podcast workflows
- Checked formatting

## Checklist
- [x] Tested the skill/change
- [x] Frontmatter is valid (if skill)
- [x] Markdown formatting is consistent
- [x] No unnecessary complexity added
```

---

## Code of Conduct

- **Be respectful** — we're all learning and improving
- **Explain your reasoning** — "why" is as important as "what"
- **Keep scope focused** — one improvement per PR when possible
- **Assume good intent** — feedback is about the idea, not the person

---

## What Happens After You Submit

1. **Review** — I'll review your changes and may ask questions
2. **Feedback** — Constructive suggestions for improvement
3. **Merge** — Once approved, your changes go live
4. **Credit** — You'll be attributed in commit history and changelog

---

## Questions?

If you're unsure about something:
- Check the existing skills for patterns
- Read the README.md for context
- Open an issue to discuss before spending time on large changes

---

## Recognition

Contributors are recognized in:
- Git commit history (forever)
- Changelog (if major improvements)
- README credits section (if you want)

Thanks for making these skills better! 🚀
