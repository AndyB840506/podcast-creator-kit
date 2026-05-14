---
name: retrospective
description: Analyze the current session to extract reusable learnings and propose updates to relevant skills. Use when the user asks for a retrospective, what was learned, or how skills should be updated.
---

# Retrospective

Analyze the current session and extract learnings that should flow back into skills.

## When to Use

- After completing a multi-step workflow (examples from my setup — replace with your skills: video edit, launch, newsletter, etc.)
- When the user says "retrospective", "what did we learn", "update skills"
- Agent should SUGGEST running this after any session where:
  - Work was redone more than once
  - User corrected the approach ("this is not great", "don't do that")
  - Steps were improvised that aren't in the skill

## Quick Start

```
/retrospective                    # Analyze current session
/retrospective video              # Focus on video skill learnings
/retrospective launch             # Focus on launch skill learnings
```

## How It Works

### Step 1: Extract Signals

Scan the conversation for these patterns:

**Corrections** (highest priority):
- User rejected output: "this is not great", "remove this", "bullshit", "wrong"
- User redirected approach: "no, do it this way", "don't do that", "let's not"
- User added context the agent didn't have: "we have a skill for that", "follow the playbook"

**Redone work:**
- Something generated, rejected, regenerated with different approach
- Multiple iterations on the same artifact (3+ versions)
- Sub-agent output that had to be cleaned up or rewritten

**Missing steps:**
- Things improvised that weren't in the workflow
- Steps that should have been in the checklist but weren't
- Tools/scripts that existed but weren't referenced in the skill

**What worked well:**
- Patterns that produced good results on first try
- New approaches that should become the default
- Shortcuts that saved time

### Step 2: Map to Skills

For each learning, identify:
- Which skill file needs updating
- The specific section to modify
- Whether it's a new rule, a fix to an existing rule, or a removal

### Step 3: Propose Diffs

Present findings as a table:

```
| # | Learning | Skill File | Change |
|---|----------|-----------|--------|
| 1 | Tags once, never in opening | x-article.md | Add tag rules section |
| 2 | LinkedIn needs paragraphs | launch.md | Add to formatting rules |
| 3 | Quartz post required | newsletter.md | Add Step 7a |
```

Then show the actual edits for approval.

### Step 4: Apply

After user approves, apply all edits. One edit per skill file, show the diff.

## What NOT to Encode

- One-off fixes that won't recur
- Content-specific decisions (which diagram to use for THIS video)
- Temporary state (program starts Mar 17 - that changes)
- Things already in the playbook/voice profile

## What TO Encode

- Process changes: "always do X before Y"
- Anti-patterns: "never do X, it causes Y"
- Tool behavior: "NoteTweet doesn't support --media"
- Format rules: "X articles use paragraphs, not one-sentence-per-line"
- Missing steps: "create Quartz post, not just images"
- Proven patterns: "real screenshots + hand-drawn diagrams together"

## Solution Quality: Think Like Mario

Proposed fixes must follow first-principles system design principles. No quick patches that create debt.

**Before proposing a fix, ask:**
1. Is this fixing the symptom or the cause? (Principle #1: minimal core)
2. Should this be a registry entry, not a hardcoded check? (#2: registry over inheritance)
3. Am I adding a field/flag when I should fix the data flow? (#11: extend views not data model)
4. Will this fix survive the next state run, or will it break again? (#6: boundary transformation)

**Anti-patterns in retrospective fixes:**
- "Add a special case for X" → find the general rule X violates
- "Check for X after the fact" → prevent X from entering the pipeline
- "Add a validation step" → fix the source of invalid data
- "Hardcode this exception" → make it a registry entry or classification rule

**Example:** "merge-tier2.py drops category" is not fixed by "add category back in build-dashboard.py." It's fixed by making merge-tier2.py preserve the field. Fix at the source, not downstream.

## Auto-Suggest Convention

After completing any multi-step workflow, the agent should check:
1. Were there more than 2 corrections from the user?
2. Was any artifact regenerated 3+ times?
3. Were steps improvised that aren't in the skill?

If yes to any: suggest `/retrospective` before moving on.

This is NOT automatic - just a suggestion. The user decides whether to run it.
