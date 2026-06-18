<?php

/** @var array<string, mixed> $payload */
/** @var string $sessionId */
/** @var array<string, mixed>|null $evaluation */
/** @var \App\Application\Routing\UrlGenerator $url */
/** @var \App\App\Routing\HowMuchDoYouKnow\Paths $howMuchDoYouKnowPaths */

use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;

$title = 'Cuánto sabes del tema';

$items = $payload[StepPayloadKeys::ITEMS] ?? [];
$meta  = $payload[StepPayloadKeys::META] ?? [];

$topicOrder = $meta['topicOrder'] ?? '';
$difficulty = $meta['difficulty'] ?? '';
$flags      = $meta['flags'] ?? [];
$evaluable  = $meta['evaluable'] ?? [];

$isSectionOrderEvaluable = (bool)($evaluable['sectionOrder'] ?? false);
$isSectionTitleEvaluable = (bool)($evaluable['sectionTitle'] ?? false);

$isStepCorrect = null;

if (is_array($evaluation)) {
    $isStepCorrect = $evaluation['result']['isStepCorrect'] ?? null;
}

$action = $url->to($howMuchDoYouKnowPaths->indexEvaluation($sessionId));
$nextUrl = $url->to($howMuchDoYouKnowPaths->justificationStep($sessionId));

$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
?>

<div class="row justify-content-center">
  <div class="col-12 col-xl-10">

    <div class="d-flex align-items-start justify-content-between mb-3">
      <div>
        <h1 class="h4 mb-1">Cuánto sabes del tema</h1>
        <div class="text-muted">
          Paso: <span class="fw-semibold">Índice</span>
        </div>
      </div>

      <div class="text-end">
        <span class="badge text-bg-primary me-1">
          Tema <?= $e($topicOrder) ?>
        </span>

        <?php if ($difficulty !== ''): ?>
          <span class="badge text-bg-secondary">
            Dificultad <?= $e($difficulty) ?>
          </span>
        <?php endif; ?>
      </div>
    </div>

    <div class="mb-3">
      <?php if (isset($isStepCorrect)): ?>
        <?php if ($isStepCorrect): ?>
          <div class="alert alert-success d-flex align-items-center" role="alert">
            <div class="me-2">✅</div>
            <div><strong>¡Correcto!</strong> Puedes continuar al siguiente paso.</div>
          </div>
        <?php else: ?>
          <div class="alert alert-danger d-flex align-items-center" role="alert">
            <div class="me-2">❌</div>
            <div><strong>No es correcto.</strong> Revisa la numeración y los apartados.</div>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">

        <div class="mb-4">
          <h2 class="h5 mb-1">Completa el índice del tema</h2>
          <p class="text-muted mb-0">
            Rellena los campos que aparecen vacíos. Los campos ya resueltos forman parte de la configuración elegida.
          </p>
        </div>

        <form method="post" action="<?= $e($action) ?>">

          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th style="width: 18%">Numeración</th>
                  <th>Apartado</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($items as $item): ?>
                  <?php
                    $key = $item['key'] ?? '';
                    $sectionOrder = $item['sectionOrder'] ?? '';
                    $sectionTitle = $item['sectionTitle'] ?? '';
                    $hints = $item['hints'] ?? [];

                    $sectionOrderHint = $hints['sectionOrder'] ?? '';
                    $sectionTitleHint = $hints['sectionTitle'] ?? '';

                    $sectionOrderInputId = $key . '_sectionOrder';
                    $sectionTitleInputId = $key . '_sectionTitle';
                  ?>

                  <tr>
                    <td>
                      <?php if ($isSectionOrderEvaluable): ?>
                        <label class="visually-hidden" for="<?= $e($sectionOrderInputId) ?>">
                          Numeración
                        </label>

                        <input
                          type="text"
                          class="form-control"
                          id="<?= $e($sectionOrderInputId) ?>"
                          name="<?= $e($key) ?>[sectionOrder]"
                          placeholder="<?= $e($sectionOrderHint) ?>"
                          autocomplete="off"
                          required
                        >
                         <div class="alert alert-info mt-2 mb-0 py-2 <?= $sectionOrderHint === '' ? 'invisible' : '' ?>">
                            <span class="fw-semibold">Pista:</span>
                            <?= $e($sectionOrderHint) ?>
                        </div>         
                      <?php else: ?>
                        <input
                          type="text"
                          class="form-control bg-light"
                          value="<?= $e($sectionOrder) ?>"
                          readonly
                        >
                        <div class="alert alert-info mt-2 mb-0 py-2 invisible">
                            <span class="fw-semibold">Pista:</span>
                            &nbsp;
                        </div>
                      <?php endif; ?>
                    </td>

                    <td>
                      <?php if ($isSectionTitleEvaluable): ?>
                        <label class="visually-hidden" for="<?= $e($sectionTitleInputId) ?>">
                          Apartado
                        </label>

                        <input
                          type="text"
                          class="form-control"
                          id="<?= $e($sectionTitleInputId) ?>"
                          name="<?= $e($key) ?>[sectionTitle]"
                          placeholder="<?= $e($sectionTitleHint) ?>"
                          autocomplete="off"
                          required
                        >
                        <div class="alert alert-info mt-2 mb-0 py-2 <?= $sectionTitleHint === '' ? 'invisible' : '' ?>">
                            <span class="fw-semibold">Pista:</span>
                            <?= $e($sectionTitleHint) ?>
                        </div>
                      <?php else: ?>
                        <input
                          type="text"
                          class="form-control bg-light"
                          value="<?= $e($sectionTitle) ?>"
                          readonly
                        >
                        <div class="alert alert-info mt-2 mb-0 py-2 invisible">
                            <span class="fw-semibold">Pista:</span>
                            &nbsp;
                        </div>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <?php if (!empty($flags) && is_array($flags)): ?>
            <div class="mb-4">
              <div class="small text-muted mb-2">Configuración activa</div>

              <div class="d-flex flex-wrap gap-2">
                <?php foreach ($flags as $key => $enabled): ?>
                  <?php if ($enabled): ?>
                    <span class="badge rounded-pill text-bg-light border">
                      ✅ <?= $e($key) ?>
                    </span>
                  <?php endif; ?>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="d-flex justify-content-end gap-2">

            <a class="btn btn-outline-secondary" href="<?= $e($url->to($howMuchDoYouKnowPaths->titleStep($sessionId))) ?>">
              Volver
            </a>

            <?php if (($isStepCorrect ?? null) === true): ?>
              <a class="btn btn-success" href="<?= $e($nextUrl) ?>">
                Continuar
              </a>
            <?php else: ?>
              <button type="submit" class="btn btn-primary">
                Comprobar
              </button>
            <?php endif; ?>

          </div>

        </form>

      </div>
    </div>

  </div>
</div>