---
name: skill-creator
description: "Create custom Claude Code skills from scratch. The skill that makes skills. Use this when you want to build your own skill, automate a workflow, make Claude repeat a process, create a custom command, or convert something manual into something automatic. Triggers: 'create a skill', 'I want a skill', 'custom skill', 'automate this as a skill', 'create a Claude Code command', 'convert this to a skill', 'make this automatic', 'skill for X'."
---

# Skill Creator — Build Your Own Skills

Describe a process you want to automate and I'll generate a complete, ready-to-use skill. It's the tool that builds tools.

Claude Code skills are `.md` files that teach Claude to do specific tasks. Any repetitive process can become a skill. Works in any language you prefer.

---

## Step 1 — Understand What You Need

Ask conversationally:

- **What should Claude do automatically?** — describe the outcome you want
- **What information does it need?** — URL, text, folder, data, file...
- **What should it generate?** — HTML, report, file, code, dashboard...
- **Will you use this yourself or share with others?**

If you've already described enough (e.g., "a skill that reads a CSV of products and generates product cards in HTML"), I'll design directly.

If you're unsure what skill to build, here are ideas:

**For Business:**
- Commercial proposal generator (client data → professional PDF/HTML)
- Budget calculator (service + hours → detailed quote)
- Contract generator (data → customized contract)
- Sales presentation creator (product → HTML slides)
- Client onboarding (data → folder + emails + docs)

**For Marketing:**
- Ad copy generator (product + audience → ad variations)
- Content planner (niche → 30-day calendar with ideas)
- Sales email creator (product → email sequence)
- Social media post generator (topic → Instagram/LinkedIn/X posts)

**For Development:**
- API generator (data model → complete API)
- Code documenter (repository → documentation)
- Test generator (code → test suite)
- Project scaffolder (project type → complete structure)

**For Productivity:**
- Document summarizer (PDF → executive summary)
- Meeting transcriber (notes → formal minutes)
- SOP generator (process → step-by-step procedure doc)
- Data analyzer (CSV → dashboard with insights)

---

## Step 2 — Design the Skill

Before writing, plan the structure:

1. **Input** — what does the skill ask the user for?
2. **Process** — what steps does it follow (in order)?
3. **Tools** — what does it use? (WebFetch, Bash, Read, Write, native Claude Code tools)
4. **Output** — what does it generate and in what format?
5. **User experience** — how does it feel to use? (friendly messages, natural flow)

### Design Principles (Learned from 9+ Skills)

These make a skill genuinely good:

**1. Never invent data** — if the skill needs info from the user (services, prices, contact, testimonials), ask. Never make it up. If unavailable, use visible placeholders or omit the section.

**2. Real data first, questions second** — if the skill can fetch data automatically (scraping, WebFetch, WebSearch), do it first. Only ask what you can't find alone.

**3. Auto-install dependencies** — if it needs Playwright, npm packages, or other tools, install automatically. Message user friendly ("Preparing tools, 30 seconds first time").

**4. Creative freedom in design** — if generating HTML/dashboards, don't mandate rigid CSS. Describe the visual result and let Claude design freely. This produces prettier, more unique results.

**5. Adapt to context** — if the skill works for different sectors/types (like a web generator for restaurant vs gym), include an adaptation guide.

**6. Conversational flow** — skill should feel like natural conversation, not a form. Group questions into 2-3 blocks, not long interrogations.

**7. Friendly fallbacks** — if something fails (scraping, installation), don't get stuck. Offer alternative and move forward.

**8. Welcome message** — if the skill goes in a kit with CLAUDE.md, include a welcome message that activates on any user input.

**9. No suggested pricing** — don't include "as a service" pricing or cost suggestions at the end of output.

**10. Clear results** — at the end, show what was generated, what data was used, what's missing, and ask if they want to adjust.

**11. Fixed models, only API key configurable** — projects using AI shouldn't expose model choice to end users. Lock the best model for each task (e.g., Sonnet for chat, Opus for analysis). Only the API key should be configurable. Prevents config errors, ensures consistent quality.

**12. Progressive conversation tone** — if the skill creates a conversational agent (interviewer, assistant, interactive form), start warm and empathetic. Only become more direct or demanding if patterns justify it (evasive answers, inconsistencies, etc.). Never start cold or curt.

**13. Human name for agents** — if the skill creates a conversational agent, give it a rotating human name per session. Humanizes interaction and feels like a team.

### Validation Checkpoints in Step 2

After designing, identify 2-3 critical validation points where user MUST approve before you continue:

**Approval order:**
1. **Scope first** — What's in? What's out? What's the boundary?
2. **Flow second** — What are the steps? In what order?
3. **Tools third** — What exactly does it generate? In what format?

**Goal:** If the user corrects you on 3+ points after this step, the design was incomplete. Early validation saves rework.

---

## Step 3 — Write the Skill

Generate the `.md` file with this structure:

```markdown
---
name: name-in-kebab-case
description: "Complete description of what it does and when to activate it. Include 5-8 different trigger phrases. Be specific but cover synonyms and different ways to ask for the same thing."
---

# Skill Name

One line describing what it does in simple language.

**Core Rule: [the most important rule for this skill]**

---

## Step 1 — [Gather info / Understand needs]

[Conversational flow to get necessary data]
[What to try fetching automatically first]
[What to ask if missing]

---

## Step 2 — [Process / Analyze / Research]

[The main logic of the skill]
[Which tools to use and how]
[Context-based adaptation if needed]

---

## Step 3 — [Generate output]

[What format the output has]
[Structure of the content]
[Creative freedom in design if HTML]

---

## Step 4 — [Save and present]

[How to name the file]
[Open it automatically if HTML]
[Summary of what was generated]
[Ask if they want to adjust]
```

### Rules for Generated Files

**Frontmatter:**
- `name` in kebab-case, no spaces or capitals
- `description` with at least 5-8 different trigger phrases
- Description should cover synonyms and variations

**Instructions:**
- Written in imperative form (do, ask, generate)
- Self-sufficient — work without programming knowledge
- If needing dependencies, include exact install command
- If needing APIs, explain how to get the key

**Tools:**
- Prefer native Claude Code tools (Read, Write, WebFetch, WebSearch, Bash)
- Avoid external dependencies when possible
- If Python/Node needed, keep it minimal with auto-install

---

## Step 4 — Install the Skill

After generating, install automatically:

```bash
mkdir -p .claude/skills
cp [skill-name].md .claude/skills/
```

If you want the skill available in ALL your projects (not just this folder):

```bash
mkdir -p ~/.claude/skills
cp [skill-name].md ~/.claude/skills/
```

---

## Step 5 — Create a Kit (if Sharing)

If others will use the skill, generate a complete kit:

```
kit-[name]/
├── CLAUDE.md                    ← Welcome message + what it does
├── INSTRUCTIONS.md              ← Step-by-step installation & usage
├── .claude/
│   └── skills/
│       └── [skill].md           ← The skill
└── [extra folders if needed]    ← assets/, templates/, etc.
```

**CLAUDE.md** should include:
- "On startup behavior" section with welcome message
- What the skill does
- What it needs from the user
- That nothing else needs installing (if true)

**INSTRUCTIONS.md** should include:
- Requirements (Claude Code + what else)
- Numbered steps from opening folder to seeing results
- File structure

---

## Step 6 — Test

After installing:

1. Pretend you're a new user and write a phrase that should trigger the skill
2. Verify instructions are clear and complete
3. If it generates files, verify they work
4. Adjust if something doesn't flow well

---

## Step 7 — Present to User

Show:
1. Name and path of generated file
2. Phrases that activate it
3. What input it needs and what output it generates
4. If a kit was created, list its files
5. Instructions for using it
6. Ask if they want to adjust anything

Don't show suggested pricing or sales advice.

---

## Reference & Best Practices

For detailed guidance on skill structure, patterns, and advanced topics, see docs in the main repo. Topics include:

- Self-contained skill structure
- Progressive disclosure patterns
- Workflow organization
- Naming conventions
- Common patterns from working skills
- And more...
