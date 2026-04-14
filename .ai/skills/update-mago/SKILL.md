---
name: update-mago
description: Update mago version, summarize changelog and configure new options interactively
---

# Context

Mago is a static analysis and linting tool for PHP projects.
It is configured via `mago.toml` at the project root.
Each configuration entry enables or disables a specific fixer or linter rule.
Rules detect problematic PHP patterns and can auto-fix them.
Source: https://github.com/carthage-software/mago

# Step 1 — Check versions

1. Read the current version in `.infra/docker/php.Dockerfile`.
2. Fetch the latest release:
   ```
   curl -s 'https://api.github.com/repos/carthage-software/mago/releases/latest' -H 'User-Agent: Agent Skill' | jq -r '.tag_name'
   ```
3. If the latest version equals the current version: **stop and report "Already up to date"**.
4. Otherwise: update the version string in `php.Dockerfile` AND in `mago.toml` (`version` key), then continue.

---

# Step 2 — Collect changelogs

Fetch the last 10 releases in one call:
```
curl -s 'https://api.github.com/repos/carthage-software/mago/releases?per_page=10' -H 'User-Agent: Agent Skill' | jq -r '.[] | {version: .tag_name, body: .body}'
```

Filter to keep **only versions strictly greater than the previous version and up to the new version**.
If some intermediate versions are missing from the first page, paginate with `&page=2`, etc., until you have them all — but **do not re-fetch pages you already have**.

Then produce a numbered inventory:

```
Found N new configurations across versions X.Y.Z → A.B.C:
1. <config_name> (introduced in vX.Y.Z)
2. <config_name> (introduced in vX.Y.Z)
…
```

**Do not skip any configuration.** If a release body mentions a new `[section]` or a new key, it counts.

---

# Step 3 — Interactive configuration (one by one)

Process each configuration from the numbered list **one at a time**, in order.

## Loop pattern — follow this exactly for configuration #N:

### 3.1 — Present the configuration

Output the description using **exactly this structure** (all four sections are mandatory — never omit one):

---

## `configuration_name` (N / TOTAL)

### What it does

One or two sentences explaining the purpose and effect of this configuration.

### Options

| Option | Allowed values | Default | Description |
|--------|---------------|---------|-------------|
| `option_name` | `value1`, `value2` | `value1` | What it controls |

### Code example

```php
// ❌ Before (mago would flag this)
$example = "bad code here";

// ✅ After (what mago produces or expects)
$example = "good code here";
```

### Current status in mago.toml

Either: `Not present — will be added if enabled`
Or: `Already present as: [key = "value"]`

---

### 3.2 — Ask for the user's decision (MANDATORY — use the UI ask tool, never plain text)

Ask exactly this question with these options:
- **Enable** — add the configuration with recommended defaults
- **Enable with custom value** — add it, then ask for the value
- **Disable / skip** — add it with disabled value

### 3.3 — Apply the decision

- If "Enable": append the appropriate block to `mago.toml`, show the diff.
- If "Enable with custom value": ask for the value, then append, show the diff.
- If "Skip": write nothing, confirm "Skipped."

### 3.4 — Advance the loop

**After applying the decision and confirming: proceed to configuration #N+1 automatically.**
Do not summarize all remaining configurations. Do not batch ahead.
When N = TOTAL: output the final summary (see Step 4).

---

# Step 4 — Final summary

Once all configurations have been processed:

```
Update complete: vOLD → vNEW

Enabled (X):
- config_a
- config_b

Skipped (Y):
- config_c
```
