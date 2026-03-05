<?php
/** @var array $payload */
/** @var string $sessionId */

use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;

$title = 'Cuánto sabes del tema';

$items = $payload[StepPayloadKeys::ITEMS] ?? [];
$meta  = $payload[StepPayloadKeys::META] ?? [];

$topicOrder   = $meta['topicOrder'] ?? '';
$topicTitle   = $meta['topicTitle'] ?? '';
$difficulty   = $meta['difficulty'] ?? '';
$flags        = $meta['flags'] ?? [];
$hintType     = $meta['hintType'] ?? '';

$item        = $items[0] ?? [];
$fieldName   = $item['name'] ?? 'title';
$placeholder = $item['placeholder'] ?? '';
$hint       = $item['hint'] ?? '';

$isStepCorrect = null;

if (is_array($evaluation)) {
    $isStepCorrect = $evaluation['result']['isStepCorrect'] ?? null;
}

$action = $url->to($howMuchDoYouKnowPaths->titleEvaluation($sessionId));

$e = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
?>

<div class="row justify-content-center">
  <div class="col-12 col-lg-8">

    <div class="d-flex align-items-start justify-content-between mb-3">
      <div>
        <h1 class="h4 mb-1">Cuánto sabes del tema</h1>
        <div class="text-muted">
          Paso: <span class="fw-semibold">Título</span>
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
          <div><strong>No es correcto.</strong> Inténtalo de nuevo.</div>
        </div>
      <?php endif; ?>
    <?php endif; ?>
    </div>

    <div class="card shadow-sm">
      <div class="card-body">

        <form method="post" action="<?= $e($action) ?>">
          <div class="mb-3">

            <label for="<?= $e($fieldName) ?>" class="form-label fw-semibold">
              Escribe el título del tema
            </label>

            <input
              type="text"
              class="form-control form-control-lg"
              id="<?= $e($fieldName) ?>"
              name="<?= $e($fieldName) ?>"
              placeholder="<?= $e($placeholder) ?>"
              autocomplete="off"
              required
            >

            <div class="form-text">
              <?php if (!empty($hintType)): ?>
                Pista: <span class="fw-semibold"><?= $e($hintType) ?></span>.
              <?php else: ?>
                Escribe exactamente el título.
              <?php endif; ?>
            </div>

            <?php if (!empty($hint)): ?>
              <div class="alert alert-info mt-3 mb-0">
                <div class="fw-semibold mb-1">Pista</div>
                <div><?= $e($hint) ?></div>
              </div>
            <?php endif; ?>

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

            <a class="btn btn-outline-secondary" href="javascript:history.back()">
              Volver
            </a>

            <?php if (($isStepCorrect ?? null) === true): ?>

              <a class="btn btn-success"
                 href="<?= $e($url->to($howMuchDoYouKnowPaths->indexStep($sessionId))) ?>">
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
