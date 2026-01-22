<?php
/** @var array $payload */
/** @var string $sesionId */

use App\Application\Exercises\Payload\ClavesPasoPayload;
use App\Core\Routes\RutasCuantoSabesTema;
use App\Helpers\Router;

$items = $payload[ClavesPasoPayload::ITEMS] ?? [];
$meta  = $payload[ClavesPasoPayload::META] ?? [];

$numeracionTema   = $meta['numeracionTema'] ?? '';
$tituloTema       = $meta['tituloTema'] ?? '';
$gradoDificultad  = $meta['gradoDificultad'] ?? '';
$banderas         = $meta['banderas'] ?? [];
$tipoPista        = $meta['tipoPista'] ?? '';

$item        = $items[0] ?? [];
$fieldName   = $item['nombre'] ?? 'titulo';
$placeholder = $item['placeholder'] ?? '';
$pista       = $item['pista'] ?? '';

$action = Router::url(RutasCuantoSabesTema::evaluarTitulo($sesionId));
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
          Tema <?= htmlspecialchars((string)$numeracionTema, ENT_QUOTES, 'UTF-8') ?>
        </span>
        <?php if ($gradoDificultad !== ''): ?>
          <span class="badge text-bg-secondary">
            Dificultad <?= htmlspecialchars((string)$gradoDificultad, ENT_QUOTES, 'UTF-8') ?>
          </span>
        <?php endif; ?>
      </div>
    </div>

    <div class="mb-3">
    <?php if (isset($evaluacion)): ?>
      <?php if ($evaluacion): ?>
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

        <form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>">
          <div class="mb-3">
            <label for="<?= htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8') ?>" class="form-label fw-semibold">
              Escribe el título del tema
            </label>

            <input
              type="text"
              class="form-control form-control-lg"
              id="<?= htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8') ?>"
              name="<?= htmlspecialchars($fieldName, ENT_QUOTES, 'UTF-8') ?>"
              placeholder="<?= htmlspecialchars((string)$placeholder, ENT_QUOTES, 'UTF-8') ?>"
              autocomplete="off"
              required
            >

            <div class="form-text">
              <?php if (!empty($tipoPista)): ?>
                Pista: <span class="fw-semibold"><?= htmlspecialchars((string)$tipoPista, ENT_QUOTES, 'UTF-8') ?></span>.
              <?php else: ?>
                Escribe exactamente el título (mañana añadimos pistas).
              <?php endif; ?>
            </div>

            <?php if (!empty($pista)): ?>
              <div class="alert alert-info mt-3 mb-0">
                <div class="fw-semibold mb-1">Pista</div>
                <div><?= htmlspecialchars((string)$pista, ENT_QUOTES, 'UTF-8') ?></div>
              </div>
            <?php endif; ?>
          </div>

          <?php if (!empty($banderas) && is_array($banderas)): ?>
            <div class="mb-4">
              <div class="small text-muted mb-2">Configuración activa</div>
              <div class="d-flex flex-wrap gap-2">
                <?php foreach ($banderas as $key => $enabled): ?>
                  <?php if ($enabled): ?>
                    <span class="badge rounded-pill text-bg-light border">
                      ✅ <?= htmlspecialchars((string)$key, ENT_QUOTES, 'UTF-8') ?>
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
            <?php if (($evaluacion ?? null) === true): ?>
            <a class="btn btn-success"
              href="<?= htmlspecialchars(
                  Router::url(RutasCuantoSabesTema::pasoIndice($sesionId)),
                  ENT_QUOTES,
                  'UTF-8'
              ) ?>">
              Continuar
            </a>
          <?php else: ?>
            <button type="submit" class="btn btn-primary">Comprobar</button>
          <?php endif; ?>
          </div>
        </form>

      </div>
    </div>

  </div>
</div>
