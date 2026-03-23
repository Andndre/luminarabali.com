---
description: "Use when editing invitation/template editor React code, Zustand store, drag-drop behavior, and template section persistence logic."
name: "Editor React Guardrails"
applyTo: "resources/js/editor/**/*"
---

# Editor React Guardrails

## Scope

- This instruction applies to the React template editor under resources/js/editor.
- Keep editor changes incremental and compatible with current admin API contracts.

## Data Integrity Rules

- Preserve section tree integrity:
    - parent_id must remain valid.
    - order_index must stay consistent after add, delete, duplicate, and move operations.
- Keep both representations in sync:
    - nested sections for UI.
    - flattened allSections (or equivalent) for persistence.

## Contract Rules

- Keep section_type values aligned across all layers:
    - TypeScript types and schemas.
    - Backend persistence/serialization.
    - Blade component views in resources/views/templates/components.
- For admin write requests, include CSRF header X-CSRF-TOKEN.

## UX and Stability

- Do not regress drag-and-drop behavior, selection state, and save/publish actions.
- Avoid breaking temporary id to persisted id mapping during save operations.

## Verification Checklist

- Run build checks after editor changes:
    - npm run build
- For integration-sensitive edits, verify template load/save/publish flow from admin UI.
