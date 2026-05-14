# Skill Best Practices & Reference Guide

Complete reference for creating and maintaining skills in your Claude Code project. This is progressive disclosure — refer here for detailed patterns and advanced concepts.

## When to Create a Skill

| Need | Solution |
|------|----------|
| Always-needed context | AGENTS.md |
| User triggers specific prompt | Slash command (`.claude/commands/`) |
| Repeated workflow with scripts/docs | **Skill** |
| Troubleshooting/reference docs | Skill with `docs/` folder |

**Create a skill when:**
- Workflow has multiple steps or scripts
- Need to document commands/APIs for repeated use
- Building up knowledge over time (troubleshooting, patterns)

## Skill Structure (Self-Contained)

```
.claude/skills/my-skill/
├── SKILL.md              # routing + minimal docs
├── templates/            # templates INSIDE skill (self-contained)
│   └── my-template.md
├── workflows/            # step-by-step procedures
│   ├── create.md
│   └── publish.md
├── scripts/              # helper scripts
│   └── do-thing.py
└── docs/                 # detailed docs (on-demand)
    └── troubleshooting.md
```

**Key principle:** Skills are self-contained. Templates live inside the skill folder, not in a separate `.claude/templates/` folder. This way copying/moving a skill brings everything with it.

## SKILL.md Format

```markdown
---
name: my-skill
description: What it does. USE WHEN [trigger phrases].
---

# My Skill

## Quick Start
[Most common commands]

## Workflow Routing
- Creating X → `workflows/create.md`
- Publishing X → `workflows/publish.md`
```

**Key principle:** Workflows contain knowledge. SKILL.md stays minimal — just commands and routing table. Explain once in the workflow, encode it, never explain again.

Reference: [Daniel Miessler's PAI v2](https://danielmiessler.com/blog/personal-ai-infrastructure)

## Progressive Disclosure Pattern

**Problem:** Large skills bloat context, slow down agent.

**Solution:** Keep SKILL.md lean, put details in `docs/`.

| In SKILL.md (always loaded) | In docs/ (loaded on-demand) |
|-----------------------------|-----------------------------|
| Quick start commands | Full API reference |
| Common workflows | Edge cases |
| Brief troubleshooting pointers | Detailed troubleshooting |
| Script locations | Script implementation details |

**Example:**
```markdown
## Troubleshooting

### Audio Sync Issues
**Symptom:** Webcam drifts out of sync.
**Quick check:** `r_frame_rate=19200/1` = VFR problem.
**Full guide:** See [docs/vfr-sync-fix.md](docs/vfr-sync-fix.md)
```

**When to split to docs/:**
- Section is >50 lines
- Content is used <20% of the time
- Multiple sub-cases or variations
- Historical/example content

**Keep in SKILL.md when:**
- Used in >80% of skill invocations
- Required for basic operation
- Less than 10 lines

## Where to Store Skills

| Location | Purpose |
|----------|---------|
| `~/.claude/skills/` | Vault-specific |
| `~/projects/my-tools/skills/` | Shared/reusable (git repo) |

**Pattern:**
- Reusable → create in project repo, symlink to vault
- Vault-specific → create directly in `.claude/skills/`

```bash
# symlink shared skill into vault
ln -s ~/projects/my-tools/skills/my-skill .claude/skills/my-skill
```

## Creating a New Skill

**Checklist before creating:**
- [ ] Check for collision with existing skills
- [ ] Check if workflow belongs in existing skill instead
- [ ] Check for existing template to reference (don't duplicate structure)

**Steps:**
1. **Choose location** (shared vs vault-specific)
2. **Create folder:** `skills/skill-name/`
3. **Create SKILL.md** with frontmatter
4. **Add workflows/** for procedures
5. **Add scripts/** if needed
6. **Symlink** if in shared repository

## Templates in Skills

**Pattern:** Templates live INSIDE the skill that uses them.

```
.claude/skills/review/
  templates/
    morning-checkin.md    ← Template here
    evening-checkin.md
  workflows/
    morning/main.md       ← Embeds: ![[.claude/skills/review/templates/morning-checkin.md]]
```

**Why:**
- Self-contained - skill folder has everything it needs
- Single source of truth - template defines structure
- Embeddable - workflows can embed templates for visual context

**Embedding templates in workflows:**
```markdown
### Questions (single source of truth)
![[.claude/skills/review/templates/morning-checkin.md]]
```

**For vault-wide templates** (not skill-specific), use `Templates/` folder.

## Workflow Structure

Workflows live in `workflows/` directory with clear names (no nested `main.md` files).

### Data Access Section

Every workflow that queries Bases needs a Data Access section at the top:

```markdown
## Data Access

**FIRST:** Read `.claude/skills/obsidian-cli/SKILL.md` to understand how to query Obsidian Bases.

The embeds below show what data to pull. Use the Obsidian CLI:

\`\`\`bash
obsidian base:query path="PATH" view="VIEW" format=json
\`\`\`
```

**Why:** Claude needs to know HOW to get the data, not just what data to get.

### Embeds vs CLI

| Pattern | Purpose | Example |
|---------|---------|---------|
| Embed `![[base#view]]` | Visual reference in Obsidian | `![[Goals.base#Every morning]]` |
| CLI query | Agent actually gets data | `obsidian base:query path="..." view="..." format=json` |

**Embeds are for humans** viewing the workflow in Obsidian.
**CLI is for Claude** executing the workflow.

### Referencing Other Skills

When a workflow depends on another skill, be explicit:

```markdown
## Data Access

**FIRST:** Read `.claude/skills/obsidian-cli/SKILL.md` to understand queries.

## Step 3: Update Tasks

**Use:** Add `- [ ]` items to session files or update task frontmatter via Obsidian CLI (`obsidian property:set`).
```

**Pattern:** "FIRST: Read [skill]" or "Use: [skill] for [operation]"

### Step Numbering

Use consistent numbering:

```markdown
## Step 0: Environment Setup
## Step 0.5: Prerequisite Check
## Step 1: Main Action
## Step 2: Next Action
```

- Step 0: Setup (optional)
- Step 0.5: Prerequisites/checks (optional)
- Steps 1+: Main workflow

## Naming Conventions

- **Skill name:** lowercase, hyphens, max 64 chars (e.g., `video-edit`)
- **Must match:** folder name = frontmatter `name`
- **Scripts:** `do-verb.py` or `verb-noun.py`
- **Workflows:** `workflow-name.md` (e.g., `create.md`, `publish.md`)
- **Docs:** `topic-name.md` (e.g., `vfr-sync-fix.md`)

## CLI Tools

**Always use Python for CLI tools. Never Bash.**

- Use `argparse` with subparsers for commands
- Follow the pattern in existing Python CLI skills
- Shebang: `#!/usr/bin/env python3`
- Make executable: `chmod +x script.py`

**Why not Bash:**
- Harder to maintain and debug
- String handling is error-prone
- No proper argument parsing
- Mixing Bash + embedded Python is ugly

## Description Best Practices

The `description` determines when agent loads the skill. Be specific:

**Good:**
```yaml
description: Automate video post-processing (recording → audio processing → upload prep). Use for editing recordings, audio processing, or publishing workflow.
```

**Bad:**
```yaml
description: Video stuff.
```

**Include trigger phrases in description:** "Use when user mentions X, Y, or Z"

> ⚠️ Triggers belong in the `description` field, NOT as a separate section in SKILL.md body. The description is what Claude uses to match user requests to skills.

## Analyzing Skills for Collisions

Check if skill descriptions overlap and might cause confusion.

**List all skills with descriptions:**
```bash
for skill in .claude/skills/*/SKILL.md; do
  [ -f "$skill" ] && echo "$(grep -m1 '^name:' "$skill" | cut -d: -f2): $(grep -m1 '^description:' "$skill" | cut -d: -f2-)"
done
```

**What to look for:**
- Similar trigger phrases across different skills
- Overlapping domains (e.g., two skills both handle "tasks")
- Vague descriptions that could match many requests

**How to fix collisions:**
- Make descriptions more specific
- Add distinguishing trigger phrases
- Consider merging skills if they do the same thing
