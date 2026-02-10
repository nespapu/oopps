<?php
/**
 * Payload esperado:
 * - $temas: array of ['valor' => int, 'etiqueta' => string]
 * - $gradosDificultad: array of ['valor' => int, 'etiqueta' => string]
 * - $defecto: array ['tema' => int, 'gradoDificultad' => int]
 * - $error: (opcional) string
 * - $titulo: (opcional) string
 */

$temas = $payload['temas'] ?? [];
$gradosDificultad = $payload['gradosDificultad'] ?? [];
$defecto = $payload['defecto'] ?? ['tema' => 0, 'gradoDificultad' => 3];
$error = $payload['error'] ?? null;
$titulo = $payload['titulo'] ?? 'Configuración';

$temaSeleccionado = (int)($defecto['tema'] ?? 0);
$gradoDificultadSeleccionado = (int)($defecto['gradoDificultad'] ?? 3);


$formularioAccion = $url->to($cuantoSabesTemaPaths->inicio());

$escapar = static fn(string $valor): string =>
    htmlspecialchars($valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$buscarEtiqueta = static function (array $opciones, int $valor): string {
    foreach ($opciones as $opc) {
        if ((int)($opc['valor'] ?? -1) === $valor) {
            return (string)($opc['etiqueta'] ?? '');
        }
    }
    return '';
};

$etiquetaTemaSeleccionado = $buscarEtiqueta($temas, $temaSeleccionado);
$etiquetaGradoDificultadSeleccionado = $buscarEtiqueta($gradosDificultad, $gradoDificultadSeleccionado);
?>

<div class="row justify-content-center">
    <div class="col-12 col-lg-10">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="h3 mb-1">Cuánto sabes del tema</h1>
                <p class="text-muted mb-0">Configura el ejercicio antes de comenzar.</p>
            </div>
            <span class="badge text-bg-primary">Paso 0 · Configuración</span>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger d-flex align-items-start" role="alert">
                <div class="me-2">⚠️</div>
                <div><?= $escapar((string)$error) ?></div>
            </div>
        <?php endif; ?>

        <div class="row g-3">
            <!-- Main card -->
            <div class="col-12 col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <div class="d-flex align-items-center">
                            <div class="me-2">🧩</div>
                            <div>
                                <div class="fw-semibold">Opciones del ejercicio</div>
                                <div class="text-muted small">Elige tema y dificultad.</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <form method="POST" action="<?= $escapar($formularioAccion) ?>" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="topicOrder" class="form-label">Tema</label>
                                <select
                                    id="topicOrder"
                                    name="numeracionTema"
                                    class="form-select"
                                    required
                                >
                                    <?php foreach ($temas as $opc): ?>
                                        <?php
                                            $valor = (int)($opc['valor'] ?? 0);
                                            $etiqueta = (string)($opc['etiqueta'] ?? '');
                                            $estaSeleccionado = ($valor === $temaSeleccionado);
                                        ?>
                                        <option value="<?= $valor ?>" <?= $estaSeleccionado ? 'selected' : '' ?>>
                                            <?= $escapar($etiqueta) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">
                                    Selecciona un tema concreto o <strong>Aleatorio</strong> para escoger uno al azar.
                                </div>
                                <div class="invalid-feedback">
                                    Debes seleccionar un tema.
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="difficulty" class="form-label">Dificultad</label>
                                <select
                                    id="difficulty"
                                    name="dificultad"
                                    class="form-select"
                                    required
                                >
                                    <?php foreach ($gradosDificultad as $opc): ?>
                                        <?php
                                            $valor = (int)($opc['valor'] ?? 0);
                                            $etiqueta = (string)($opc['etiqueta'] ?? '');
                                            $estaSeleccionado = ($valor === $gradoDificultadSeleccionado);
                                        ?>
                                        <option value="<?= $valor ?>" <?= $estaSeleccionado ? 'selected' : '' ?>>
                                            <?= $escapar($etiqueta) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">
                                    Ajusta el nivel de exigencia del ejercicio.
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

                                <a class="btn btn-outline-secondary"
                                    href="<?= $escapar($url->to('/panel-control-ejercicios')) ?>">
                                    Volver
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Side summary -->
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
                                <?= $escapar($etiquetaTemaSeleccionado !== '' ? $etiquetaTemaSeleccionado : 'Aleatorio') ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="text-muted small">Dificultad</div>
                            <div class="fw-semibold">
                                <?= $escapar($etiquetaGradoDificultadSeleccionado !== '' ? $etiquetaGradoDificultadSeleccionado : '—') ?>
                            </div>
                        </div>

                        <div class="alert alert-light border mb-0">
                            <div class="small text-muted mb-1">Siguiente paso</div>
                            <div class="fw-semibold">Título</div>
                            <div class="small text-muted">Tras iniciar, te redirigimos al primer paso del ejercicio.</div>
                        </div>
                    </div>
                </div>

                <div class="text-muted small mt-3">
                    Consejo: si eliges <strong>Aleatorio</strong>, el sistema seleccionará un tema válido para tu oposición.
                </div>
            </div>
        </div>
    </div>
</div>
