<?php
$topics = $payload['topics'] ?? [];
$difficultyLevels = $payload['difficultyLevels'] ?? [];
$defaults = $payload['defaults'] ?? ['topicOrder' => 0, 'difficulty' => 3];

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