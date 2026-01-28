<?php

declare(strict_types=1);

namespace App\Controllers\Dev;

use App\Application\Http\Redirector;
use App\Application\Routing\UrlGenerator;
use App\Domain\Exercise\ConfigEjercicio;
use App\Domain\Exercise\PasoEjercicio;
use App\Domain\Exercise\TipoEjercicio;
use App\Infrastructure\Session\AlmacenSesionEjercicio;
use App\Core\Routes\Dev\RutasDevSesionEjercicio;

final class DevSesionEjercicioController
{
    public function __construct(
        private readonly AlmacenSesionEjercicio $almacen,
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator
    ) {
    }

    /**
     * GET /dev/sesion-ejercicio
     * - Crea una sesión si no existe.
     * - Lee una sesión si existe de $_SESSION
     * - Muestra una pantalla sencilla de depuración
     */
    public function mostrar(): void
    {
        $sesion = $this->almacen->getSesionActual();

        if ($sesion === null) {
            $type = TipoEjercicio::cuantoSabesTema();

            $contextoUsuario = [
                'usuario' => 'dev-user',
                'oposicionId' => 'dev-opposition',
            ];

            $config = new ConfigEjercicio(
                tema: 0,
                dificultad: 2,
                banderas: ['barajar_preguntas' => true]
            );

            $sesion = $this->almacen->crear(
                tipoEjercicio: $type,
                contextoUsuario: $contextoUsuario,
                config: $config,
                primerPaso: PasoEjercicio::primero()
            );
        }

        $recargado = $this->almacen->get($sesion->sesionId());
        if ($recargado === null) {
            http_response_code(500);
            echo "ERROR: No se pudo recuperar la sesión desde el almacen.";
            return;
        }

        $this->renderHtml($recargado);
    }

    /**
     * POST /dev/sesion-ejercicio/siguiente
     * - Carga la sesión actual de $_SESSION
     * - Progresa al siguiente paso del ejercicio
     * - Guarda la sesión
     * - Redirige a la pantalla anterior (PRG)
     */
    public function siguiente(): void
    {
        $sesion = $this->almacen->getSesionActual();

        if ($sesion === null) {
            $this->redirector->redirect(RutasDevSesionEjercicio::BASE);
            return;
        }

        $siguientePaso = $sesion->pasoActual()->siguiente() ?? $sesion->pasoActual();
        $sesion->moverAlPaso($siguientePaso);

        $this->almacen->guardar($sesion);

        // PRG: evitar el reenvío del formulario
        $this->redirector->redirect(RutasDevSesionEjercicio::BASE, 303);
    }

    /**
     * POST /dev/sesion-ejercicio/reset
     * - Borrar la sesión actual
     * - Redirige a la pantalla anterior
     */
    public function reset(): void
    {
        $sesionIdActual = $this->almacen->getSesionIdActual();
        if ($sesionIdActual !== null) {
            $this->almacen->borrar($sesionIdActual);
        }

        $this->redirector->redirect(RutasDevSesionEjercicio::BASE, 303);
    }

    private function renderHtml($sesion): void
    {
        $sesionId = htmlspecialchars($sesion->sesionId(), ENT_QUOTES, 'UTF-8');
        $tipoSlug = htmlspecialchars($sesion->tipoEjercicio()->slug(), ENT_QUOTES, 'UTF-8');
        $tipoNombre = htmlspecialchars($sesion->tipoEjercicio()->nombre(), ENT_QUOTES, 'UTF-8');
        $paso = htmlspecialchars($sesion->pasoActual()->value, ENT_QUOTES, 'UTF-8');
        $fechaCreacion = htmlspecialchars($sesion->fechaCreacion()->format(\DateTimeInterface::ATOM), ENT_QUOTES, 'UTF-8');
        $fechaActualizacion = htmlspecialchars($sesion->fechaActualizacion()->format(\DateTimeInterface::ATOM), ENT_QUOTES, 'UTF-8');

        $config = $sesion->config();
        $tema = $config->tema();
        $dificultad = $config->dificultad();
        $banderasJson = htmlspecialchars(json_encode($config->banderas(), JSON_PRETTY_PRINT), ENT_QUOTES, 'UTF-8');

        $siguienteAccion = $this->urlGenerator->to(RutasDevSesionEjercicio::SIGUIENTE);
        $resetAccion = $this->urlGenerator->to(RutasDevSesionEjercicio::RESET);

        echo <<<HTML
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8"/>
  <title>Dev Exercise Session Smoke</title>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <style>
    body { font-family: Arial, sans-serif; padding: 16px; }
    code, pre { background: #f6f8fa; padding: 8px; border-radius: 8px; display: block; overflow:auto; }
    .row { display:flex; gap:12px; flex-wrap:wrap; }
    .card { border:1px solid #ddd; border-radius:12px; padding:12px; min-width: 280px; }
    form { display:inline-block; margin-right: 8px; }
    button { padding:8px 12px; }
  </style>
</head>
<body>
  <h1>Dev Ejercicio Sesión Smoke</h1>

  <div class="row">
    <div class="card">
      <h2>Sesion</h2>
      <p><strong>sesion id</strong>: <code>{$sesionId}</code></p>
      <p><strong>tipo</strong>: <code>{$tipoSlug}</code> ({$tipoNombre})</p>
      <p><strong>paso actual</strong>: <code>{$paso}</code></p>
      <p><strong>fecha creación</strong>: <code>{$fechaCreacion}</code></p>
      <p><strong>fecha actualización</strong>: <code>{$fechaActualizacion}</code></p>
    </div>

    <div class="card">
      <h2>Config</h2>
      <p><strong>tema</strong>: <code>{$tema}</code></p>
      <p><strong>dificultad</strong>: <code>{$dificultad}</code></p>
      <p><strong>banderas</strong>:</p>
      <pre>{$banderasJson}</pre>
    </div>
  </div>

  <hr/>

  <form method="post" action="{$siguienteAccion}">
    <button type="submit">Siguiente paso (POST → 303 → GET)</button>
  </form>

  <form method="post" action="{$resetAccion}">
    <button type="submit">Resetea sesión</button>
  </form>

  <p>
    ✅ Pistas para una correcta validación:
    <ul>
      <li>Refresca la página: la sesión debería haberse persistido (no hay parámetros en la URL).</li>
      <li>Clica "Siguiente paso": el paso del ejercicio ha cambiado y no cambias de página.</li>
      <li>Abre DevTools → Red: no sesionId debería aparecen en la URL.</li>
    </ul>
  </p>

</body>
</html>
HTML;
    }
}
