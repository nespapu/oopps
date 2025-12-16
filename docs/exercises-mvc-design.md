> ⚠️ This document is a work in progress.  
> It captures the current understanding of the legacy exercise flow and will be refined incrementally during the MVC migration.

# Exercises — Legacy Flow Analysis & MVC High-Level Design

## 1. Goal and non-goals

### Goal
Analyze the current legacy exercise flow end-to-end (configuration → load → run → results) and propose a high-level MVC architecture that can coexist with the legacy implementation during the migration.

### Non-goals
- Do not modify the legacy flow yet.
- No refactors, no route removals, no DB schema changes in this phase.
- Only analysis + design.

---

## 2. Legacy entry points inventory (what to locate)

> This section is a checklist to systematically find *all* entry points. Fill it with real paths/URLs as you discover them.

### 2.1 URLs / routes (Front Controller or direct scripts)
Search for:
- Links to exercise screens (anchor tags).
- `action="..."` in legacy forms.
- Redirects (`header("Location: ...")`, custom helpers, JS `window.location`).
- Any `require` / `include` of legacy exercise scripts.

**Expected kinds of entry points:**
- Exercise configuration screen (choose exercise + options).
- Exercise execution screen (do the exercise).
- Exercise submission endpoint (POST).
- Results screen (feedback, score, next/try-again).

**Legacy routes found**

- `GET /menu.php?USER_CONTEXT`
- `GET /simulacroExamen.php?USER_CONTEXT`
- `GET /titulo.php?USER_CONTEXT&TOPIC_CONTEXT&EXERCISE_CONFIGURATION`
- `GET /indice.php?USER_CONTEXT&TOPIC_CONTEXT&EXERCISE_CONFIGURATION`
- `GET /justificacion.php?USER_CONTEXT&TOPIC_CONTEXT&EXERCISE_CONFIGURATION`
- `GET /citas.php?USER_CONTEXT&TOPIC_CONTEXT&EXERCISE_CONFIGURATION`
- `GET /herramientas.php?USER_CONTEXT&TOPIC_CONTEXT&EXERCISE_CONFIGURATION`
- `GET /contextoEscolar.php?USER_CONTEXT&TOPIC_CONTEXT&EXERCISE_CONFIGURATION`
- `GET /contextoLaboral.php?USER_CONTEXT&TOPIC_CONTEXT&EXERCISE_CONFIGURATION`
- `GET /bibliografia.php?USER_CONTEXT&TOPIC_CONTEXT&EXERCISE_CONFIGURATION`
- `GET /webgrafia.php?USER_CONTEXT&TOPIC_CONTEXT&EXERCISE_CONFIGURATION`

Query bundles (common):

`USER_CONTEXT = nombre={user}&oposicion={oppositionId}`

`TOPIC_CONTEXT = tema={number}`

`EXERCISE_CONFIGURATION = dificultad={difficulty}{1|2|3|4}&numeracion={boolean}&apartado={boolean}&ciclos={boolean}&leyes={boolean}&modulos={boolean}&conceptoCita={boolean}&autorCita={boolean}&anyoCita={boolean}&cita={boolean}&numeracionCita={boolean}&apartadoCita={boolean}&herramienta={boolean}&descripcionHerramienta={boolean}&ensenyanza={boolean}&ciclosContexto={boolean}&modulosContexto={boolean}&conceptoContextoEscolar={boolean}&aplicacionContextoEscolar={boolean}&metodo={boolean}&campo={boolean}&profesional={boolean}&conceptoContextoLaboral={boolean}&aplicacionContextoLaboral={boolean}&beneficio={boolean}&autorLibro={boolean}&anyoLibro={boolean}&tituloLibro={boolean}&editorial={boolean}&nombreWeb={boolean}&url={boolean}`


### 2.2 Scripts / files
Search by keywords:
- `exercise`, `ejercicio`, `fill`, `gap`, `huecos`, `writing`, `texto`, `result`, `score`
- Also search DB/config keywords: `config`, `settings`, `difficulty`, `time`, `attempt`, `topic`, `tema`

**Files found**

- `/menu.php` -> *Mixed (controller + view)*. Exercises configuration common entry point
- `/titulo.php` -> *Mixed (controller + view)*. 1st screen Fill the blank exercise
- `/indice.php` -> *Mixed (controller + view)*. 2nd screen Fill the blank exercise
- `/justificacion.php` -> *Mixed (controller + view)*. 3rd screen Fill the blank exercise
- `/citas.php` -> *Mixed (controller + view)*. 4th screen Fill the blank exercise
- `/herramientas.php` -> *Mixed (controller + view)*. 5th screen Fill the blank exercise
- `/contextoEscolar.php` -> *Mixed (controller + view)*. 6th screen Fill the blank exercise
- `/contextoLaboral.php` -> *Mixed (controller + view)*. 7th screen Fill the blank exercise
- `/bibliografia.php` -> *Mixed (controller + view)*. 8th screen Fill the blank exercise
- `/webgrafia.php` -> *Mixed (controller + view)*. 9th screen Fill the blank exercise
- `/php/definirCamposOcultos.php` -> *Controller-ish (state/flow support)*. Read exercise configuration coming as parameters and create hidden inputs to store them. This inputs will be used to recreate the parameters passed again to next screen.
- `/php/db.php` -> *Infrastructure*. Open a DB connection to be used in the screen scripts to retrieve data.
- `/php/utils.php` -> *Domain (Model) [text masking / difficulty]*. Script used in Fill the blank exercise that contains methods to hide word characters or text words based on the difficulty chosen by the user.
- `/php/obtenerNombreTema.php` -> *Domain (Model)*. Script to retrieve topic title based on the script parameters {orden} and {oposicionId}.   
- `/js/misScripts.js` -> *Controller-ish (navigation + evaluation) + View helpers*. JS file containing navigation functions to move from exercise screens, retrieve values from screen inputs to create URLs, generate random values to pick up topics, functions to evaluate the user inputs in a screen, to restart the inputs and to show up the solution to the user.
- `/js/string-similarity.min.js` -> *Third party library*. It contains methods to compare the similarity between two texts. They will be used to evaluate whether user input is correct or not.
- `/css/miEstilo.css` -> *View asset (presentation)*. CSS file containing classes used to highlight inputs (right|wrong) after evaluating an exercise.

### 2.3 Forms
For each form that affects exercises, record:
- View file where the form lives
- `action` target
- HTTP method
- Inputs (names + meaning)
- Hidden fields that act as “exercise id / type / config”

**Forms found**
- [ ] TODO

---

## 3. Legacy client-side controller (`misScripts.js`) — responsibilities and flow

`/js/misScripts.js` acts as a client-side orchestration layer for legacy exercises. It concentrates responsibilities that, in an MVC design, would be split across Controllers (routing/flow), Domain Services (evaluation rules), and View/UI helpers. :contentReference[oaicite:1]{index=1}

### 3.1 Main responsibilities (current)
- Navigation between legacy screens by building long query strings and redirecting via `document.location.href`. :contentReference[oaicite:2]{index=2}
- Reading exercise configuration from DOM inputs (both normal inputs and hidden inputs) and propagating it across screens (state carried in URL parameters). :contentReference[oaicite:3]{index=3}
- Random topic selection when the user chooses a special option (topic=0 → pick random). :contentReference[oaicite:4]{index=4}
- In-screen evaluation logic:
  - Equality-based checks (`modo = "igualdad"`).
  - Similarity-based checks (`modo = "similitud"`) using `string-similarity`. :contentReference[oaicite:5]{index=5}
- UI feedback helpers:
  - Mark inputs as correct/incorrect via CSS classes.
  - Reset inputs and clear visible solutions.
  - Render “show solution” blocks by injecting DOM elements. :contentReference[oaicite:6]{index=6}
- Gating progression: enable/disable “Continue” button based on correctness. :contentReference[oaicite:7]{index=7}

### 3.2 Navigation and state propagation
The function `irPantallaEjercicioCuantoSabesTema(pantalla, from)` reads a large set of configuration flags (many booleans) from DOM inputs and constructs the next screen URL with all values appended as query parameters. This is the primary legacy mechanism to keep exercise state between screens. :contentReference[oaicite:8]{index=8}

### 3.3 Evaluation model used in legacy
Evaluation is performed in the browser by comparing user inputs with hidden “solution” inputs contained in each question container:
- Equality comparison: case-insensitive string equality.
- Similarity comparison: uses a threshold (currently 0.7) for approximate matching. :contentReference[oaicite:9]{index=9}

### 3.4 Implications for the MVC migration
- The legacy system transports configuration through query string + hidden inputs; the MVC design should replace this with an `attemptId` / `ExerciseSession` persisted server-side to avoid huge URLs and duplicated state logic.
- Evaluation logic currently lives in JS; the MVC design should progressively move evaluation to server-side domain services (keeping client-side feedback only as UI enhancement during transition).
- `misScripts.js` will likely be split into:
  - (Controller) request/flow orchestration (server-side)
  - (Service) evaluators (server-side)
  - (View) minimal JS for UX (client-side)

---

## 4. Exercise types in legacy (identify and categorize)

> The objective is to list “exercise families” and their key invariants. The MVC design will use these families.

### 4.1 Candidate exercise families (typical in OOPPS)
- **Free writing (escritura libre)**
  - User writes an answer in a textarea.
  - Evaluation: manual/assisted (keyword match?), or simply stored as attempt.
  - Result: show expected solution + user text + metrics (time, length).

- **Fill in the blanks (completar huecos)**
  - Prompt with gaps.
  - Evaluation: compare tokens/answers.
  - Result: highlight correct/incorrect blanks.

- **Quiz / short answer / flashcards (if exists)**
  - Evaluation: exact match / normalization.

- **Timed practice (if exists)**
  - Same exercise types but with time constraint, storing elapsed time.

### 4.2 Legacy exercise type mapping
For each legacy type:
- Type ID/name used in legacy (string, numeric id, file name)
- Data source (DB/table, JSON, PHP arrays)
- Evaluation rule (exact match, normalized, manual)
- Result format

**Types found**
- [ ] TODO: list here

---

## 5. Legacy flow map (current behavior)

> Fill the real steps as you trace the code. This is the “truth table” of how the app currently works.

### 5.1 Flow: configuration → load → execution → results

#### Step A — Configuration
- Entry: configuration page URL (GET)
- Inputs: selected topic/exercise, difficulty, time, mode, etc.
- Output: a “resolved config” (often stored in session or passed via query/post)

**Notes (legacy):**
- [ ] TODO

#### Step B — Load data
- Data source: DB / files / hardcoded arrays
- What is loaded:
  - Exercise metadata (title, instructions, type)
  - Prompt content (text + blanks, statement)
  - Solution data (expected text, answers)
- Where it happens:
  - In the same script as the view?
  - In an included file?
  - In a “model-like” function?

**Notes (legacy):**
- [ ] TODO

#### Step C — Execution
- Entry: execution page URL (GET)
- Renders the exercise view
- Common dependencies:
  - Session (user, exercise state)
  - Timer logic in JS or server
  - Anti-refresh / attempt id

**Notes (legacy):**
- [ ] TODO

#### Step D — Submission
- Entry: submission URL (POST)
- Takes user answers and “evaluates”
- Stores attempt:
  - DB insert/update OR session only
  - score, correct items, time, raw user input

**Notes (legacy):**
- [ ] TODO

#### Step E — Results
- Entry: results page URL (GET) OR same POST renders results
- Shows:
  - Score / correctness breakdown
  - Expected vs user
  - Retry / next links

**Notes (legacy):**
- [ ] TODO

---

## 6. Responsibility audit (what is Model vs Controller vs View today)

> This is where we detect “controllers camuflados”.

### 6.1 Heuristics
- **Model (domain logic):**
  - Exercise evaluation algorithms
  - Answer normalization
  - Score computation
  - Attempt persistence logic (conceptually)
  - Data loading that is not presentation-specific

- **Controller (application flow):**
  - Reads request (GET/POST)
  - Validates input
  - Chooses exercise type handler
  - Orchestrates load → evaluate → persist
  - Decides redirect vs render (PRG)

- **View (presentation):**
  - HTML templates
  - Minimal conditional display logic
  - No DB calls, no evaluation, no request branching

### 6.2 “Camouflaged controllers” patterns to look for
- `if ($_POST ...) { ... } else { ... }` inside a view file
- DB queries inside templates
- `header("Location: ...")` in include files
- Mixed rendering + evaluation in one script

### 6.3 Findings (to fill)
- [ ] TODO: list each legacy file and classify:
  - Mostly Model / Controller / View / Mixed
  - What must be extracted first during migration

---

## 7. Proposed MVC high-level architecture (target design)

### 7.1 Key principles
- **Typed exercise domain:** each exercise type has:
  - A configuration object
  - A data loader
  - A renderer (view)
  - An evaluator (domain service)
- **PRG pattern:** POST evaluates and redirects to GET results to avoid resubmission.
- **Compatibility during migration:** legacy remains accessible; MVC can wrap/bridge legacy data sources.

---

## 8. Domain model (entities and services)

### 8.1 Core entities (models)
- `Exercise`
  - `id`, `type`, `title`, `instructions`, `topicId`, `metadata`

- `ExerciseConfig`
  - `exerciseId`, `options` (difficulty, timeLimit, mode, etc.)
  - Derived config from user selection + defaults

- `ExerciseSession` (or `AttemptContext`)
  - `attemptId`, `userId`, `exerciseId`, `config`, `startedAt`, `expiresAt` (optional)

- `ExerciseAttempt`
  - `attemptId`, `userId`, `exerciseId`, `submittedAt`, `answersRaw`, `elapsedSeconds`
  - `resultSummary` (score, correctCount, etc.)

- `ExerciseResult`
  - `score`, `maxScore`, `details` (type-specific breakdown)
  - `feedback` (strings or structured)

### 8.2 Repositories (infrastructure)
- `ExerciseRepository`
  - `findById(exerciseId)`
  - `findByTopic(topicId)`
  - `loadPayload(exerciseId)` (prompt + solution data)

- `AttemptRepository`
  - `create(attempt)`
  - `update(attempt)`
  - `findById(attemptId)`

### 8.3 Domain services
- `ExerciseEvaluator` (interface)
  - `evaluate(payload, userSubmission, config): ExerciseResult`

Concrete evaluators:
- `FreeWritingEvaluator`
- `FillInTheBlanksEvaluator`
- (others as needed)

### 8.4 Legacy adapters (temporary)
- `LegacyExerciseDataSource`
  - Reads data exactly like legacy (DB schema, file layout, etc.)
  - Returns normalized payload for MVC domain
- `LegacyAttemptStorage` (optional)
  - If legacy stores attempts in a specific table/format, wrap it

---

## 9. Controllers (by use-case)

> We avoid a “mega controller” by separating concerns, but keep it pragmatic.

### 9.1 Controllers
- `ExerciseConfigController`
  - GET config form
  - POST config selection → creates `ExerciseSession` / `attemptId` → redirect to run

- `ExerciseRunController`
  - GET run page for a given attemptId
  - Loads payload via repository + adapter
  - Renders the correct view based on type

- `ExerciseSubmitController`
  - POST submission for attemptId
  - Validates
  - Evaluates using type evaluator
  - Persists attempt+result
  - Redirects to results

- `ExerciseResultController`
  - GET results page for attemptId
  - Loads stored result and renders

---

## 10. Views (by screen)

- `views/exercises/config/index.php`
- `views/exercises/run/free-writing.php`
- `views/exercises/run/fill-in-the-blanks.php`
- `views/exercises/results/free-writing.php`
- `views/exercises/results/fill-in-the-blanks.php`

Rules:
- Views receive prepared DTO/arrays, not repositories.
- No evaluation logic in views.

---

## 11. Proposed routes (MVC)

> These are the planned routes. Keep them stable and predictable.

### 11.1 Configuration
- `GET  /exercises`  
  Shows exercise catalog / config entry point.

- `POST /exercises/config`  
  Receives selected exercise + options, creates attempt/session, redirects.

### 11.2 Run
- `GET  /exercises/{attemptId}`  
  Displays the exercise execution screen.

### 11.3 Submit (PRG)
- `POST /exercises/{attemptId}`  
  Submits answers, evaluates, stores, redirects to results.

### 11.4 Results
- `GET  /exercises/{attemptId}/results`  
  Shows results page.

---

## 12. Compatibility strategy (legacy + MVC coexistence)

### 12.1 Parallel routing
- Keep legacy URLs working as-is.
- Introduce new MVC routes under `/exercises/...` (new namespace).
- Provide links from dashboard to either legacy or MVC depending on a feature flag.

### 12.2 Feature flag
A single toggle that can be:
- environment variable (`EXERCISES_MVC_ENABLED=true/false`)
- config file entry

Behavior:
- If disabled → link to legacy.
- If enabled → link to MVC.

### 12.3 Data compatibility
- MVC repositories initially read the same DB tables / sources as legacy (read-only where possible).
- Attempts/results can be stored either:
  - In new tables (cleaner but DB change), OR
  - In existing legacy tables through an adapter (no schema change).

**This design doc does not mandate DB changes yet.**

---

## 13. Migration plan (high-level, no code changes in this phase)

1) Complete legacy inventory:
- entry points, files, exercise types, data sources.

2) Build “read-only” MVC slice:
- Implement `GET /exercises` and `GET /exercises/{attemptId}` using legacy data source adapter (no evaluation yet).

3) Add submit + results:
- Implement `POST /exercises/{attemptId}` + `GET /exercises/{attemptId}/results` with PRG.

4) Gradually migrate types:
- One evaluator + views at a time.
- Keep legacy for unmigrated types.

---

## 14. Appendix — What to record while analyzing legacy (template)

For each legacy exercise type:
- Name/type id:
- Entry URLs:
- Files involved:
- Data source (table/files):
- Payload structure:
- Submission inputs:
- Evaluation rules:
- Result format:
- Side effects (session/db):
- Problems detected (mixing concerns, duplication, etc.):

