# Fill the Blanks — Migration Guide

> This document is a lightweight implementation guide for the MVC migration of the "Fill the Blanks" exercise.
>
> Unlike `exercises-mvc-design.md`, which describes the overall architecture, this guide focuses on day-to-day development, implementation patterns, naming conventions, and migration progress.

---

# 1. Goal

The goal of this migration is to move the legacy "Fill the Blanks" exercise from a mixed PHP/JavaScript implementation to the MVC architecture described in `exercises-mvc-design.md`.

Each wizard screen must follow the same implementation pattern and architectural conventions.

---

# 2. Current Status

## Completed

* [x] Exercise configuration
* [x] Exercise session creation
* [x] Title step

## Pending

* [ ] Index
* [ ] Justification
* [ ] Quotes
* [ ] Tools
* [ ] School Context
* [ ] Work Context
* [ ] Bibliography
* [ ] Webography
* [ ] Summary

---

# 3. Wizard Architecture

The MVC flow for every wizard step follows the same structure.

```text
Exercise Configuration
        ↓
ExerciseSession
        ↓
StepBuilder
        ↓
StepPayload
        ↓
View
        ↓
StepAnswer
        ↓
Evaluator
        ↓
StepEvaluation
        ↓
ExerciseSession
```

Responsibilities:

* StepBuilder builds data required by the view.
* View renders inputs and feedback.
* StepAnswer captures user input.
* Evaluator validates answers.
* StepEvaluation stores correction results.
* ExerciseSession persists progress between steps.

---

# 4. Standard Migration Workflow

Every wizard screen should be implemented using the following sequence.

## 1. Review Legacy Implementation

Understand:

* Data source
* Rendering logic
* Evaluation rules
* Configuration flags
* Navigation behavior

---

## 2. Design StepPayload

Define the data required by the view.

Questions:

* What information must be displayed?
* Which fields are evaluable?
* Which hints are required?

---

## 3. Design StepAnswer

Define the structure used to receive user answers.

Questions:

* Which values are submitted?
* How will nested structures be represented?

---

## 4. Implement StepBuilder

Responsibilities:

* Load domain data
* Apply configuration flags
* Generate hints
* Build payload

The builder must not perform evaluation.

---

## 5. Implement Evaluator

Responsibilities:

* Compare answers with expected values
* Create field results
* Create step evaluation

Evaluation strategies:

* EqualityEvaluator
* SimilarityEvaluator

---

## 6. Implement View

Responsibilities:

* Render payload
* Render evaluation feedback
* Render navigation actions

Views must remain free from business logic.

---

## 7. Connect Routing

Add required routes.

Typical routes:

```text
GET  /steps/{step}
POST /steps/{step}/evaluate
```

---

## 8. Persist Evaluation

Store evaluation results inside ExerciseSession.

Example:

```text
evaluationByStep[ExerciseStep::INDEX]
```

---

## 9. Add Tests

Minimum tests:

### Builder

* Builds payload correctly

### Evaluator

* Correct answer
* Incorrect answer

---

## 10. Execute Smoke Test

Manual verification:

* Screen loads correctly
* Evaluation works
* Navigation works
* Session state is preserved

---

## 11. Refactor

Before committing:

* Remove duplication
* Improve naming
* Extract helper methods when necessary
* Remove dead code

---

# 5. Naming Conventions

> Note
>
> Payload DTO classes and StepAnswer DTO classes listed below are part of the target MVC design and are not implemented yet.
>
> Current implementations use associative arrays for both payloads and user answers.
>
> The naming conventions below document the expected future structure and should be used as guidance when implementing new wizard steps.


## Payloads

```text
TitleStepPayload
IndexStepPayload
JustificationStepPayload
QuotesStepPayload
ToolsStepPayload
SchoolContextStepPayload
WorkContextStepPayload
BibliographyStepPayload
WebographyStepPayload
```

---

## Answers

```text
TitleStepAnswer
IndexStepAnswer
JustificationStepAnswer
QuotesStepAnswer
ToolsStepAnswer
SchoolContextStepAnswer
WorkContextStepAnswer
BibliographyStepAnswer
WebographyStepAnswer
```

---

## Builders

```text
TitlePayloadBuilder
IndexPayloadBuilder
JustificationPayloadBuilder
QuotesPayloadBuilder
ToolsPayloadBuilder
SchoolContextPayloadBuilder
WorkContextPayloadBuilder
BibliographyPayloadBuilder
WebographyPayloadBuilder
```

---

## Views

```text
title.php
index.php
justification.php
quotes.php
tools.php
school-context.php
work-context.php
bibliography.php
webography.php
```

---

# 6. Answer Key Convention

Use stable keys for answer mapping.

Examples:

```text
item0.title
item0.order
item1.title
```

Nested structures:

```text
cycle0.name
cycle0.law0.name
cycle0.law1.name
cycle0.module0.name
```

The convention must remain consistent across all steps.

---

# 7. Migration Log

## YYYY-MM-DD

* Completed:
* Commit:
* Notes:

---

# 8. Next Screen

Current target:

```text
Index
```

Required components:

* IndexStepPayload
* IndexStepAnswer
* IndexPayloadBuilder
* Evaluator
* View
* Routing
* Session persistence
* Tests
* Smoke test

The objective is to complete one screen at a time and keep the wizard functional after every migration step.
