<?php
namespace App\App;

use App\Application\Exercises\CuantoSabesTemaConfigPayloadBuilder;
use App\Application\Exercises\Evaluation\CuantoSabesTemaTituloEvaluationService;
use App\Application\Exercises\StepBuilder\CuantoSabesTemaTituloPayloadBuilder;
use App\Controllers\LoginController;
use App\Controllers\PanelControlEjerciciosController;
use App\Controllers\CuantoSabesTemaConfigController;
use App\Controllers\CuantoSabesTemaTituloController;
use App\Controllers\Dev\DevSesionEjercicioController;
use App\Core\Routes\RutasCuantoSabesTema;
use App\Core\Routes\Dev\RutasDevSesionEjercicio;
use App\Domain\Exercise\PistaService;
use App\Domain\Temas\TemaRepository;
use App\Helpers\Router;
use App\Infrastructure\Persistence\Repositories\TemaRepositorySQL;
use App\Infrastructure\Session\AlmacenSesionEjercicio;

final class AppWiring
{
    private ?AlmacenSesionEjercicio $almacenSesionEjercicio = null;
    private ?PistaService $pistaServicio = null;
    private ?TemaRepository $temaRepositorio = null;

    public function rutas(): array
    {
        return [
            'login' => function (): void {
                $c = $this->loginController();

                if (Router::esGet()) {
                    $c->mostrar();
                    return;
                }

                if (Router::esPost()) {
                    $c->comprobar();
                    return;
                }

                http_response_code(405);
            },

            'login/salir' => function (): void {
                $this->loginController()->salir();
            },

            'panel-control-ejercicios' => function (): void {
                $this->panelControlEjerciciosController()->mostrar();
            },

            RutasCuantoSabesTema::CONFIG => function (): void {
                if (!Router::esGet()) { http_response_code(405); return; }
                $this->cuantoSabesTemaConfigController()->mostrar();
            },

            RutasCuantoSabesTema::INICIO => function (): void {
                if (!Router::esPost()) { http_response_code(405); return; }
                $this->cuantoSabesTemaConfigController()->comprobar();
            },

            RutasCuantoSabesTema::PASO_TITULO => function (): void {
                if (!Router::esGet()) { http_response_code(405); return; }
                $this->cuantoSabesTemaTituloController()->mostrar();
            },

            RutasCuantoSabesTema::EVAL_TITULO => function (): void {
                if (!Router::esPost()) { http_response_code(405); return; }
                $this->cuantoSabesTemaTituloController()->evaluar();
            },
            
            // DEV
            RutasDevSesionEjercicio::BASE => function (): void {
                if (!Router::esGet()) { http_response_code(405); return; }
                $this->devSesionEjercicioController()->mostrar();
            },

            RutasDevSesionEjercicio::SIGUIENTE => function (): void {
                if (!Router::esPost()) { http_response_code(405); return; }
                $this->devSesionEjercicioController()->siguiente();
            },

            RutasDevSesionEjercicio::RESET => function (): void {
                if (!Router::esPost()) { http_response_code(405); return; }
                $this->devSesionEjercicioController()->reset();
            },
        ];
    }

    // -----------------
    // Controllers
    // -----------------
    private function loginController(): LoginController
    {
        return new LoginController(/* deps cuando toque */);
    }

    private function panelControlEjerciciosController(): PanelControlEjerciciosController
    {
        return new PanelControlEjerciciosController(/* deps cuando toque */);
    }

    private function cuantoSabesTemaConfigController(): CuantoSabesTemaConfigController
    {
        return new CuantoSabesTemaConfigController(
            $this->cuantoSabesTemaConfigPayloadBuilder(),
            $this->temaRepositorio(),
            $this->almacenSesionEjercicio()
        );
    }

    private function cuantoSabesTemaTituloController(): CuantoSabesTemaTituloController
    {
        return new CuantoSabesTemaTituloController(
            $this->almacenSesionEjercicio(),
            $this->cuantoSabesTemaTituloPayloadBuilder(),
            $this->temaRepositorio(),
            $this->cuantoSabesTemaTituloEvaluationService()
        );
    }

    private function devSesionEjercicioController(): DevSesionEjercicioController
    {
        return new DevSesionEjercicioController($this->almacenSesionEjercicio());
    }

    // -----------------
    // Dependencias compartidas
    // -----------------
    private function almacenSesionEjercicio(): AlmacenSesionEjercicio
    {
        if ($this->almacenSesionEjercicio === null) {
            $this->almacenSesionEjercicio = new AlmacenSesionEjercicio();
        }
        return $this->almacenSesionEjercicio;
    }

    private function pistaServicio(): PistaService
    {
        if ($this->pistaServicio === null) {
            $this->pistaServicio = new PistaService();
        }
        return $this->pistaServicio;
    }

    private function temaRepositorio(): TemaRepository
    {
        if ($this->temaRepositorio === null) {
            $this->temaRepositorio = new TemaRepositorySQL();
        }
        return $this->temaRepositorio;
    }

    private function cuantoSabesTemaConfigPayloadBuilder(): CuantoSabesTemaConfigPayloadBuilder
    {
        return new CuantoSabesTemaConfigPayloadBuilder(
            $this->temaRepositorio()
        );
    }

    private function cuantoSabesTemaTituloPayloadBuilder(): CuantoSabesTemaTituloPayloadBuilder
    {
        return new CuantoSabesTemaTituloPayloadBuilder(
            $this->temaRepositorio(),
            $this->pistaServicio()
        );
    }

    private function cuantoSabesTemaTituloEvaluationService(): CuantoSabesTemaTituloEvaluationService
    {
        return new CuantoSabesTemaTituloEvaluationService();
    }
}

?>