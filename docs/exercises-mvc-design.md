> ⚠️ This document is a work in progress.  
> It captures the current understanding of the legacy exercise flow and will be refined incrementally during the MVC migration.

# Exercises — Legacy Flow Analysis & MVC High-Level Design

## How to read this document
- Sections 2–5: legacy analysis (what exists)
- Sections 6–7: target MVC design (what we want)
- Sections 7.10+: migration plan (how to get there)

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

- Form: "Simulacro examen teórico" wizard — step: Configuración
  - Form tag: none (JS-driven UI)
  - View: /menu.php
  - Action: N/A (JS-driven navigation)
  - Method: N/A (no HTTP submit)
  - Trigger: onclick -> irPantallaEjercicioSimulacro()
  - Hidden fields (context):
    - nombre: {user}
    - oposicion: {oppositionId}
  - Inputs: none in `/menu.php` (uses hidden `nombre` and `oposicion` as context)

- Form: "Cuánto sabes del tema"  wizard — step: Configuración
  - Form tag: none (JS-driven UI)
  - View: /menu.php
  - Action: N/A (JS-driven navigation)
  - Method: N/A (no HTTP submit)
  - Trigger: onclick -> irPantallaEjercicioCuantoSabesTema('titulo.php', 'checkbox')
  - Hidden fields (context):
    - nombre: {user}
    - oposicion: {oppositionId}
  - Inputs:
    - tema: {default|0|topicOrder} topic id/order to evaluate (affects /titulo.php via TOPIC_CONTEXT)
    - dificultad: {default|1|2|3|4} (affects text masking across screens)
    - numeracion: {boolean} (affects /indice.php)
    - apartado: {boolean} (affects /indice.php)
    - ciclos: {boolean} (affects /justificacion.php)
    - leyes: {boolean} (affects /justificacion.php)
    - modulos: {boolean} (affects /justificacion.php)
    - conceptoCita: {boolean} (affects /citas.php)
    - autorCita: {boolean} (affects /citas.php)
    - anyoCita: {boolean} (affects /citas.php)
    - cita: {boolean} (affects /citas.php)
    - numeracionCita: {boolean} (affects /citas.php)
    - apartadoCita: {boolean} (affects /citas.php)
    - herramienta: {boolean} (affects /herramientas.php)
    - descripcionHerramienta: {boolean} (affects /herramientas.php)
    - ensenyanza: {boolean} (affects /contextoEscolar.php)
    - ciclosContexto: {boolean} (affects /contextoEscolar.php)
    - modulosContexto: {boolean} (affects /contextoEscolar.php)
    - conceptoContextoEscolar: {boolean} (affects /contextoEscolar.php)
    - aplicacionContextoEscolar: {boolean} (affects /contextoEscolar.php)
    - metodo: {boolean} (affects /contextoEscolar.php)
    - campo: {boolean} (affects /contextoLaboral.php)
    - profesional: {boolean} (affects /contextoLaboral.php) (label text mismatch: shows “Ciclos” in UI)
    - conceptoContextoLaboral: {boolean} (affects /contextoLaboral.php)
    - aplicacionContextoLaboral: {boolean} (affects /contextoLaboral.php)
    - beneficio: {boolean} (affects /contextoLaboral.php)
    - autorLibro: {boolean} (affects /bibliografia.php)
    - anyoLibro: {boolean} (affects /bibliografia.php)
    - tituloLibro: {boolean} (affects /bibliografia.php)
    - editorial: {boolean} (affects /bibliografia.php)
    - nombreWeb: {boolean} (affects /webgrafia.php)
    - url: {boolean} (affects /webgrafia.php)

- Form: "Cuánto sabes del tema"  wizard — step: Título
  - View: /titulo.php
  - Action: N/A (JS-driven navigation)
  - Method: N/A (no HTTP submit)
  - Trigger(s):
    - Back button -> onclick -> irPantallaEjercicioCuantoSabesTema('menu.php', 'input')
    - "Corregir" button -> onclick -> corregirEjercicioTitulo(); habilitarBotonContinuar();
    - "Reiniciar" button -> onclick -> reiniciarEjercicioTitulo();
    - "Solución" button -> onclick -> mostrarSolucionEjercicioTitulo();
    - "Continuar" button -> onclick -> irPantallaEjercicioCuantoSabesTema('indice.php', 'input') (disabled until validation)
  - Hidden fields (context/state carrier):
    - Created by: /php/definirCamposOcultos.php (reads URL params and prints hidden inputs)
    - Expected state carried:
      - nombre: {user}
      - oposicion: {oppositionId}
      - tema: {topicOrder}
      - dificultad: {1|2|3|4}
      - plus EXERCISE_CONFIGURATION flags (booleans) for later screens
  - Inputs (user editable):
    - (text) input.rellenar.rellenarTitulo.tituloTitulo
      - Meaning: user types the topic title (fill the blank)
      - Placeholder: hint derived from `obtenerAyuda(titulo, dificultad, "letras")` when dificultad != 4
  - Hidden inputs (solution/reference data):
    - input[type=hidden].solucionTitulo = {topicTitle}
  - Notes:
    - Evaluation: client-side (JS) — `corregirEjercicioTitulo()` compares user input vs `.solucionTitulo`.
    - Progress gating: "Continuar" button starts disabled and is enabled by `habilitarBotonContinuar()` after correction.

- Form "Cuánto sabes del tema" wizard — step: Índice
  - View: /indice.php
  - Action: N/A (JS-driven navigation)
  - Method: N/A (no HTTP submit)
  - Trigger(s):
    - Back button -> onclick -> irPantallaEjercicioCuantoSabesTema('titulo.php', 'input')
    - "Corregir" button -> onclick -> corregirEjercicioIndice(); habilitarBotonContinuar();
    - "Reiniciar" button -> onclick -> reiniciarEjercicioIndice();
    - "Solución" button -> onclick -> mostrarSolucionEjercicioIndice();
    - "Continuar" button -> onclick -> irPantallaEjercicioCuantoSabesTema('justificacion.php', 'input') (disabled until validation)
  - Hidden fields (context/state carrier):
    - Created by: /php/definirCamposOcultos.php (reads URL params and prints hidden inputs)
    - Expected state carried:
      - nombre: {user}
      - oposicion: {oppositionId}
      - tema: {topicOrder}
      - dificultad: {1|2|3|4}
      - numeracion: {boolean}
      - apartado: {boolean}
      - plus remaining EXERCISE_CONFIGURATION flags for later screens
  - Inputs (user editable / depending on configuration):
    - For each "apartado" row returned from DB:
      - Numeración field:
        - If numeracion == "true":
          - input.indiceNumeracion = {orden} (readonly, prefilled)
        - Else:
          - input.rellenar.rellenarNumeracion.indiceNumeracion
            - Meaning: user types section order (fill the blank)
            - Placeholder: hint derived from `obtenerAyuda(fila['orden'], dificultad, "letras")` when dificultad != 4
      - Apartado title field:
        - If apartado == "true":
          - input.indiceApartado = {titulo} (readonly, prefilled)
        - Else:
          - input.rellenar.rellenarApartado.indiceApartado
            - Meaning: user types section title (fill the blank)
            - Placeholder: hint derived from `obtenerAyuda(fila['titulo'], dificultad, "letras")` when dificultad != 4
  - Hidden inputs (solution/reference data):
    - For each row:
      - input[type=hidden].solucionNumeracion = {orden}
      - input[type=hidden].solucionApartado = {titulo}
  - Notes:
    - Data source: DB table `apartado` filtered by `codigo_oposicion` and `orden_tema`, ordered by `numeracion`.
    - Evaluation: client-side (JS) — `corregirEjercicioIndice()` compares user inputs vs `.solucionNumeracion` / `.solucionApartado`.
    - Progress gating: "Continuar" starts disabled and is enabled after correction via `habilitarBotonContinuar()`.

- Form: "Cuánto sabes del tema"  wizard — step: Justificación
  - View: /justificacion.php
  - Action: N/A (JS-driven navigation)
  - Method: N/A (no HTTP submit)
  - Trigger(s):
    - Back button -> onclick -> irPantallaEjercicioCuantoSabesTema('indice.php', 'input')
    - "Corregir" button -> onclick -> corregirEjercicioJustificacion(); habilitarBotonContinuar();
    - "Reiniciar" button -> onclick -> reiniciarEjercicioJustificacion();
    - "Solución" button -> onclick -> mostrarSolucionEjercicioJustificacion();
    - "Continuar" button -> onclick -> irPantallaEjercicioCuantoSabesTema('citas.php', 'input') (disabled until validation)
  - Hidden fields (context/state carrier):
    - Created by: /php/definirCamposOcultos.php (reads URL params and prints hidden inputs)
    - Expected state carried:
      - nombre: {user}
      - oposicion: {oppositionId}
      - tema: {topicOrder}
      - dificultad: {1|2|3|4}
      - ciclos: {boolean}
      - leyes: {boolean}
      - modulos: {boolean}
      - plus remaining EXERCISE_CONFIGURATION flags for later screens
  - Inputs (user editable / depending on configuration):
    - For each cycle block:
      - Cycle name:
        - If ciclos == "true": rendered as plain text (no input)
        - Else:
          - input.rellenar.rellenarCiclo.justificacionCiclo
            - Meaning: user types cycle name (fill the blank)
            - Placeholder: hint derived from `obtenerAyuda(ciclo, dificultad, "letras")` when dificultad != 4
      - Laws list:
        - If leyes == "true": each law rendered as plain text
        - Else:
          - input.rellenar.rellenarLey.justificacionLey (one per law)
            - Meaning: user types law name
            - Placeholder: hint derived from `obtener

- Form: "Cuánto sabes del tema"  wizard — step: Citas
  - View: /citas.php
  - Action: N/A (JS-driven navigation)
  - Method: N/A (no HTTP submit)
  - Trigger(s):
    - Back button -> onclick -> irPantallaEjercicioCuantoSabesTema('justificacion.php', 'input')
    - "Corregir" button -> onclick -> corregirEjercicioCitas(); habilitarBotonContinuar();
    - "Reiniciar" button -> onclick -> reiniciarEjercicioCitas();
    - "Solución" button -> onclick -> mostrarSolucionEjercicioCitas();
    - "Continuar" button -> onclick -> irPantallaEjercicioCuantoSabesTema('herramientas.php', 'input') (disabled until validation)
  - Hidden fields (context/state carrier):
    - Created by: /php/definirCamposOcultos.php (reads URL params and prints hidden inputs)
    - Expected state carried:
      - nombre: {user}
      - oposicion: {oppositionId}
      - tema: {topicOrder}
      - dificultad: {1|2|3|4}
      - conceptoCita: {boolean}
      - autorCita: {boolean}
      - anyoCita: {boolean}
      - cita: {boolean}
      - numeracionCita: {boolean}
      - apartadoCita: {boolean}
      - plus remaining EXERCISE_CONFIGURATION flags for later screens
  - Inputs (user editable / depending on configuration):
    - For each quote block returned from DB:
      - Quote concept:
        - If conceptoCita == "true":
          - input.citasConcepto = {concepto_cita} (readonly, prefilled)
        - Else:
          - input.rellenar.rellenarConcepto.citasConcepto (placeholder uses `obtenerAyuda(..., "letras")` when dificultad != 4)
      - Quote author:
        - If autorCita == "true":
          - input.citasAutor = {autor_cita} (readonly, prefilled)
        - Else:
          - input.rellenar.rellenarAutor.citasAutor (placeholder uses `obtenerAyuda(..., "letras")` when dificultad != 4)
      - Quote year:
        - If anyoCita == "true":
          - input.citasAnyo = {anyo_cita} (readonly, prefilled)
        - Else:
          - input.rellenar.rellenarAnyo.citasAnyo (placeholder uses `obtenerAyuda(..., "letras")` when dificultad != 4)
      - Section reference (where the quote appears):
        - Section order:
          - If numeracionCita == "true":
            - input.citasNumeracion = {apartado_orden} (readonly, prefilled)
          - Else:
            - input.rellenar.rellenarNumeracion.citasNumeracion (placeholder uses `obtenerAyuda(..., "letras")` when dificultad != 4)
        - Section title:
          - If apartadoCita == "true":
            - input.citasApartado = {apartado_titulo} (readonly, prefilled)
          - Else:
            - input.rellenar.rellenarApartado.citasApartado (placeholder uses `obtenerAyuda(..., "letras")` when dificultad != 4)
      - Quote content:
        - If cita == "true":
          - textarea.citasCita contains {contenido} (prefilled; no placeholder)
        - Else:
          - textarea.rellenar.rellenarCita.citasCita (placeholder uses `obtenerAyuda(..., "palabras")` when dificultad != 4)
  - Hidden inputs (solution/reference data):
    - Per quote block:
      - input[type=hidden].solucion.solucionConcepto = {concepto_cita}
      - input[type=hidden].solucion.solucionAutor = {autor_cita}
      - input[type=hidden].solucion.solucionAnyo = {anyo_cita}
      - input[type=hidden].solucion.solucionNumeracion = {apartado_orden}
      - input[type=hidden].solucion.solucionApartado = {apartado_titulo}
      - input[type=hidden].solucion.solucionCita = {contenido}
  - Notes:
    - Client-side libraries:
      - Includes `string-similarity.min.js` in addition to `misScripts.js`, suggesting similarity-based evaluation is used for at least some fields (notably long text like quote content).
    - Data source: SQL joins `apartado_tener_cita`, `cita`, and `apartado` filtered by `codigo_oposicion` and `orden_tema`.
    - Evaluation: client-side (JS) — `corregirEjercicioCitas()` compares user inputs vs `.solucion*`.
    - Progress gating: "Continuar" starts disabled and is enabled after correction via `habilitarBotonContinuar()`.

- Form: "Cuánto sabes del tema"  wizard — step: Herramientas
  - View: /herramientas.php
  - Action: N/A (JS-driven navigation)
  - Method: N/A (no HTTP submit)
  - Trigger(s):
    - Back button -> onclick -> irPantallaEjercicioCuantoSabesTema('citas.php', 'input')
    - "Corregir" button -> onclick -> corregirEjercicioHerramientas(); habilitarBotonContinuar();
    - "Reiniciar" button -> onclick -> reiniciarEjercicioHerramientas();
    - "Solución" button -> onclick -> mostrarSolucionEjercicioHerramientas();
    - "Continuar" button -> onclick -> irPantallaEjercicioCuantoSabesTema('contextoEscolar.php', 'input') (disabled until validation)
  - Hidden fields (context/state carrier):
    - Created by: /php/definirCamposOcultos.php (reads URL params and prints hidden inputs)
    - Expected state carried:
      - nombre: {user}
      - oposicion: {oppositionId}
      - tema: {topicOrder}
      - dificultad: {1|2|3|4}
      - herramienta: {boolean}
      - descripcionHerramienta: {boolean}
      - plus remaining EXERCISE_CONFIGURATION flags for later screens
  - Inputs (user editable / depending on configuration):
    - For each tool row returned from DB:
      - Tool name:
        - If herramienta == "true":
          - input.herramientasNombre = {toolName} (readonly, prefilled)
        - Else:
          - input.rellenar.rellenarNombre.herramientasNombre
            - Meaning: user types tool name (fill the blank)
            - Placeholder: hint derived from `obtenerAyuda(nombre, dificultad, "letras")` when dificultad != 4
      - Tool description (optional field):
        - Only rendered when descripcion != "" (description may be null/empty in DB). 
        - If descripcionHerramienta == "true":
          - textarea.herramientasDescripcion contains {description} (prefilled)
        - Else:
          - textarea.rellenar.rellenarDescripcion.herramientasDescripcion
            - Meaning: user types tool description (fill the blank)
            - Placeholder: hint derived from `obtenerAyuda(descripcion, dificultad, "palabras")` when dificultad != 4
  - Hidden inputs (solution/reference data):
    - Per tool row:
      - input[type=hidden].solucionNombre = {toolName}
      - input[type=hidden].solucionDescripcion = {description} (may be empty)
  - Notes:
    - Client-side libraries:
      - Includes `string-similarity.min.js`, likely to support similarity-based evaluation for longer text fields (e.g., descriptions). 
    - Data source: SQL join between `tema_usar_herramienta` and `herramienta` filtered by `codigo_oposicion` and `orden_tema`. 
    - Evaluation: client-side (JS) — `corregirEjercicioHerramientas()` compares user inputs vs `.solucionNombre` / `.solucionDescripcion`.
    - Progress gating: "Continuar" starts disabled and is enabled after correction via `habilitarBotonContinuar()`.

- Form: "Cuánto sabes del tema"  wizard — step: Contexto Escolar
  - View: /contextoEscolar.php
  - Action: N/A (JS-driven navigation)
  - Method: N/A (no HTTP submit)
  - Trigger(s):
    - Back button -> onclick -> irPantallaEjercicioCuantoSabesTema('herramientas.php', 'input')
    - "Corregir" button -> onclick -> corregirEjercicioContextoEscolar(); habilitarBotonContinuar();
    - "Reiniciar" button -> onclick -> reiniciarEjercicioContextoEscolar();
    - "Solución" button -> onclick -> mostrarSolucionEjercicioContextoEscolar();
    - "Continuar" button -> onclick -> irPantallaEjercicioCuantoSabesTema('contextoLaboral.php', 'input') (disabled until validation)
  - Hidden fields (context/state carrier):
    - Created by: /php/definirCamposOcultos.php (reads URL params and prints hidden inputs)
    - Expected state carried:
      - nombre: {user}
      - oposicion: {oppositionId}
      - tema: {topicOrder}
      - dificultad: {1|2|3|4}
      - ensenyanza: {boolean}
      - ciclosContexto: {boolean}
      - modulosContexto: {boolean}
      - conceptoContextoEscolar: {boolean}
      - aplicacionContextoEscolar: {boolean}
      - metodo: {boolean}
      - plus remaining EXERCISE_CONFIGURATION flags for later screens
  - Inputs (user editable / depending on configuration):
    - For each row from `contexto_escolar`:
      - Teaching (ensenyanza):
        - If ensenyanza == "true":
          - input.contextoEscolarEnsenyanza = {ensenyanza} (readonly, prefilled)
        - Else:
          - input.rellenar.rellenarEnsenyanza.contextoEscolarEnsenyanza
            - Placeholder: `obtenerAyuda(ensenyanza, dificultad, "letras")` when dificultad != 4
      - Cycle (ciclo) (optional):
        - Only rendered when `fila['ciclo'] != ""`. 
        - If ciclosContexto == "true":
          - input.contextoEscolarCiclos = {ciclo} (readonly, prefilled)
        - Else:
          - input.rellenar.rellenarCiclos.contextoEscolarCiclos
            - Placeholder: `obtenerAyuda(ciclo, dificultad, "letras")` when dificultad != 4
      - Module (modulo) (optional):
        - Only rendered when `fila['modulo'] != ""`. 
        - If modulosContexto == "true":
          - input.contextoEscolarModulos = {modulo} (readonly, prefilled)
        - Else:
          - input.rellenar.rellenarModulos.contextoEscolarModulos
            - Placeholder: `obtenerAyuda(modulo, dificultad, "letras")` when dificultad != 4
      - Concept (concepto):
        - If conceptoContextoEscolar == "true":
          - textarea.contextoEscolarConcepto contains {concepto} (prefilled)
        - Else:
          - textarea.rellenar.rellenarConcepto.contextoEscolarConcepto
            - Placeholder: `obtenerAyuda(concepto, dificultad, "palabras")` when dificultad != 4
      - Application (aplicacion):
        - If aplicacionContextoEscolar == "true":
          - textarea.contextoEscolarAplicacion contains {aplicacion} (prefilled)
        - Else:
          - textarea.rellenar.rellenarAplicacion.contextoEscolarAplicacion
            - Placeholder: `obtenerAyuda(aplicacion, dificultad, "palabras")` when dificultad != 4
      - Method (metodo) (optional):
        - Only rendered when `fila['metodo'] != ""`. 
          - textarea.contextoEscolarMetodo contains {metodo} (prefilled)
        - Else:
          - textarea.rellenar.rellenarMetodo.contextoEscolarMetodo
            - Placeholder: `obtenerAyuda(metodo, dificultad, "palabras")` when dificultad != 4
  - Hidden inputs (solution/reference data):
    - input[type=hidden].solucionEnsenyanza = {ensenyanza}
    - input[type=hidden].solucionCiclos = {ciclo} (may be empty)
    - input[type=hidden].solucionModulos = {modulo} (may be empty)
    - input[type=hidden].solucionConcepto = {concepto}
    - input[type=hidden].solucionAplicacion = {aplicacion}
    - input[type=hidden].solucionMetodo = {metodo} (may be empty)
  - Notes:
    - Client-side libraries:
      - Includes `string-similarity.min.js`, likely to support similarity-based evaluation for longer text fields (concept/application/method). 
    - Data source: DB table `contexto_escolar` filtered by `codigo_oposicion` and `orden_tema`. 
    - Evaluation: client-side (JS) — `corregirEjercicioContextoEscolar()` compares user inputs vs `.solucion*`.
    - Progress gating: "Continuar" starts disabled and is enabled after correction via `habilitarBotonContinuar()`.

- Form: "Cuánto sabes del tema"  wizard — step: Contexto Laboral
  - View: /contextoLaboral.php
  - Action: N/A (JS-driven navigation)
  - Method: N/A (no HTTP submit)
  - Trigger(s):
    - Back button -> onclick -> irPantallaEjercicioCuantoSabesTema('contextoEscolar.php', 'input')
    - "Corregir" button -> onclick -> corregirEjercicioContextoLaboral(); habilitarBotonContinuar();
    - "Reiniciar" button -> onclick -> reiniciarEjercicioContextoLaboral();
    - "Solución" button -> onclick -> mostrarSolucionEjercicioContextoLaboral();
    - "Continuar" button -> onclick -> irPantallaEjercicioCuantoSabesTema('bibliografia.php', 'input') (disabled until validation)
  - Hidden fields (context/state carrier):
    - Created by: /php/definirCamposOcultos.php (reads URL params and prints hidden inputs)
    - Expected state carried:
      - nombre: {user}
      - oposicion: {oppositionId}
      - tema: {topicOrder}
      - dificultad: {1|2|3|4}
      - campo: {boolean}
      - profesional: {boolean}
      - conceptoContextoLaboral: {boolean}
      - aplicacionContextoLaboral: {boolean}
      - beneficio: {boolean}
      - plus remaining EXERCISE_CONFIGURATION flags for later screens
  - Inputs (user editable / depending on configuration):
    - For each row from `contexto_laboral`:
      - Field (campo):
        - If campo == "true":
          - input.contextoLaboralCampo = {campo} (readonly, prefilled)
        - Else:
          - input.rellenar.rellenarCampo.contextoLaboralCampo
            - Placeholder: `obtenerAyuda(campo, dificultad, "letras")` when dificultad != 4
      - Professional (profesional) (optional):
        - Only rendered when `fila['profesional'] != ""` (may be null/empty). 
        - If profesional == "true":
          - input.contextoLaboralProfesional = {profesional} (readonly, prefilled)
        - Else:
          - input.rellenar.rellenarProfesional.contextoLaboralProfesional
            - Placeholder: `obtenerAyuda(profesional, dificultad, "letras")` when dificultad != 4
      - Concept (concepto):
        - If conceptoContextoLaboral == "true":
          - textarea.contextoLaboralConcepto contains {concepto} (readonly)
        - Else:
          - textarea.rellenar.rellenarConcepto.contextoLaboralConcepto
            - Placeholder: `obtenerAyuda(concepto, dificultad, "palabras")` when dificultad != 4
      - Task / application (tarea) (note: stored as `tarea` in DB):
        - If aplicacionContextoLaboral == "true":
          - textarea.contextoLaboralAplicacion contains {tarea} (intended readonly; code has typo `readyonly`).
        - Else:
          - textarea.rellenar.rellenarAplicacion.contextoLaboralAplicacion
            - Placeholder: `obtenerAyuda(tarea, dificultad, "palabras")` when dificultad != 4
      - Benefit (beneficio):
        - If beneficio == "true":
          - textarea.contextoLaboralBeneficio contains {beneficio} (readonly)
        - Else:
          - textarea.rellenar.rellenarBeneficio.contextoLaboralBeneficio
            - Placeholder: `obtenerAyuda(beneficio, dificultad, "palabras")` when dificultad != 4
  - Hidden inputs (solution/reference data):
    - input[type=hidden].solucionCampo = {campo}
    - input[type=hidden].solucionProfesional = {profesional} (may be empty)
    - input[type=hidden].solucionConcepto = {concepto}
    - input[type=hidden].solucionAplicacion = {tarea}
    - input[type=hidden].solucionBeneficio = {beneficio}
  - Notes:
    - Client-side libraries:
      - Includes `string-similarity.min.js`, likely used for similarity-based evaluation on longer text fields. 
    - Data source: DB table `contexto_laboral` filtered by `codigo_oposicion` and `orden_tema`. 
    - Evaluation: client-side (JS) — `corregirEjercicioContextoLaboral()` compares user inputs vs `.solucion*`.
    - Progress gating: "Continuar" starts disabled and is enabled after correction via `habilitarBotonContinuar()`.

- Form: "Cuánto sabes del tema"  wizard — step: Bibliografía
  - View: /bibliografia.php
  - Action: N/A (JS-driven navigation)
  - Method: N/A (no HTTP submit)
  - Trigger(s):
    - Back button -> onclick -> irPantallaEjercicioCuantoSabesTema('contextoLaboral.php', 'input')
    - "Corregir" button -> onclick -> corregirEjercicioBibliografia(); habilitarBotonContinuar();
    - "Reiniciar" button -> onclick -> reiniciarEjercicioBibliografia();
    - "Solución" button -> onclick -> mostrarSolucionEjercicioBibliografia();
    - "Continuar" button -> onclick -> irPantallaEjercicioCuantoSabesTema('webgrafia.php', 'input') (disabled until validation)
  - Hidden fields (context/state carrier):
    - Created by: /php/definirCamposOcultos.php (reads URL params and prints hidden inputs)
    - Expected state carried:
      - nombre: {user}
      - oposicion: {oppositionId}
      - tema: {topicOrder}
      - dificultad: {1|2|3|4}
      - autorLibro: {boolean}
      - anyoLibro: {boolean}
      - tituloLibro: {boolean}
      - editorial: {boolean}
      - plus remaining EXERCISE_CONFIGURATION flags for later screens
  - Inputs (user editable / depending on configuration):
    - For each book row returned from DB:
      - Author:
        - If autorLibro == "true":
          - input.bibliografiaAutor = {author} (readonly, prefilled)
        - Else:
          - input.rellenar.rellenarAutor.bibliografiaAutor
            - Placeholder: `obtenerAyuda(autor, dificultad, "letras")` when dificultad != 4
      - Publication year:
        - If anyoLibro == "true":
          - input.bibliografiaAnyo = {year} (readonly, prefilled)
        - Else:
          - input.rellenar.rellenarAnyo.bibliografiaAnyo
            - Placeholder: `obtenerAyuda(anyo, dificultad, "letras")` when dificultad != 4
      - Title:
        - If tituloLibro == "true":
          - input.bibliografiaTitulo = {title} (readonly, prefilled)
        - Else:
          - input.rellenar.rellenarTitulo.bibliografiaTitulo
            - Placeholder: `obtenerAyuda(titulo, dificultad, "letras")` when dificultad != 4
      - Editorial:
        - If editorial == "true":
          - input.bibliografiaEditorial = {editorial} (readonly, prefilled)
        - Else:
          - input.rellenar.rellenarEditorial.bibliografiaEditorial
            - Placeholder: `obtenerAyuda(editorial, dificultad, "letras")` when dificultad != 4
  - Hidden inputs (solution/reference data):
    - Per book row:
      - input[type=hidden].solucionAutor = {author}
      - input[type=hidden].solucionAnyo = {year}
      - input[type=hidden].solucionTitulo = {title}
      - input[type=hidden].solucionEditorial = {editorial}
  - Notes:
    - Client-side libraries:
      - Includes `string-similarity.min.js` (likely not essential here since all fields are short, but present consistently in these steps). {index=1}
    - Data source:
      - SQL join between `tema_referenciar_libro` and `libro` (joined by autor + titulo), filtered by `codigo_oposicion` and `orden_tema`. 
    - Evaluation: client-side (JS) — `corregirEjercicioBibliografia()` compares user inputs vs `.solucionAutor` / `.solucionAnyo` / `.solucionTitulo` / `.solucionEditorial`.
    - Progress gating: "Continuar" starts disabled and is enabled after correction via `habilitarBotonContinuar()`.

- Form: "Cuánto sabes del tema"  wizard — step: Webgrafía
  - View: /webgrafia.php
  - Action: N/A (JS-driven navigation)
  - Method: N/A (no HTTP submit)
  - Trigger(s):
    - Back button -> onclick -> irPantallaEjercicioCuantoSabesTema('webgrafia.php', 'input')
      - Note: back currently points to the same screen (likely intended to go to /bibliografia.php).
    - "Corregir" button -> onclick -> corregirEjercicioWebgrafia(); habilitarBotonContinuar();
    - "Reiniciar" button -> onclick -> reiniciarEjercicioWebgrafia();
    - "Solución" button -> onclick -> mostrarSolucionEjercicioWebgrafia();
    - "Fin" button -> onclick -> irPantallaEjercicioCuantoSabesTema('menu.php', 'input') (disabled until validation)
  - Hidden fields (context/state carrier):
    - Created by: /php/definirCamposOcultos.php (reads URL params and prints hidden inputs)
    - Expected state carried:
      - nombre: {user}
      - oposicion: {oppositionId}
      - tema: {topicOrder}
      - dificultad: {1|2|3|4}
      - nombreWeb: {boolean}
      - url: {boolean}
      - plus remaining EXERCISE_CONFIGURATION flags
  - Inputs (user editable / depending on configuration):
    - For each website row returned from DB:
      - Website name:
        - If nombreWeb == "true":
          - input.webgrafiaNombre = {siteName} (readonly, prefilled)
        - Else:
          - input.rellenar.rellenarNombre.webgrafiaNombre
            - Meaning: user types website name (fill the blank)
            - Placeholder: `obtenerAyuda(nombre, dificultad, "letras")` when dificultad != 4
      - Website URL:
        - If url == "true":
          - input.webgrafiaUrl = {siteUrl} (readonly, prefilled)
        - Else:
          - input.rellenar.rellenarUrl.webgrafiaUrl
            - Meaning: user types website URL (fill the blank)
            - Placeholder: `obtenerAyuda(url, dificultad, "letras")` when dificultad != 4
  - Hidden inputs (solution/reference data):
    - Per website row:
      - input[type=hidden].solucionNombre = {siteName}
      - input[type=hidden].solucionUrl = {siteUrl}
  - Notes:
    - Client-side libraries:
      - Includes `string-similarity.min.js` (present consistently in later steps; may be useful for approximate matching, especially URLs).
    - Data source:
      - SQL join between `tema_referenciar_web` and `web` (joined by url), filtered by `codigo_oposicion` and `orden_tema`.
    - Evaluation: client-side (JS) — `corregirEjercicioWebgrafia()` compares user inputs vs `.solucionNombre` / `.solucionUrl`.
    - Progress gating: "Fin" starts disabled and is enabled after correction via `habilitarBotonContinuar()`.

#### Wizard screens overview (Cuánto sabes del tema)

| Screen | Purpose | Main configuration flags | Similarity lib | Gating |
|------|--------|--------------------------|----------------|--------|
| menu.php | Exercise configuration | dificultad + all boolean flags | ❌ | ❌ |
| titulo.php | Topic title | dificultad | ❌ | ✅ |
| indice.php | Sections index | numeracion, apartado, dificultad | ❌ | ✅ |
| justificacion.php | Cycles, laws and modules | ciclos, leyes, modulos, dificultad | ❌ | ✅ |
| citas.php | Quotes | conceptoCita, autorCita, anyoCita, cita, numeracionCita, apartadoCita | ✅ | ✅ |
| herramientas.php | Teaching tools | herramienta, descripcionHerramienta | ✅ | ✅ |
| contextoEscolar.php | School context | ensenyanza, ciclosContexto, modulosContexto, conceptoContextoEscolar, aplicacionContextoEscolar, metodo | ✅ | ✅ |
| contextoLaboral.php | Professional context | campo, profesional, conceptoContextoLaboral, aplicacionContextoLaboral, beneficio | ✅ | ✅ |
| bibliografia.php | Bibliography | autorLibro, anyoLibro, tituloLibro, editorial | (optional) | ✅ |
| webgrafia.php | Webography | nombreWeb, url | (optional) | ✅ |

**Notes**

- All wizard steps (except menu.php) implement progress gating via a disabled "Continue/Fin" button.
- Evaluation is performed client-side in all steps.
- `string-similarity.min.js` is used mainly in steps with long text fields (quotes, descriptions, contexts).
- Exercise state is propagated across screens via hidden inputs generated by `definirCamposOcultos.php`.


---

## 3. Legacy client-side controller (`misScripts.js`) — responsibilities and flow

`/js/misScripts.js` acts as a client-side orchestration layer for legacy exercises. It concentrates responsibilities that, in an MVC design, would be split across Controllers (routing/flow), Domain Services (evaluation rules), and View/UI helpers.

### 3.1 Main responsibilities (current)
- Navigation between legacy screens by building long query strings and redirecting via `document.location.href`.
- Reading exercise configuration from DOM inputs (both normal inputs and hidden inputs) and propagating it across screens (state carried in URL parameters). 
- Random topic selection when the user chooses a special option (topic=0 → pick random).
- In-screen evaluation logic:
  - Equality-based checks (`modo = "igualdad"`).
  - Similarity-based checks (`modo = "similitud"`) using `string-similarity`.
- UI feedback helpers:
  - Mark inputs as correct/incorrect via CSS classes.
  - Reset inputs and clear visible solutions.
  - Render “show solution” blocks by injecting DOM elements.
- Gating progression: enable/disable “Continue” button based on correctness.

### 3.2 Navigation and state propagation
The function `irPantallaEjercicioCuantoSabesTema(pantalla, from)` reads a large set of configuration flags (many booleans) from DOM inputs and constructs the next screen URL with all values appended as query parameters. This is the primary legacy mechanism to keep exercise state between screens. 

### 3.3 Evaluation model used in legacy
Evaluation is performed in the browser by comparing user inputs with hidden “solution” inputs contained in each question container:
- Equality comparison: case-insensitive string equality.
- Similarity comparison: uses a threshold (currently 0.7) for approximate matching.

### 3.4 Implications for the MVC migration
- The legacy system transports configuration through query string + hidden inputs; the MVC design should replace this with an `attemptId` / `ExerciseSession` persisted server-side to avoid huge URLs and duplicated state logic.
- Evaluation logic currently lives in JS; the MVC design should progressively move evaluation to server-side domain services (keeping client-side feedback only as UI enhancement during transition).
- `misScripts.js` will likely be split into:
  - (Controller) request/flow orchestration (server-side)
  - (Service) evaluators (server-side)
  - (View) minimal JS for UX (client-side)
- In the initial MVC slices, the legacy JavaScript codebase remains in place and can coexist with the new MVC implementation. However, MVC exercise screens do not rely on legacy JS for flow control or evaluation logic.

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
- Fill in the blanks (wizard-based, multi-step)
- Free writing (single-step exercise)

---

## 5. Legacy flow map (current behavior)

### 5.1 Exercise: “Cuánto sabes del tema” (fill-the-blanks wizard)

#### Step A — Configuration (menu.php)
- Entry: `GET /menu.php?nombre={user}&oposicion={oppositionId}`
- UI: user selects:
  - `tema` (topic order; `0` = random)
  - `dificultad` {1|2|3|4}
  - multiple boolean flags controlling which fields are blank vs shown
- State handling:
  - `nombre` and `oposicion` are stored in hidden inputs (DOM).
  - On “Comenzar”, JS reads all inputs and redirects to the first wizard screen (`titulo.php`) by building a long query string (state carried via URL parameters).

#### Step B — Load data (server-side per screen)
Each wizard screen:
- Reads the state from URL parameters and creates hidden inputs via `/php/definirCamposOcultos.php`.
- Loads exercise payload from DB (topic/title/sections/quotes/tools/contexts/books/webs).
- Optionally computes “hints” via `obtenerAyuda(value, dificultad, mode)` when `dificultad != 4`.
- Renders:
  - user-editable inputs when the corresponding flag is disabled (blank must be filled),
  - or readonly/pre-filled inputs when the corresponding flag is enabled (not evaluated).

Wizard screens sequence (legacy):
1) `titulo.php`
2) `indice.php`
3) `justificacion.php`
4) `citas.php`
5) `herramientas.php`
6) `contextoEscolar.php`
7) `contextoLaboral.php`
8) `bibliografia.php`
9) `webgrafia.php`

#### Step C — Execution (user interaction in the browser)
- User fills the editable fields (inputs/textarea).
- Each screen displays:
  - a “question” container with editable fields,
  - and an invisible “solution” container with hidden inputs containing the expected values.

#### Step D — Evaluation (client-side)
- Trigger: user clicks “Corregir”.
- Evaluation is executed in the browser (JS in `misScripts.js`):
  - compares user input values with hidden solution values.
  - marks fields as correct/incorrect via CSS classes (presentation feedback).
  - for longer text fields, the app includes `string-similarity.min.js` on some screens (suggesting similarity-based matching).
- There is no server-side submission for answers; no POST-based persistence is observed in this flow.

#### Step E — Results + navigation (in-screen feedback + gated progression)
- Results are immediate and local to the screen:
  - incorrect/correct highlighting
  - optional “Solución” action to reveal the expected answers
  - “Reiniciar” to clear user inputs
- Progress gating:
  - “Continuar/Fin” button starts disabled and is enabled after correction (`habilitarBotonContinuar()`).
- Navigation:
  - “Continuar” redirects to the next screen via JS by rebuilding the URL query string (state carried forward).
  - Final screen `webgrafia.php` ends by redirecting to `menu.php`.


### 5.2 Observed state carriers (how legacy keeps state)
- URL query string: the primary mechanism. Configuration flags + difficulty + topic selection are propagated across screens by JS redirects.
- Hidden inputs injected server-side: `/php/definirCamposOcultos.php` prints hidden fields so JS can re-read state from the DOM on each screen.
- Hidden “solution” fields per question: expected answers are embedded in the HTML as `<input type="hidden">` (solution container) and used by client-side evaluation.

### 5.3 Risks / limitations of the legacy flow
- Extremely large URLs: state is duplicated and transported on every navigation step, increasing fragility and making debugging harder.
- Client-side evaluation only:
  - answers are never submitted to the server (no POST/PRG), so results are not persisted and can be bypassed.
  - correctness rules live in JS, making it harder to reuse the same logic on the server later.
- Mixing concerns per screen: each PHP screen typically performs controller-like tasks (reading params, querying DB, deciding what is editable) and view rendering in the same file.
- Duplication: the same rendering + evaluation pattern (inputs + hidden solutions + buttons) is repeated across many screens.
- Security / integrity: solutions are shipped to the browser (hidden inputs), so users can inspect them.
- Inconsistent navigation: some “Back” links can point to the same screen (potential bug), showing the fragility of manual URL wiring.

### 5.4 Mapping legacy wizard steps to future MVC design

The legacy wizard can be mapped to a single conceptual exercise flow in MVC, with clear separation between configuration, execution and evaluation.

### 5.5 Known legacy bugs (documented, not fixed yet)
- Back button points wrongly in `webgrafia.php`
- Typo in `contextoLaboral.php` textarea.contextoLaboralAplicacion readyonly should be readonly
- Label inconsistency in `menu.php` profesional tagged by Ciclos and should be Profesional


#### Proposed MVC structure (high-level)

- **GET /exercises/{exerciseType}/config**
  - Replaces: `menu.php`
  - Responsibility:
    - Render exercise configuration UI (topic, difficulty, flags).
  - Output:
    - Exercise configuration form (no execution logic).

- **POST /exercises/{exerciseType}/start**
  - Replaces: JS redirect from `menu.php` to `titulo.php`
  - Responsibility:
    - Validate configuration.
    - Create an `ExerciseSession` (server-side).
    - Redirect to the first execution step.

- **GET /exercises/{exerciseType}/sessions/{sessionId}/steps/{step}**
  - Replaces:
    - `titulo.php`
    - `indice.php`
    - `justificacion.php`
    - `citas.php`
    - `herramientas.php`
    - `contextoEscolar.php`
    - `contextoLaboral.php`
    - `bibliografia.php`
    - `webgrafia.php`
  - Responsibility:
    - Load domain data for the given step.
    - Decide which fields are evaluable based on configuration flags.
    - Render the step view.
  - Notes:
    - State is retrieved from the session (not from URL parameters).

- **POST /exercises/{exerciseType}/sessions/{sessionId}/steps/{step}/evaluate**
  - Replaces: client-side `corregirEjercicio*()` JS logic.
  - Responsibility:
    - Receive user answers.
    - Evaluate correctness using domain rules.
    - Return evaluation result (PRG pattern or JSON for progressive enhancement).

- **POST /exercises/{exerciseType}/sessions/{sessionId}/steps/{step}/reset**
  - Replaces: client-side “Reiniciar”.
  - Responsibility:
    - Clear user answers for the current step.

- **GET /exercises/{exerciseType}/sessions/{sessionId}/summary**
  - Replaces: implicit end of wizard (return to `menu.php`).
  - Responsibility:
    - Show final results and summary.
    - Optionally persist attempt data.

#### Key improvements over legacy
- Exercise state lives server-side (`ExerciseSession`) instead of URL + hidden inputs.
- Evaluation logic moves to domain services and can be reused/tested.
- Views become pure rendering layers (no DB access, no flow decisions).
- Navigation becomes explicit and declarative instead of JS-wired URLs.
---

## 6. Responsibility audit (legacy code)

This section identifies where responsibilities live today (often mixed) and where they should move in the future MVC architecture.

### 6.1 Responsibility categories used
- **Domain (Model):** core concepts + rules (independent from web/UI).
- **Controller-ish:** request flow, navigation, orchestration, decision-making.
- **View:** HTML rendering / UI composition.
- **Infrastructure:** DB access, IO, glue code.
- **Assets / Libraries:** UI assets or third-party code.

### 6.2 Entry points and screens (wizard)
| File | Current responsibilities | Category (legacy) | MVC target |
|------|--------------------------|-------------------|-----------|
| /menu.php | Renders config UI; collects inputs; relies on JS to start wizard; holds hidden user context | View + Controller-ish | `ExercisesConfigController::show()` + `start()` |
| /titulo.php | Reads state; queries DB; computes hints; renders inputs + hidden solutions; includes gating buttons | Mixed (Controller-ish + View) | `WizardStepController::show(step=title)` + step view |
| /indice.php | Reads flags to decide readonly vs blanks; queries DB; renders list inputs + hidden solutions | Mixed | `WizardStepController::show(step=index)` + step view |
| /justificacion.php | Builds nested structure (cycles/laws/modules); renders conditional blanks + hidden solutions | Mixed | `WizardStepController::show(step=justification)` + step view |
| /citas.php | Queries quotes + apartado info; renders textareas; includes similarity lib; embeds hidden solutions | Mixed | `WizardStepController::show(step=quotes)` + step view |
| /herramientas.php | Queries tools; renders optional description; includes similarity lib; embeds hidden solutions | Mixed | `WizardStepController::show(step=tools)` + step view |
| /contextoEscolar.php | Queries school context; renders optional fields; includes similarity lib; embeds hidden solutions | Mixed | `WizardStepController::show(step=school-context)` + step view |
| /contextoLaboral.php | Queries professional context; renders optional fields; includes similarity lib; embeds hidden solutions | Mixed | `WizardStepController::show(step=work-context)` + step view |
| /bibliografia.php | Queries books; renders conditional blanks; embeds hidden solutions | Mixed | `WizardStepController::show(step=bibliography)` + step view |
| /webgrafia.php | Queries websites; renders conditional blanks; embeds hidden solutions; end navigation | Mixed | `WizardStepController::show(step=webography)` + `summary()` |

### 6.3 Shared PHP helpers (server-side)
| File | Current responsibilities | Category (legacy) | MVC target |
|------|--------------------------|-------------------|-----------|
| /php/definirCamposOcultos.php | Reads URL params; creates hidden inputs; effectively carries state between screens | Controller-ish + View helper | Replace with server-side `ExerciseSession` + request DTO |
| /php/db.php | DB connection / credentials / low-level access | Infrastructure | `Database` / repository layer |
| /php/obtenerNombreTema.php | Builds `$sql` to fetch topic title/name (plus side effects) | Infrastructure + Domain-ish | `TopicRepository` + `Topic` model |
| /php/utils.php | Domain-style logic: hints (`obtenerAyuda`) and masking based on difficulty | Domain (Model) | `HintService` / `MaskingService` (unit-testable) |

### 6.4 Client-side orchestration (JS)
| File | Current responsibilities | Category (legacy) | MVC target |
|------|--------------------------|-------------------|-----------|
| /js/misScripts.js | Navigation (build URLs); gating; evaluation; reset; show solution; DOM updates | Controller-ish + Domain-ish + View helper (mixed) | Split into: server Controllers + domain Evaluators + minimal UI JS |
| /js/string-similarity.min.js | Similarity algorithm used by evaluation | Third-party library | Keep as library or replace with server-side equivalent |
| /css/miEstilo.css | Presentation feedback (right/wrong, layout) | View asset | Reuse in MVC views (or replace gradually) |

### 6.5 Key “mixing” hotspots (what to untangle first)
- **Screens as “everything files”:** each `*.php` step performs state parsing, DB querying, rule decisions, and HTML rendering.
- **State propagation:** URL params + hidden inputs are a cross-cutting concern spread everywhere.
- **Evaluation logic:** lives in JS and depends on “solutions in HTML”, which is a major architectural constraint.

### 6.6 MVC extraction plan (high-level)
- Move **state** into `ExerciseSession` (server-side), referenced by `{sessionId}`.
- Move **evaluation** into domain services (`Evaluator` + strategy per field/type).
- Keep **views** responsible only for rendering:
  - questions,
  - current answers,
  - evaluation feedback (from server result).
- Keep minimal client-side JS only for UX (optional), not for correctness.

---

## 7. Proposed MVC architecture for exercises (high level)

This section defines the target MVC design for exercises, preserving legacy behavior during transition while enabling incremental refactoring.

### 7.0 Execution lifecycle: Session vs Attempt

The MVC design distinguishes two related but conceptually different lifecycle entities:

- **ExerciseSession**
  Represents the *interactive, in-progress execution* of an exercise.
  It is created when the user starts an exercise and exists while the user navigates through its steps.
  A session is mutable and may evolve over time as the user submits answers and progresses through the wizard.

- **ExerciseAttempt**
  Represents a *completed snapshot* of an exercise execution.
  It is created when the exercise is finished (or explicitly submitted) and is suitable for long-term persistence, analytics or review.

Key differences:

| Aspect | ExerciseSession | ExerciseAttempt |
|------|-----------------|-----------------|
| Purpose | Drive live interaction | Persist final result |
| Mutability | Mutable | Immutable |
| Lifetime | Short-lived | Long-lived |
| Persistence | Session storage / cache | Database |
| Step-by-step state | Yes | No |
| Final score / summary | Optional | Mandatory |

In early migration slices, the system focuses exclusively on `ExerciseSession`.
`ExerciseAttempt` is introduced only after server-side evaluation and result persistence are implemented.

In early migration slices, step progression is explicitly stored in the
ExerciseSession (`currentStep`) instead of being derived from previous
evaluations. This keeps the control flow simple, predictable and easy to debug.

### 7.1 Core concepts (Domain model)
- **ExerciseType**
  - A semantic identifier (slug) for each exercise family (e.g., `fill-the-blanks`, `mock-exam`).
- **ExerciseConfig**
  - Immutable configuration chosen by the user before starting (topic selection, difficulty, boolean flags).
  - Example fields:
    - `topicOrder`, `difficulty`
    - `flags` (map/DTO): `numeracion`, `apartado`, `ciclos`, `leyes`, `modulos`, etc.
- **ExerciseSession**
  - Server-side state container created when the exercise starts.
  - Holds:
    - `sessionId`
    - `userContext` (user + opposition)
    - `config` (ExerciseConfig)
    - `currentStep`
    - optional: `answersByStep`, `evaluationByStep`, timestamps
- **ExerciseStep**
  - Enumeration of wizard steps (for `fill-the-blanks`):
    - `title`, `index`, `justification`, `quotes`, `tools`, `schoolContext`, `workContext`, `bibliography`, `webography`
- **StepPayload**
  - Data needed to render a given step (loaded from repositories).
  - Does not include HTML; pure data structures (lists, nested structures).
  - MUST NOT be exposed to the view layer under any circumstance.
- **StepAnswer**
  - User-submitted values for a step (DTO).
- **StepEvaluation**
  - Evaluation result (per field correctness + optional similarity score + messages).

### 7.2 Domain services
- **HintService / MaskingService**
  - Implements the legacy behavior of generating hints based on difficulty:
    - `getHint(value, difficulty, mode)` where mode is `letters|words`.
- **Evaluator**
  - Evaluates a step by comparing `StepAnswer` vs expected values from payload.
  - Uses strategies:
    - `EqualityEvaluator` (case-insensitive exact match)
    - `SimilarityEvaluator` (threshold-based for longer texts)
  - Output: `StepEvaluation` (field-level correctness + overall status).
- **StepBuilder**
  - Builds `StepPayload` for a given step using repositories.

### 7.3 Persistence / Infrastructure
- **Repositories (read models)**
  - `TopicRepository`
  - `ApartadoRepository`
  - `QuoteRepository`
  - `ToolRepository`
  - `SchoolContextRepository`
  - `WorkContextRepository`
  - `BookRepository`
  - `WebRepository`
- **Session storage**
  - Phase 1 (simple): store `ExerciseSession` in PHP session (`$_SESSION`) keyed by `sessionId`.
  - Phase 2 (robust): persist sessions/attempts in DB (`exercise_attempt`, `exercise_answer`, etc.).

### 7.4 Controllers (MVC)
#### Configuration / start
- `ExercisesConfigController`
  - `GET /exercises/{exerciseType}/config`
  - `POST /exercises/{exerciseType}/start`
  - Responsibilities:
    - render config form
    - validate config
    - create `ExerciseSession`
    - redirect to first step

#### Wizard steps
- `ExerciseWizardController`
  - `GET /exercises/{exerciseType}/sessions/{sessionId}/steps/{step}`
  - `POST /exercises/{exerciseType}/sessions/{sessionId}/steps/{step}/evaluate`
  - `POST /exercises/{exerciseType}/sessions/{sessionId}/steps/{step}/reset`
  - `GET /exercises/{exerciseType}/sessions/{sessionId}/summary`
  - Responsibilities:
    - load session + enforce step order (gating)
    - build payload for the step
    - render step view
    - evaluate posted answers (PRG)
    - store evaluation results (session or DB)
    - decide next step

### 7.5 Views (per exercise type)
- `views/exercises/{exerciseType}/config.php`
- `views/exercises/{exerciseType}/steps/{step}.php`
- `views/exercises/{exerciseType}/summary.php`

Views should be dumb:
- render payload + input fields
- display evaluation feedback
- no DB access
- no navigation logic beyond linking to controller routes

### 7.6 Routes summary (initial)
- `GET  /exercises/fill-the-blanks/config`
- `POST /exercises/fill-the-blanks/start`
- `GET  /exercises/fill-the-blanks/sessions/{sessionId}/steps/{step}`
- `POST /exercises/fill-the-blanks/sessions/{sessionId}/steps/{step}/evaluate`
- `POST /exercises/fill-the-blanks/sessions/{sessionId}/steps/{step}/reset`
- `GET  /exercises/fill-the-blanks/sessions/{sessionId}/summary`

### 7.7 Transition strategy (compatibility with legacy)
- Keep legacy scripts working as-is under existing URLs.
- Introduce MVC routes under a new prefix (`/exercises/...`) to avoid collisions.
- Start with one step migrated (recommended: `titulo` or `indice`) to validate:
  - session state handling
  - step payload building
  - evaluator correctness
- During transition:
  - reuse legacy CSS (`miEstilo.css`) for consistent UI
  - keep client-side JS minimal (optional), do not embed solutions in HTML for MVC screens
- Add feature flags:
  - per exercise type or per step: legacy vs MVC rendering.

### 7.8 Folder / class layout proposal

```
/public
  index.php

/legacy
  menu.php
  titulo.php
  indice.php
  justificacion.php
  citas.php
  herramientas.php
  contextoEscolar.php
  contextoLaboral.php
  bibliografia.php
  webgrafia.php

/src
  /Controllers
    ExercisesConfigController.php
    ExerciseWizardController.php

  /Domain
    /Exercise
      ExerciseType.php
      ExerciseStep.php
      ExerciseConfig.php
      ExerciseSession.php
      StepPayload.php
      StepAnswer.php
      StepEvaluation.php

    /Services
      HintService.php
      Evaluator.php
      /Evaluation
        EqualityEvaluator.php
        SimilarityEvaluator.php

  /Infrastructure
    /Persistence
      DbConnection.php
      /Repositories
        TopicRepository.php
        ApartadoRepository.php
        QuoteRepository.php
        ToolRepository.php
        SchoolContextRepository.php
        WorkContextRepository.php
        BookRepository.php
        WebRepository.php

    /Session
      ExerciseSessionStore.php

  /Routing
    Router.php
    Routes.php

/views
  /exercises
    /fill-the-blanks
      config.php
      summary.php
      /steps
        title.php
        index.php
        justification.php
        quotes.php
        tools.php
        school-context.php
        work-context.php
        bibliography.php
        webography.php

/docs
  exercises-mvc-design.md

```

### 7.9 Minimal data structures (DTOs / shapes)

The following DTO shapes capture the minimum data needed for the MVC flow.

#### ExerciseType
- `slug: string` (e.g., `fill-the-blanks`)
- `name: string`

#### ExerciseConfig
- `topicOrder: int | 0` (0 = random)
- `difficulty: int` (1..4)
- `flags: map<string, bool>`
  - Example keys:
    - `numeracion`, `apartado`, `ciclos`, `leyes`, `modulos`
    - `conceptoCita`, `autorCita`, `anyoCita`, `cita`, `numeracionCita`, `apartadoCita`
    - `herramienta`, `descripcionHerramienta`
    - `ensenyanza`, `ciclosContexto`, `modulosContexto`, `conceptoContextoEscolar`, `aplicacionContextoEscolar`, `metodo`
    - `campo`, `profesional`, `conceptoContextoLaboral`, `aplicacionContextoLaboral`, `beneficio`
    - `autorLibro`, `anyoLibro`, `tituloLibro`, `editorial`
    - `nombreWeb`, `url`

#### ExerciseSession
- `sessionId: string`
- `exerciseType: ExerciseType`
- `userContext`
  - `userName: string`
  - `oppositionId: string`
- `config: ExerciseConfig`
- `currentStep: ExerciseStep`
- `answersByStep: map<ExerciseStep, StepAnswer>` (optional)
- `evaluationByStep: map<ExerciseStep, StepEvaluation>` (optional)
- `createdAt: datetime`
- `updatedAt: datetime`

#### ExerciseStep (enum)
- `title`
- `index`
- `justification`
- `quotes`
- `tools`
- `schoolContext`
- `workContext`
- `bibliography`
- `webography`

#### StepPayload (generic wrapper)
- `step: ExerciseStep`
- `items: list` (step-specific)
- `meta`
  - `topicOrder: int`
  - `topicTitle: string`
  - `difficulty: int`
  - `flags: map<string, bool>`
  - `hintMode: "letters" | "words"` (optional by field)
- `expected: list` (server-only; not rendered into HTML in MVC)

Step-specific `items` examples:

- **Title step**
  - items: `[ { key: "topicTitle", label: "Título", hint, expectedValue } ]`

- **Index step**
  - items: `[ { sectionOrder, sectionTitle, evaluable: { order: bool, title: bool }, hints } ]`

- **Justification step**
  - items: `[ { cycleName, laws: [...], modules: [...], evaluable: {...}, hints } ]`

- **Quotes step**
  - items: `[ { concept, author, year, sectionOrder, sectionTitle, content, evaluable: {...}, hints } ]`

- **Tools step**
  - items: `[ { toolName, description?, evaluable: {...}, hints } ]`

- **Contexts**
  - items: `[ { teaching, cycle?, module?, concept, application, method?, evaluable: {...}, hints } ]` (school)
  - items: `[ { field, professional?, concept, task, benefit, evaluable: {...}, hints } ]` (work)

- **Bibliography**
  - items: `[ { author, year, title, editorial, evaluable: {...}, hints } ]`

- **Webography**
  - items: `[ { name, url, evaluable: {...}, hints } ]`

#### StepAnswer
- `step: ExerciseStep`
- `values: map<string, string>`
  - Key format recommendation:
    - include row index + field name, e.g. `item0.title`, `item3.author`, `item2.url`
  - For nested steps (justification):
    - include path: `cycle0.law1.name`, `cycle0.module2.name`

#### StepEvaluation
- `step: ExerciseStep`
- `fieldResults: map<string, FieldResult>`
- `isStepCorrect: bool`
- `score: float` (optional)
- `createdAt: datetime`

#### FieldResult
- `isCorrect: bool`
- `expected: string` (optional; show only after “Solución” or teacher mode)
- `actual: string`
- `strategy: "equality" | "similarity"`
- `similarityScore: float | null`
- `message: string | null`

### 7.10 First MVC slice (incremental migration plan)

Goal: migrate a single wizard step end-to-end (MVC route → controller → view → evaluation → gating) while keeping the legacy flow untouched.

#### Recommended first step: `title` (topic title)
Rationale:
- Minimal UI: one user input.
- Minimal payload: topic title.
- Validates the full MVC pipeline: session, payload building, evaluation, PRG/gating, view feedback.

---

## Slice 1 — Routes

- `GET  /exercises/fill-the-blanks/config`
- `POST /exercises/fill-the-blanks/start`
- `GET  /exercises/fill-the-blanks/sessions/{sessionId}/steps/title`
- `POST /exercises/fill-the-blanks/sessions/{sessionId}/steps/title/evaluate`

Notes:
- Legacy URLs remain available and unchanged.
- New MVC endpoints live under `/exercises/...` prefix.
- The reset endpoint is intentionally deferred to a later slice to keep the initial migration scope minimal.

---

## Slice 2 — Controllers (minimal methods)

### `ExercisesConfigController`
- `show()` → renders config page (topic, difficulty, flags)
- `start()` → validates input, creates `ExerciseSession`, redirects to step `title`

### `ExerciseWizardController`
- `showStep(sessionId, step)` → renders the step view with payload + prior evaluation (if any)
- `evaluateStep(sessionId, step)` → evaluates answers, stores evaluation in session, redirects back to `showStep` (PRG)

---

## Slice 3 — Session state (Phase 1)
- Implement `ExerciseSessionStore` using `$_SESSION`:
  - `createSession(config, userContext) -> sessionId`
  - `get(sessionId) -> ExerciseSession`
  - `save(session)`

Store only what you need:
- `sessionId`
- `exerciseType`
- `config` (topicOrder, difficulty, flags)
- `currentStep` (start at `title`)
- `evaluationByStep['title']` (after evaluate)

---

## Slice 4 — Payload building

Implement `StepBuilder::buildTitlePayload(session)`:
- Determine topic:
  - If `topicOrder == 0` → pick random topic order from DB.
- Load:
  - topic title (expected answer)
  - topic display header (optional)
- Build payload:
  - `items[0] = { key: "topicTitle", label: "Título", hint }`
- DO NOT embed expected values in HTML.

---

## Slice 5 — Evaluation

Implement `Evaluator::evaluateTitle(payload, answer)`:
- Strategy: equality (case-insensitive).
- Output:
  - `FieldResult` for `topicTitle`
  - `isStepCorrect = true/false`

Optional later:
- add similarity strategy for long text fields (quotes/contexts).

---

## Slice 6 — Views (minimal)

### `views/exercises/fill-the-blanks/config.php`
- Form fields:
  - topic selector (including “random”)
  - difficulty selector
  - flags checkboxes (optional in slice 1: you can start with a reduced set)

### `views/exercises/fill-the-blanks/steps/title.php`
- Render:
  - step header (topic info)
  - one input: `name="topicTitle"`
  - “Check” submit button (POST evaluate)
- Render feedback:
  - field-level correct/incorrect styling (reuse CSS)
  - show “Continue” link/button only when `isStepCorrect == true`

---

## Slice 7 — Gating behavior (server-driven)
Legacy gating is client-side. In MVC slice:
- The step page should render “Continue” only if evaluation is correct.
- Otherwise, no continue link is shown (or keep it disabled).

---

## Definition of Done (Slice 1)
- User can:
  - open MVC config
  - start a session
  - see title step
  - submit an answer
  - receive correctness feedback
  - continue only when correct
- No legacy script is modified.
- No solutions are shipped to the browser as hidden inputs.

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