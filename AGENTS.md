# AGENTS.md — extension-tao-item (taoItems)

> Shared pillars (standards, quality / `pr-ready-gate`, Make, commit/PR):
> [nextgen-stack `tao/AGENTS.md`](https://github.com/oat-sa/nextgen-stack/blob/main/tao/AGENTS.md)
> · local: [`../AGENTS.md`](../AGENTS.md).

## 01 — Project Context

**What / why:** `oat-sa/extension-tao-item` (id `taoItems`) owns the
format-agnostic **Item** abstraction: ontology, Items library CRUD, previewer
registry hooks, compile/export points, categories/REST.

**Not:** QTI Creator (`taoQtiItem`) or Assets (`taoMediaManager`).

**Key directories / stack / constraints:**

```text
manifest.php
actions/                 # Items, Preview, Import/Export, REST, structures.xml
models/classes/          # ItemsService, itemModel, …
views/js/controller/     # library UI + routes.js
views/js/previewer/
test/
```

Entrypoints: `/taoItems/Items/index`; `interface.itemModel.php`.

- Stack: PHP on tao-core/generis; FE AMD/Grunt; npm `@oat-sa/tao-items`; runner
  often `@oat-sa/tao-item-runner`.
- Versions from manifests/CI only.

**Docs:** [`README.md`](README.md). Shared docs / decision-log rules → parent AGENTS.

## 02 — Standards & Conventions

Package-only below. Family patterns, quality SoT, `pr-ready-gate`, polar-star →
**parent AGENTS**.

**Patterns / structure:**

- Extend formats via `itemModel` in format packages — do not paste QTI Creator here.

**Never do (this package):**

- QTI Creator widgets here; Assets/RM chrome forks; hand-edit loaders.
- Patch `taoQtiItem` without ownership. Engine bugs → `@oat-sa/tao-item-runner`.

**Ownership**

| Surface | Own? |
|---------|------|
| Items library CRUD | **Yes** |
| QTI Creator | **No** (`taoQtiItem`) |
| Item preview registry hooks | **Yes** |
| AMD `ui/*` | **Consume** |

## 03 — Build & Test Commands

Shared Make / CI / readiness / commit policy → **parent AGENTS**
([commit/PR policy](https://oat-sa.atlassian.net/wiki/x/_oXmqQ)).

**This package** (from Composer platform root):

```bash
./vendor/bin/phpunit -c phpunit.xml.dist taoItems/test
npx grunt eslint:extensionreport --extension=taoItems --force
npx grunt taobundle --extension=taoItems
```
