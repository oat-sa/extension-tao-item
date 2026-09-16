# AGENTS.md — extension-tao-item (taoItems)

## Purpose

`oat-sa/extension-tao-item` (extension id `taoItems`) owns the format-agnostic **Item** abstraction: Item ontology, Items library CRUD UI, previewer registry hooks, compile/export extension points, and categories/REST surfaces.

It is **not** the QTI Creator or format-specific authoring — that lives in `oat-sa/extension-tao-itemqti` (`taoQtiItem`) behind `itemModel`. It is also **not** the Assets library (`taoMediaManager`).

## Shared platform agent rules

Common readiness / context-budget / Definition of Done / family anti-patterns /
verify-by-change-type conventions for TAO PHP extensions live in the installed
**`tao`** package (`oat-sa/tao-core`) `AGENTS.md`. Read that file when present
in the platform install.

This file covers **only** ownership and workflows specific to this package.
Do **not** require any external monorepo checkout or workstation-only note paths.


## Stack

Do **not** hardcode dependency or runtime versions in this file.

- PHP extension on `oat-sa/tao-core` + `oat-sa/generis` (+ backoffice as declared in `composer.json`)
- FE: RequireJS AMD + Grunt; npm package `@oat-sa/tao-items` under `views/` (pins in `views/package.json`)
- Item runner consumed via `@oat-sa/tao-item-runner` — engine bugs usually belong upstream
- Versions: read `composer.json` / `views/package.json` / CI workflows — never hardcode pins here

## Core Rules

- **Follow existing patterns first** in this package.
- **Prefer TDD** for behavior changes unless docs/config-only.
- **Prefer minimal, local changes.** No broad refactors unless requested.
- **Preserve license headers** — sibling-style **`GPL-2.0-only`** (see `composer.json`); do not auto-migrate to SPDX dual-license.
- **Update tests** when behavior changes.
- **Do not weaken** CI / lint / test / CodeRabbit gates.
- For shared agent discipline (context budget, DoD, family anti-patterns), follow the installed **`tao`** (`oat-sa/tao-core`) `AGENTS.md`.


## Structure

```text
manifest.php
actions/                 # Items, Preview, Import/Export, REST, structures.xml
models/classes/          # ItemsService, itemModel interface, …
models/ontology/
views/js/controller/     # library UI + routes.js
views/js/previewer/      # preview registry consumers
test/
```

Entrypoints: `manifest.php`; menu → Items library (`/taoItems/Items/index`); `models/classes/interface.itemModel.php` for format plugins.

## UI layer

| Surface | Own? | Where |
|---------|------|--------|
| Items library CRUD | **Yes** | `views/js/controller/items/*`, `actions/` |
| QTI Creator canvas | **No** | `taoQtiItem` |
| Item preview registry hooks | **Yes** | registry / preview adapters; format UIs often elsewhere |
| Shared AMD `ui/*` | **Consume** | `@oat-sa/tao-core-ui` via platform client config |

## Conventions

- Keep PHP `actions/` thin; domain logic in `models/classes/`.
- New/changed screens: sync `structures.xml`, `views/js/controller/routes.js`, and PHP action/template.
- Do **not** hand-edit `views/js/loader/*.min.js` — rebuild with Grunt.
- Extend Item behavior via `itemModel` implementations in format packages — do not paste QTI Creator into this repo.
- i18n: follow existing `__()` / locale patterns.

## Testing

- PHPUnit under `test/` (unit/integration as present).
- Run from the **installed TAO platform** root (`vendor/bin/phpunit` + platform `phpunit.xml.dist`), targeting this extension’s tests (typical path `taoItems/test/...` — confirm in your install).
- FE: nearest QUnit / grunt test for touched AMD when coverage exists.

Discover the **platform root** (Composer application with `vendor/bin/phpunit`) from the environment — do not assume a particular monorepo path.

## Commands

**Platform root** = Composer app with `vendor/`. **Package root** = this repository.

```bash
# from platform root (adjust extension path if mapped differently)
./vendor/bin/phpunit -c phpunit.xml.dist taoItems/test

# FE from platform tao/views/build (or local views/build if present)
npx grunt taobundle --extension=taoItems
npx grunt eslint:extensionreport --extension=taoItems --force
npx grunt taotest --extension=taoItems
```

## Hard rules / Constraints

- Do not implement QTI Creator widgets here — use `taoQtiItem`.
- Do not confuse Items library with Assets (`taoMediaManager`) or Resource Manager chrome (`tao-core-ui`).
- Item-runner engine fixes: prefer `@oat-sa/tao-item-runner` / owning package.
- Keep changes inside this package unless the task explicitly requires another repo.
- Never commit `.ai/` or `.cursor/`.

## Anti-patterns

- Hand-edit generated `loader/*.min.js`.
- Fork `ui/*` or Resource Manager into this package.
- Patch `taoQtiItem` “because Items is abstract” without ownership.
- Invent Composer/npm version pins.
- Weaken CI / skip CodeRabbit critical/major.

Also follow family anti-patterns in the installed **`tao`** (`oat-sa/tao-core`) `AGENTS.md`.

## Agent notes (`.ai/`)

Local, **gitignored** branch-scoped notes. Do **not** commit `.ai/`. Durable
rules stay in this file and in tao-core `AGENTS.md` for shared conventions.

Write a **polar-star** under `.ai/work/<slug>/` plus supporting docs; prefer
re-reading those files over chat-only memory.

```text
.ai/work/<branch-slug>/   # injective: `%`→`%25`, `_`→`%5F`, `/`→`_`
.ai/current                # symlink to active work dir
.ai/archive/*.tar.gz
```

Enable once per clone:

```bash
git config core.hooksPath .githooks
```

After `git branch -d` / prune: `scripts/ai-notes-gc.sh`  
Optional: `scripts/ai-notes-gc.sh --self-test`.


## Definition of Done

Satisfy **tao-core** Definition of Done / Readiness conventions when available, plus this package’s Hard rules. Minimal local checklist:

1. Package-specific AC / polar-star addressed.
2. Diff stays in this package unless the task requires otherwise.
3. TDD evidence for behavior changes (or docs/config-only exception).
4. License headers updated (`GPL-2.0-only` sibling style).
5. `.ai/` notes updated when decisions matter.
6. `pr-ready-gate` (or tao-core readiness fallback) passed with real command output.

## Skills ([oat-sa/skills](https://github.com/oat-sa/skills))

1. Search / load skills from **[oat-sa/skills](https://github.com/oat-sa/skills)** first.
2. Prefer reusing shared skills over inventing a parallel local skill.
3. Create a new skill only when nothing suitable exists.

**Must-have for implementation / PR prep:** [`pr-ready-gate`](https://github.com/oat-sa/skills/tree/feat/pr-ready-gate/pr-ready-gate)
(branch pin while testing). If the skill cannot be loaded, use the same criteria
as **`tao` / tao-core AGENTS Readiness gate**: tests + lint on touched scope +
local CodeRabbit with **zero critical / zero major**.

## Readiness gate (before “done” / before opening a PR)

Prefer skill `pr-ready-gate`. Fallback: follow **tao-core** `AGENTS.md` Readiness
gate / Definition of Done, plus this package’s Hard rules. Report real command
results. Docs / hooks / `AGENTS.md`-only changes: `bash -n` on touched shell +
CodeRabbit on the diff; skip irrelevant suites explicitly.


## Pointers

- `README.md` — package overview
- `composer.json` / `LICENSE` — license and Composer deps
- `views/package.json` — FE pins (if present)
- Installed **`tao`** package `AGENTS.md` (`oat-sa/tao-core`) — shared agent conventions
- [oat-sa/skills](https://github.com/oat-sa/skills) — shared skills; [`pr-ready-gate`](https://github.com/oat-sa/skills/tree/feat/pr-ready-gate/pr-ready-gate) (branch pin while testing)
- `.coderabbit.yaml` → remote `oat-sa/tao-code-quality` `coderabbit/php/authoring/v1`
- `.github/workflows/*` — PR CI
- `.githooks/post-checkout` + `scripts/ai-notes-gc.sh` — local `.ai/` lifecycle

## Default Agent Behavior

1. Read this file, then `.ai/current` / polar-star for the branch.
2. Read installed **`tao`** (`oat-sa/tao-core`) `AGENTS.md` for shared gates when available.
3. Check [oat-sa/skills](https://github.com/oat-sa/skills) before inventing procedures; use `pr-ready-gate` for implementation/PR prep.
4. Prefer TDD; keep diffs minimal and inside this package.
5. Respect UI/ownership tables above; avoid Anti-patterns.
6. Update `.ai/` as decisions land; verify with platform-root commands; do not weaken CI.
