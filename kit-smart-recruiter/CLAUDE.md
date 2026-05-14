# Smart Recruiter — AI Screening Interviewer

This project runs AI-powered candidate screening interviews.

## How it works

**Two modes — detected automatically:**

1. **Recruiter Mode** — if `job-config.json` does NOT exist  
   Configure the position: requirements, dealbreakers, culture fit, compensation range, language.

2. **Candidate Mode** — if `job-config.json` EXISTS  
   Claude conducts the full screening interview and generates a fit report.

## On startup

When the user types anything:
- Check for `job-config.json`
- If missing → run the recruiter setup flow
- If present → start the candidate interview

Always use the `smart-recruiter` skill.

## Output files

- `job-config.json` — position configuration (created by recruiter)
- `report-[name]-[YYYY-MM-DD].md` — fit report (created after each interview)
