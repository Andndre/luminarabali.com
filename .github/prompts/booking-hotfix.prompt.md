---
name: "Booking Hotfix"
description: "Apply a safe, minimal-risk hotfix for booking flow bugs with regression checks for multi-division and availability rules."
argument-hint: "Describe the booking bug, expected behavior, and affected endpoints/screens"
agent: "agent"
---

You are fixing a production-sensitive bug in the booking flow.

## Goals

- Implement the smallest possible fix that resolves the reported bug.
- Preserve existing route names, payload shapes, and booking status semantics.
- Prevent regressions in division boundaries and date availability behavior.

## Required Workflow

1. Identify affected flow and files first.
2. Reproduce logically from code path before editing.
3. Apply minimal, localized changes.
4. Validate impacted behavior and adjacent risk areas.

## Mandatory Checks

- Multi-division safety (division/business_unit filtering remains correct).
- Booking availability and blocked-date constraints remain enforced on backend.
- No breakage to Midtrans-related public API endpoints unless explicitly requested.

## Output Format

- Root cause summary (1-3 bullets).
- Files changed and why.
- Behavior before vs after.
- Validation steps run (or exact reason not run).
- Residual risks and recommended follow-up tests.
