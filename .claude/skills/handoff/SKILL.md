---
name: handoff
description: Transfer context between agent sessions. Choose immediate (clipboard) or park (session file) mode. If no active session is found, prompt the user to specify one or create a new session.
---

# Handoff Skill

Transfer work context between agent sessions without losing progress.

## Workflow Routing

Choose the mode that fits your workflow:

| Command | Mode |
|---|---|
| `/handoff` | Clipboard: build handoff, copy to clipboard, paste in next session |
| `/handoff park` | Park: update session Progress only, resume later by reopening the session |

## Progress Entry Format

**Immediate** handoffs write minimal session entry - the handoff prompt carries full detail:
```markdown
### YYYY-MM-DD (handoff)
Exported PNGs, created agenda, published to wiki. Stopped at: Discord not posted. See handoff for full context.
```

**Park** handoffs write the full entry to session Progress - the session file IS the handoff:
```markdown
### YYYY-MM-DD (parked)
**Done:** What was accomplished. Concrete, not vague.
**Learned:**
- Gotchas, constraints, decisions discovered during work
**Stopped at:** Exactly where work paused.
**Next:**
1. First thing to do when resuming
2. Second action
**Files:**
- `path/to/file.md` - what it is
```

**Why the split:** Long-running sessions accumulate many Progress entries. Each full entry adds ~30 lines. After 6-8 handoffs the session file grows too large. Immediate handoffs transfer detail via clipboard, keeping the session file lean. Park handoffs are for work that might not resume soon - the session file is the persistent record.

## Key Rules

Follow these steps in order:
1. **Update docs first** - the vault/project is the source of truth, not the clipboard
2. **User's direction > agent's analysis** - if user said what to focus on next, that's the priority. If the user's direction is incomplete, supplement with agent's analysis but prioritize user input when clear.
3. **Progress = handoff data** - same structure everywhere, canonical location
4. **Scan for pending markers before Next Steps** - grep for `USER-COMMENT`, `NEEDS USER INPUT`, `TODO`, `FIXME` in the active session's files. Every marker found must appear explicitly in the handoff's Next Steps with where it lives, what's being asked, and who must act

## Platform Notes

- **Windows:** Use `clip` instead of `pbcopy` for clipboard: `cat << 'EOF' | clip`
- **Mac:** Use `pbcopy` (as shown in examples below)
- **Linux:** Use `xclip -selection clipboard`

## When No Session File Exists

If there is no `status: in-progress` session file, skip steps 1–2 of the Immediate Handoff Workflow and build the handoff directly from conversation context. Do not block or ask the user to create a session — just proceed with what's known.

## Immediate Handoff Workflow

Copy work context to clipboard for pasting into a new session.

### Steps

1. **Find the active session** - Check for `status: in-progress` session worked on in this conversation. If user mentioned one, use that.

2. **Write minimal progress entry** - The handoff prompt has the full detail:
   ```markdown
   ### YYYY-MM-DD (handoff)
   Exported excalidraw PNGs, created WS04 agenda, published to wiki. Stopped at: Discord announcement not posted. See handoff for full context.
   ```
   One sentence for done, one for stopped at. That's it. No Learned/Next/Files - those live in the handoff prompt.

3. **Capture user's direction** - If user gave specific intention for next session, put that first in Next. User's words > agent's analysis.

4. **Build handoff from session** - Combine session header + this conversation's work into the format below.

5. **Write to clipboard:**
   ```bash
   cat << 'EOF' | pbcopy
   ## Continue: [brief title]

   **IMPORTANT: Before executing anything, present:**
   1. What you understand was done
   2. Your proposed next steps
   3. Wait for approval before acting

   ---

   ### Active Session
   - `Notes/Sessions/YYYY-MM-DD-HHMM-Title.md` (read this first - Progress section is up to date)

   ### Context
   [From session Goal + Context. 2-3 sentences max.]

   ### Key Learnings
   [From Progress > Learned]

   ### Current State
   [From Progress > Done + Stopped at]

   ### Next Steps
   1. [User's stated priority, if given]
   2. [From Progress > Next]
   3. ...

   ### Relevant Files
   [From Progress > Files]
   EOF
   ```

6. **Confirm:**
   > Handoff ready. Paste into new session with Cmd+V.

### Clipboard Format Rules

- **No prior context assumed** - receiving agent knows nothing
- **Files by path** - just give paths, not @ mentions
- **MUST include the "wait for approval" instruction** - new agent should NOT jump into execution
- **Keep it concise** - enough to continue, not a novel

## Park Workflow

Update session Progress only. No clipboard. The session file IS the handoff for later resumption.

### Why This Works

The session already has Goal, Context, Progress, Definition of Done. A separate handoff artifact duplicates this = two sources of truth = one goes stale. The Progress Entry Format gives the session the same bones as a clipboard handoff. Next time work resumes, the agent reads the session file and picks up from Progress.

### Steps

1. **Find the active session** - Check for `status: in-progress` session worked on in this conversation. If user mentioned one, use that.

2. **Update session Progress section** - Append a new entry using the Progress Entry Format above. Factual, not verbose.

3. **Capture user's direction** - If user gave specific intention for next session, put that first in Next. User's words > agent's analysis.

4. **Confirm:**
   > Session updated: `Notes/Sessions/YYYY-MM-DD-HHMM-Title.md`
   >
   > To resume: reopen the session when ready.

## Before Writing Next Steps: Scan for Pending Markers

**Always** grep the active session's content/plan/research files for pending-review markers BEFORE drafting the Next Steps list. Don't let them silently carry forward between sessions.

```bash
# Example: search active session files
grep -nH 'USER-COMMENT\|NEEDS USER INPUT\|TODO\|FIXME\|NEEDS CLARIFICATION' Notes/Sessions/*.md
```

> Note: grep may match markers inside skill documentation text itself. Only flag markers that appear in project files (code, notes, plans) — not inside `.claude/skills/` files.

Every marker found must appear explicitly in the handoff's Next Steps or Open Items section with:
- **Where** it lives (file:line or section name)
- **What** is being asked (one-line summary, from the marker text itself)
- **Who** must act (user approval needed? or agent should proceed?)
