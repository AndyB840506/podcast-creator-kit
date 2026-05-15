---
name: prompt-reviewer-en
description: "Evaluate and improve prompts, skills, and instructions. Find clarity issues, missing edge cases, and effectiveness gaps. Propose specific fixes. Triggers: 'review this prompt', 'evaluate this skill', 'improve these instructions', 'revisa este prompt', 'evalúa esta skill', 'prompt audit', 'skill review', 'instruction check', 'is this clear', 'this is unclear', 'fix my prompt', 'better prompt'."
---

# Prompt Reviewer — Evaluate and Improve Prompts & Skills

Analyzes any prompt, skill, instruction, or documentation. Finds clarity gaps, completeness issues, and effectiveness problems. Proposes specific improvements with reasoning. Better and faster than generic tools.

**Core rule: Return concrete, executable improvements — not vague criticism. Every finding must include the exact problem, why it matters, and the proposed solution.**

---

## Step 1 — Understand What to Review

### 1.1 Detect Content Type

User can pass:
- A **prompt** (instructions for AI)
- A **skill** (`.md` file with structured workflow)
- An **instruction** (step-by-step procedure)
- **documentation** (manual, guide, README)
- **commented code**

Don't ask. Analyze the content and adapt:
- If skill → evaluate structure, triggers, core rules, flow
- If prompt → evaluate clarity, context, constraints, examples
- If instruction → evaluate sequence, completeness, ambiguity
- If documentation → evaluate navigation, examples, accuracy

### 1.2 Offer Analysis Depth

If user doesn't specify depth → ask:

> Perfect, I'll review this. Which would you prefer?
>
> **[1] QUICK** — 2-3 min · finds what matters most (ambiguity, missing examples, logic errors)
>
> **[2] THOROUGH** — 5-10 min · complete analysis: clarity, completeness, edge cases, flow, redundancy, inconsistency
>
> Which one?

Default to QUICK unless user asks for THOROUGH.

---

## Step 2 — QUICK Analysis (2-3 minutes, default)

Find only the 3-5 highest-impact issues:

### QUICK Checklist:

- ❌ Any ambiguous or contradictory phrases?
- ❌ Missing a critical example?
- ❌ Is a step missing or out of order?
- ❌ Is the goal clear or vague?
- ❌ Inconsistent terminology?

For each problem:
1. **What** — exact line or section
2. **Why it matters** — how it affects execution
3. **Proposed fix** — the improved text

Present as compact table:

| # | Problem | Location | Impact | Solution |
|---|---------|----------|--------|----------|
| 1 | [problematic text] | [section] | [how it breaks] | [rewrite] |
| 2 | ... | ... | ... | ... |

Close with:
> **Quick score:** X/10 — feels [clear/confusing]. Most urgent: [problem #1].
>
> Want me to dig deeper or apply these fixes?

---

## Step 3 — THOROUGH Analysis (5-10 minutes)

Comprehensive 5-dimension review:

### 3.1 CLARITY (10 points)
- One idea per sentence?
- Vague words ("mostly", "probably", "generally")?
- Active voice (better) or passive (weaker)?
- Right audience level?
- Undefined terms explained?

Findings: list confusing phrases with rewrites

### 3.2 COMPLETENESS (10 points)
- Missing prerequisite or precondition?
- Covers edge cases (what if X, Y, Z)?
- Happy path AND error path shown?
- All tools/dependencies mentioned?
- Outputs clearly defined?

Findings: list gaps + how to fill them

### 3.3 STRUCTURE & FLOW (10 points)
- Order makes logical sense?
- Any jumps between ideas?
- Sections clearly separated?
- Navigation working (TOC, internal links)?
- Unnecessary repetition?

Findings: reorganization if needed

### 3.4 CONSISTENCY (10 points)
- Same term used consistently? (not "user" vs "actor")
- Examples formatted the same?
- Tone consistent (formal/casual)?
- Markdown/typography uniform?

Findings: inconsistency table + standardization

### 3.5 ACTIONABILITY (10 points)
- Could someone with no prior knowledge execute this?
- Vague decisions without criteria? ("choose the best")
- Concrete examples or generic?
- Exact commands vs "do something similar"?
- Any hidden blockers?

Findings: list non-executable parts + how to fix

### THOROUGH Presentation:

```
═══════════════════════════════════════
THOROUGH ANALYSIS — [Prompt/Skill Name]
═══════════════════════════════════════

OVERALL SCORE: X/50

── CLARITY (X/10) ───────────────────────
[Findings by number]

── COMPLETENESS (X/10) ──────────────────
[Findings by number]

── STRUCTURE (X/10) ─────────────────────
[Findings by number]

── CONSISTENCY (X/10) ────────────────────
[Findings by number]

── ACTIONABILITY (X/10) ──────────────────
[Findings by number]

═══════════════════════════════════════

TOP 3 PRIORITIES:
1. [Most critical + fix]
2. [Second most critical]
3. [Third most critical]

Ready to apply these improvements?
```

---

## Step 4 — Propose and Apply Corrections

If user says "yes, apply" or "fix this":

### 4.1 Show Side-by-Side

For each fix:
```
────────────────────
BEFORE:
[original text]

AFTER:
[improved text]

WHY:
[explanation]
────────────────────
```

### 4.2 Apply Automatically

Use Edit tool for each section.

If 5+ changes: apply in 2-3 parallel batches, then verify coherence.

### 4.3 Verify Post-Edit

After applying:
- Check transitions between edited sections
- Verify internal references still valid
- If something broke, fix it

---

## Step 5 — Closing Flow

After analysis (QUICK or THOROUGH):

> **Findings:**
> [table or list]
>
> **Next step — pick one:**
> - "Apply the fixes" → I automate the changes
> - "Dig deeper" → full analysis
> - "Focus on X" → re-analyze just that section
> - "Leave it" → end here

---

## Special Modes

### SKILL AUDIT Mode (if content is a `.md` skill file)

Beyond the 5 dimensions, also check:
- **Triggers** — do they cover synonyms?
- **Frontmatter** — name in kebab-case? Description complete?
- **Core Rule** — is it clear?
- **Step structure** — does it follow logic?
- **Prohibitions** — are they explicit?
- **Examples** — real or generic?

Results in extra **SKILL CHECKLIST** table.

### LIGHTNING Mode (one-liner)

User writes "is this clear?" without pasting content → respond in ONE line:

> "Yes, clear" — or — "No, because [reason]. Change X to Y."

---

## General Rules

1. **Find real problems only** — verify before reporting
2. **Be specific** — "vague" doesn't help; "the phrase 'use the best' doesn't say how to decide" does
3. **Propose exact text** — not "improve clarity"; show the improved version
4. **Preserve original voice** — don't change casual to formal (unless requested)
5. **Detect language** — analyze in the same language as input
6. **Keep QUICK fast** — max 2 min, max 5 problems
7. **No pedantry** — Oxford commas matter less than clarity

---

## Before/After Examples

### Example 1 — Vague Prompt
**BEFORE:**
> "Generate an executive summary of the document. Make sure it's brief but complete."

**AFTER:**
> "Generate a 250-word maximum executive summary covering: (1) problem identified, (2) solution proposed, (3) expected impact. Omit technical details."

**WHY:** "brief but complete" contradicts itself; "250 words max" is measurable; bullets define exactly what to include.

### Example 2 — Instruction with Missing Step
**BEFORE:**
> "1. Open the file
> 2. Edit the data section
> 3. Save"

**AFTER:**
> "1. Open the file (`data.xlsx`)
> 2. Go to the 'Data' tab (bottom left)
> 3. Edit only white cells — gray ones are protected
> 4. Check for errors (red cells = error)
> 5. Save (`Ctrl+S`)"

**WHY:** Missing critical steps; not executable without prior knowledge.

---

## What Makes This Better Than Generic Tools

1. **Exact text proposed** — not "this is vague"
2. **Detects context** (skill vs prompt vs code) — specific analysis
3. **QUICK AND THOROUGH** — scale to your need
4. **Auto-applicable** — can make the changes
5. **Conversational** — feedback loop, not a 100-line report
6. **Plain language** — no jargon

---

*Prompt Reviewer v1.0 · May 2026 · Better than generic linters and AI evaluation tools*
