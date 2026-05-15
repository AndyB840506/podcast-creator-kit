---
name: handoff
description: "Backup your Claude Projects to GitHub before closing or switching computers. One command: handoff. Triggers: 'handoff', 'backup to github', 'sync github', 'push backup', 'save to github', 'commit and push', 'back me up'"
---

# Handoff — Backup to GitHub

Push your latest Claude Projects to GitHub private repo. One command, automatic versioning.

**Regla fundamental: Always push the latest version. Fresh commit, fresh push, ready to resume on any machine.**

---

## How It Works

1. **Stage changes** — all files in Claude Projects
2. **Create commit** — timestamped snapshot of current state
3. **Push to GitHub** — sent to `github.com/AndyB840506/claude-projects`
4. **Confirm** — show what was backed up

---

## Step 1 — Stage and Commit

Navigate to your Claude Projects folder and commit all changes with a timestamp.

```powershell
cd "e:\Claude Project\Claude Projects"

# Stage all changes
git add -A

# Check what will be committed
git status

# Commit with timestamp
$timestamp = Get-Date -Format "yyyy-MM-dd HH:mm:ss"
git commit -m "Handoff $(Get-Date -Format 'yyyy-MM-dd HH:mm')"
```

**If there's nothing to commit:**
```
On branch main
nothing to commit, working tree clean
```
This is fine — your last handoff is still backed up.

**If there are changes:**
```
[main abc1234] Handoff 2026-05-14 23:45
 5 files changed, 123 insertions(+), 45 deletions(-)
```
Great — changes are committed.

---

## Step 2 — Push to GitHub

Send your commit to the remote repository.

```powershell
git push origin main
```

**First time?** You'll be prompted to authenticate with GitHub:
- Option 1: Browser popup (GitHub sign-in) — easiest
- Option 2: Personal access token (PAT) if browser doesn't work

**Success:**
```
To https://github.com/AndyB840506/claude-projects.git
   abc1234..def5678  main -> main
```

---

## Step 3 — Verify

Your backup is now on GitHub. Confirm:

```powershell
# Show latest commit pushed
git log --oneline -1

# Or open in browser: https://github.com/AndyB840506/claude-projects
```

---

## Recovery Instructions

To restore your Claude Projects on a new or different machine:

```powershell
cd "path\where\you\want\it"
git clone https://github.com/AndyB840506/claude-projects.git "Claude Projects"
cd "Claude Projects"
```

Your entire project history is restored — all skills, kits, config files, everything.

---

## Step 4 — Set Up Google Drive for Desktop (Continuous Auto-Sync)

While GitHub is your versioned backup (triggered by handoff), Google Drive for Desktop provides continuous automatic sync — so your local changes are always backed up to the cloud without thinking.

### Installation (one-time)

1. **Download** Google Drive for Desktop from [https://www.google.com/drive/download/](https://www.google.com/drive/download/)
2. **Install and sign in** with your Google account (berandre2@gmail.com)
3. **Choose mirror mode** in Settings:
   - "Mirror files" = keeps a local copy always synced + cloud copy
   - (Don't use "Streaming" — you want local copies available offline)
4. **Add this folder** to Google Drive sync:
   - In Google Drive for Desktop settings → add folder → select `e:\Claude Project`

### What happens

- Every time you save a file in `e:\Claude Project\Claude Projects\...`, it automatically syncs to Google Drive
- You get a web-accessible backup at https://drive.google.com
- Combined with `/handoff` git commits, you have two safety nets: versioned (git) + continuous (Drive)

### If you move the folder later

After Google Drive is syncing, if you move `Claude Projects` to a different location:
- Update the sync folder in Google Drive for Desktop settings
- No need to reconfigure — it just follows the new path

---

## Notes

- **No size limits** — GitHub allows private repos with unlimited size (up to GitHub's policies)
- **Automatic versioning** — each handoff creates a new commit you can revert to if needed
- **No manual uploads** — no base64, no cloud API struggles, just git
- **Works offline** — you can commit without internet, push when connected

---

## Step 5 — Optional: Review Skills Before Next Session

After pushing to GitHub, consider a quick skill audit before closing:

> "Your backup is pushed. Before the next session, want to run `/prompt-reviewer` on any skills you created or modified today? Catches clarity issues early."

Options:
- **"Yes"** → Run prompt-reviewer (RÁPIDO mode, 2-3 min) on the modified skills
- **"No"** → Close the session (skills are already backed up)
- **"Later"** → I'll remind you at the next `/handoff`

This is optional but recommended if you created new skills or made significant updates. A quick clarity check now prevents confusion later.
