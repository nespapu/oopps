<?php

declare(strict_types=1);

/** @var UrlGenerator $url */
/** @var Paths $howMuchDoYouKnowPaths */

$title = 'Cuánto sabes del tema';

$topics = $payload['topics'] ?? [];
$difficultyLevels = $payload['difficultyLevels'] ?? [];
$defaults = $payload['defaults'] ?? ['topicOrder' => 0, 'difficulty' => 3];
$flags = $payload['flags'] ?? [];

$error = $payload['error'] ?? null;

$selectedTopicOrder = (int) ($defaults['topicOrder'] ?? 0);
$selectedDifficulty = (int) ($defaults['difficulty'] ?? 3);

$formAction = $url->to($howMuchDoYouKnowPaths->start());

$escape = static fn(string $value): string =>
    htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$findLabel = static function (array $options, int $value): string {
    foreach ($options as $option) {
        if ((int) ($option['value'] ?? -1) === $value) {
            return (string) ($option['label'] ?? '');
        }
    }
    return '';
};

$selectedTopicLabel = $findLabel($topics, $selectedTopicOrder);
$selectedDifficultyLabel = $findLabel($difficultyLevels, $selectedDifficulty);
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="h3 mb-1">Cuánto sabes del tema</h1>
                <p class="text-muted mb-0">Configura el ejercicio antes de empezar.</p>
            </div>
            <span class="badge text-bg-primary">Paso 0 · Configuración</span>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-start" role="alert">
                <div class="me-2">⚠️</div>
                <div><?= $escape((string) $error) ?></div>
            </div>
        <?php endif; ?>

        <div class="row g-3">
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <div class="d-flex align-items-center">
                            <div class="me-2">🧩</div>
                            <div>
                                <div class="fw-semibold">Opciones del ejercicio</div>
                                <div class="text-muted small">Elige el tema y la dificultad.</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="<?= $escape($formAction) ?>" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="topicOrder" class="form-label">Tema</label>
                                <select id="topicOrder" name="topicOrder" class="form-select" required>
                                    <?php foreach ($topics as $option): ?>
                                        <?php
                                            $value = (int) ($option['value'] ?? 0);
                                            $label = (string) ($option['label'] ?? '');
                                            $isSelected = ($value === $selectedTopicOrder);
                                        ?>
                                        <option value="<?= $value ?>" <?= $isSelected ? 'selected' : '' ?>>
                                            <?= $escape($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">
                                    Selecciona un tema concreto o elige <strong>Aleatorio</strong>.
                                </div>
                                <div class="invalid-feedback">
                                    Debes seleccionar un tema.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="difficulty" class="form-label">Dificultad</label>
                                <select id="difficulty" name="difficulty" class="form-select" required>
                                    <?php foreach ($difficultyLevels as $option): ?>
                                        <?php
                                            $value = (int) ($option['value'] ?? 0);
                                            $label = (string) ($option['label'] ?? '');
                                            $isSelected = ($value === $selectedDifficulty);
                                        ?>
                                        <option value="<?= $value ?>" <?= $isSelected ? 'selected' : '' ?>>
                                            <?= $escape($label) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">
                                    Ajusta el nivel de reto.
                                </div>
                                <div class="invalid-feedback">
                                    Debes seleccionar una dificultad.
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="mb-4">
                                <h2 class="h5 mb-2">Índice</h2>
                                <p class="text-muted small mb-3">
                                    Configura qué partes del índice se mostrarán resueltas durante el ejercicio.
                                </p>
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="sectionOrder"
                                        name="flags[sectionOrder]"
                                        value="1"
                                        <?= ($flags['sectionOrder'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="sectionOrder">
                                        Numeración
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, la numeración de los apartados aparecerá visible.
                                    </div>
                                </div>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="sectionTitle"
                                        name="flags[sectionTitle]"
                                        value="1"
                                        <?= ($flags['sectionTitle'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="sectionTitle">
                                        Apartado
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, el título de los apartados aparecerá visible.
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="mb-4">
                                <h2 class="h5 mb-2">Justificación</h2>
                                <p class="text-muted small mb-3">
                                    Configura qué elementos de la justificación se mostrarán resueltos durante el ejercicio.
                                </p>
                                                                
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="cycles"
                                        name="flags[cycles]"
                                        value="1"
                                        <?= ($flags['cycles'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="cycles">
                                        Ciclos
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, los ciclos aparecerán visibles.
                                    </div>
                                </div>
                                                                
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="laws"
                                        name="flags[laws]"
                                        value="1"
                                        <?= ($flags['laws'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="laws">
                                        Leyes
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, las leyes aparecerán visibles.
                                    </div>
                                </div>
                                                                
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="modules"
                                        name="flags[modules]"
                                        value="1"
                                        <?= ($flags['modules'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="modules">
                                        Módulos
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, los módulos aparecerán visibles.
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="mb-4">
                                <h2 class="h5 mb-2">Citas</h2>
                                <p class="text-muted small mb-3">
                                    Configura qué elementos de las citas se mostrarán resueltos durante el ejercicio.
                                </p>

                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="quoteConcept"
                                        name="flags[quoteConcept]"
                                        value="1"
                                        <?= ($flags['quoteConcept'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="quoteConcept">
                                        Concepto
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, el concepto de la cita aparecerá visible.
                                    </div>
                                </div>

                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="quoteAuthor"
                                        name="flags[quoteAuthor]"
                                        value="1"
                                        <?= ($flags['quoteAuthor'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="quoteAuthor">
                                        Autor
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, el autor de la cita aparecerá visible.
                                    </div>
                                </div>

                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="quoteYear"
                                        name="flags[quoteYear]"
                                        value="1"
                                        <?= ($flags['quoteYear'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="quoteYear">
                                        Año
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, el año de la cita aparecerá visible.
                                    </div>
                                </div>

                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="quoteContent"
                                        name="flags[quoteContent]"
                                        value="1"
                                        <?= ($flags['quoteContent'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="quoteContent">
                                        Contenido
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, el contenido de la cita aparecerá visible.
                                    </div>
                                </div>

                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="quoteSectionOrder"
                                        name="flags[quoteSectionOrder]"
                                        value="1"
                                        <?= ($flags['quoteSectionOrder'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="quoteSectionOrder">
                                        Numeración del apartado
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, la numeración del apartado aparecerá visible.
                                    </div>
                                </div>

                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="quoteSectionTitle"
                                        name="flags[quoteSectionTitle]"
                                        value="1"
                                        <?= ($flags['quoteSectionTitle'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="quoteSectionTitle">
                                        Título del apartado
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, el título del apartado aparecerá visible.
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="mb-4">
                                <h2 class="h5 mb-2">Herramientas</h2>
                                <p class="text-muted small mb-3">
                                    Configura qué elementos de las herramientas se mostrarán resueltos durante el ejercicio.
                                </p>

                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="toolName"
                                        name="flags[toolName]"
                                        value="1"
                                        <?= ($flags['toolName'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="toolName">
                                        Nombre
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, el nombre de la herramienta aparecerá visible.
                                    </div>
                                </div>

                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="toolDescription"
                                        name="flags[toolDescription]"
                                        value="1"
                                        <?= ($flags['toolDescription'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="toolDescription">
                                        Descripción
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, la descripción de la herramienta aparecerá visible.
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="mb-4">
                                <h2 class="h5 mb-2">Contexto escolar</h2>
                                <p class="text-muted small mb-3">
                                    Configura qué elementos del contexto escolar se mostrarán resueltos durante el ejercicio.
                                </p>
                                                                
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="schoolContextTeaching"
                                        name="flags[schoolContextTeaching]"
                                        value="1"
                                        <?= ($flags['schoolContextTeaching'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="schoolContextTeaching">
                                        Enseñanza
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, la enseñanza aparecerá visible.
                                    </div>
                                </div>
                                                                
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="schoolContextCycle"
                                        name="flags[schoolContextCycle]"
                                        value="1"
                                        <?= ($flags['schoolContextCycle'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="schoolContextCycle">
                                        Ciclo
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, el ciclo aparecerá visible.
                                    </div>
                                </div>
                                                                
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="schoolContextModule"
                                        name="flags[schoolContextModule]"
                                        value="1"
                                        <?= ($flags['schoolContextModule'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="schoolContextModule">
                                        Módulo
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, el módulo aparecerá visible.
                                    </div>
                                </div>
                                                                
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="schoolContextConcept"
                                        name="flags[schoolContextConcept]"
                                        value="1"
                                        <?= ($flags['schoolContextConcept'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="schoolContextConcept">
                                        Concepto
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, el concepto aparecerá visible.
                                    </div>
                                </div>
                                                                
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="schoolContextApplication"
                                        name="flags[schoolContextApplication]"
                                        value="1"
                                        <?= ($flags['schoolContextApplication'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="schoolContextApplication">
                                        Aplicación
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, la aplicación del concepto aparecerá visible.
                                    </div>
                                </div>
                                                                
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="schoolContextMethod"
                                        name="flags[schoolContextMethod]"
                                        value="1"
                                        <?= ($flags['schoolContextMethod'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="schoolContextMethod">
                                        Metodología
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, la metodología aparecerá visible.
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            
                            <div class="mb-4">
                                <h2 class="h5 mb-1">Contexto laboral</h2>
                                <p class="text-muted small mb-3">
                                    Selecciona qué campos del contexto laboral aparecerán visibles durante el ejercicio.
                                </p>
                                                                
                                <div class="form-check mb-3">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="workContextField"
                                        name="flags[workContextField]"
                                        value="1"
                                        <?= ($flags['workContextField'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="workContextField">
                                        Campo profesional
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, el campo profesional aparecerá visible.
                                    </div>
                                </div>
                                                                
                                <div class="form-check mb-3">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="workContextRole"
                                        name="flags[workContextRole]"
                                        value="1"
                                        <?= ($flags['workContextRole'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="workContextRole">
                                        Rol profesional
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, el rol profesional aparecerá visible cuando sea aplicable.
                                    </div>
                                </div>
                                                                
                                <div class="form-check mb-3">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="workContextConcept"
                                        name="flags[workContextConcept]"
                                        value="1"
                                        <?= ($flags['workContextConcept'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="workContextConcept">
                                        Concepto
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, el concepto aparecerá visible.
                                    </div>
                                </div>
                                                                
                                <div class="form-check mb-3">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="workContextApplication"
                                        name="flags[workContextApplication]"
                                        value="1"
                                        <?= ($flags['workContextApplication'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="workContextApplication">
                                        Aplicación o tareas
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, la aplicación o las tareas relacionadas aparecerán visibles.
                                    </div>
                                </div>
                                                                
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="workContextBenefit"
                                        name="flags[workContextBenefit]"
                                        value="1"
                                        <?= ($flags['workContextBenefit'] ?? false) ? 'checked' : '' ?>
                                    >
                                    <label class="form-check-label" for="workContextBenefit">
                                        Beneficio
                                    </label>
                                    <div class="form-text">
                                        Si está marcado, el beneficio obtenido aparecerá visible.
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                                                                
                            <div class="mb-4">
                                <h2 class="h5 mb-2">Bibliografía</h2>
                                                                
                                <p class="text-muted small mb-3">
                                    Configura qué elementos de las referencias bibliográficas se mostrarán
                                    resueltos durante el ejercicio.
                                </p>
                                                                
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="bookAuthor"
                                        name="flags[bookAuthor]"
                                        value="1"
                                        <?= ($flags['bookAuthor'] ?? false) ? 'checked' : '' ?>
                                    >
                                                                
                                    <label class="form-check-label" for="bookAuthor">
                                        Autor
                                    </label>
                                                                
                                    <div class="form-text">
                                        Si está marcado, el autor de la referencia bibliográfica aparecerá
                                        visible.
                                    </div>
                                </div>
                                                                
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="bookPublicationYear"
                                        name="flags[bookPublicationYear]"
                                        value="1"
                                        <?= ($flags['bookPublicationYear'] ?? false) ? 'checked' : '' ?>
                                    >
                                                                
                                    <label class="form-check-label" for="bookPublicationYear">
                                        Año de publicación
                                    </label>
                                                                
                                    <div class="form-text">
                                        Si está marcado, el año de publicación aparecerá visible.
                                    </div>
                                </div>
                                                                
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="bookPublisher"
                                        name="flags[bookPublisher]"
                                        value="1"
                                        <?= ($flags['bookPublisher'] ?? false) ? 'checked' : '' ?>
                                    >
                                                                
                                    <label class="form-check-label" for="bookPublisher">
                                        Editorial
                                    </label>
                                                                
                                    <div class="form-text">
                                        Si está marcado, la editorial de la referencia bibliográfica
                                        aparecerá visible.
                                    </div>
                                </div>
                                                                
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="bookTitle"
                                        name="flags[bookTitle]"
                                        value="1"
                                        <?= ($flags['bookTitle'] ?? false) ? 'checked' : '' ?>
                                    >
                                                                
                                    <label class="form-check-label" for="bookTitle">
                                        Título
                                    </label>
                                                                
                                    <div class="form-text">
                                        Si está marcado, el título de la obra aparecerá visible.
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                                                                
                            <div class="mb-4">
                                <h2 class="h5 mb-2">Webgrafía</h2>
                                                                
                                <p class="text-muted small mb-3">
                                    Configura qué elementos de la webgrafía se mostrarán resueltos durante el ejercicio.
                                </p>
                                                                
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="websiteName"
                                        name="flags[websiteName]"
                                        value="1"
                                        <?= ($flags['websiteName'] ?? false) ? 'checked' : '' ?>
                                    >
                                                                
                                    <label class="form-check-label" for="websiteName">
                                        Nombre del sitio web
                                    </label>
                                                                
                                    <div class="form-text">
                                        Si está marcado, el nombre del sitio web aparecerá visible.
                                    </div>
                                </div>
                                                                
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="websiteURL"
                                        name="flags[websiteURL]"
                                        value="1"
                                        <?= ($flags['websiteURL'] ?? false) ? 'checked' : '' ?>
                                    >
                                                                
                                    <label class="form-check-label" for="websiteURL">
                                        URL
                                    </label>
                                                                
                                    <div class="form-text">
                                        Si está marcado, la URL del sitio web aparecerá visible.
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    Empezar
                                </button>

                                <!-- Si tienes Paths del dashboard, úsalo aquí -->
                                <a class="btn btn-outline-secondary" href="<?= $escape($url->to('/panel-control-ejercicios')) ?>">
                                    Volver
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <div class="fw-semibold">Resumen</div>
                        <div class="text-muted small">Lo que vas a iniciar.</div>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="text-muted small">Tema</div>
                            <div class="fw-semibold">
                                <?= $escape($selectedTopicLabel !== '' ? $selectedTopicLabel : 'Aleatorio') ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="text-muted small">Dificultad</div>
                            <div class="fw-semibold">
                                <?= $escape($selectedDifficultyLabel !== '' ? $selectedDifficultyLabel : '—') ?>
                            </div>
                        </div>

                        <div class="alert alert-light border mb-0">
                            <div class="small text-muted mb-1">Siguiente paso</div>
                            <div class="fw-semibold">Título</div>
                            <div class="small text-muted">Al empezar, pasarás al paso 1.</div>
                        </div>
                    </div>
                </div>

                <div class="text-muted small mt-3">
                    Consejo: si eliges <strong>Aleatorio</strong>, el sistema escogerá un tema válido para tu oposición.
                </div>
            </div>
        </div>
    </div>
</div>