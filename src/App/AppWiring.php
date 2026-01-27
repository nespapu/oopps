<?php
namespace App\App;

use App\Application\Exercises\CuantoSabesTemaConfigPayloadBuilder;
use App\Application\Exercises\Evaluation\CuantoSabesTemaTituloEvaluationService;
use App\Application\Exercises\StepBuilder\CuantoSabesTemaTituloPayloadBuilder;
use App\Application\Flash\FlashMessenger;
use App\Application\Http\RequestContext;
use App\Application\Routing\UrlGenerator;
use App\Controllers\LoginController;
use App\Controllers\PanelControlEjerciciosController;
use App\Controllers\CuantoSabesTemaConfigController;
use App\Controllers\CuantoSabesTemaTituloController;
use App\Controllers\Dev\DevSesionEjercicioController;
use App\Core\Routes\RutasCuantoSabesTema;
use App\Core\Routes\Dev\RutasDevSesionEjercicio;
use App\Domain\Exercise\PistaService;
use App\Domain\Temas\TemaRepository;
use App\Helpers\ValidadorMetodoHttp;
use App\Infrastructure\Flash\SessionFlashMessenger;
use App\Infrastructure\Http\ServerRequestContext;
use App\Infrastructure\Persistence\Repositories\TemaRepositorySQL;
use App\Infrastructure\Routing\ScriptNameUrlGenerator;
use App\Infrastructure\Session\AlmacenSesionEjercicio;

final class AppWiring
{
    private ?AlmacenSesionEjercicio $almacenSesionEjercicio = null;
    private ?FlashMessenger $flash = null;
    private ?PistaService $pistaServicio = null;
    private ?RequestContext $requestContext = null;
    private ?ScriptNameUrlGenerator $urlGenerator = null;
    private ?TemaRepository $temaRepositorio = null;

    public function rutas(): array
    {
        return [
            'login' => ValidadorMetodoHttp::segunMetodo(
                fn () => $this->loginController()->mostrar(),
                fn () => $this->loginController()->comprobar()
            ),

            'login/salir' => ValidadorMetodoHttp::soloPost(
                fn () => $this->loginController()->salir()
            ),

            'panel-control-ejercicios' => ValidadorMetodoHttp::soloGet(
                fn () => $this->panelControlEjerciciosController()->mostrar()
            ),

            RutasCuantoSabesTema::CONFIG => ValidadorMetodoHttp::soloGet(
                fn () => $this->cuantoSabesTemaConfigController()->mostrar()
            ),

            RutasCuantoSabesTema::INICIO => ValidadorMetodoHttp::soloPost(
                fn () => $this->cuantoSabesTemaConfigController()->comprobar()
            ),

            RutasCuantoSabesTema::PASO_TITULO => ValidadorMetodoHttp::soloGet(
                fn () => $this->cuantoSabesTemaTituloController()->mostrar()
            ),

            RutasCuantoSabesTema::EVAL_TITULO => ValidadorMetodoHttp::soloPost(
                fn () => $this->cuantoSabesTemaTituloController()->evaluar()
            ),
            
            RutasCuantoSabesTema::PASO_INDICE => ValidadorMetodoHttp::soloGet(
                fn () => print "Paso índice. Próximamente..."
            ),
            
            // DEV
            RutasDevSesionEjercicio::BASE => ValidadorMetodoHttp::soloGet(
                fn () => $this->devSesionEjercicioController()->mostrar()
            ),

            RutasDevSesionEjercicio::SIGUIENTE => ValidadorMetodoHttp::soloPost(
                fn () => $this->devSesionEjercicioController()->siguiente()
            ),

            RutasDevSesionEjercicio::RESET => ValidadorMetodoHttp::soloPost(
                fn () => $this->devSesionEjercicioController()->reset()
            ),
        ];
    }

    // -----------------
    // Controllers
    // -----------------
    private function loginController(): LoginController
    {
        return new LoginController(
            $this->flash()
        );
    }

    private function panelControlEjerciciosController(): PanelControlEjerciciosController
    {
        return new PanelControlEjerciciosController(/* deps cuando toque */);
    }

    private function cuantoSabesTemaConfigController(): CuantoSabesTemaConfigController
    {
        return new CuantoSabesTemaConfigController(
            $this->almacenSesionEjercicio(),
            $this->cuantoSabesTemaConfigPayloadBuilder(),
            $this->flash(),
            $this->temaRepositorio(),
            $this->urlGenerator()
        );
    }

    private function cuantoSabesTemaTituloController(): CuantoSabesTemaTituloController
    {
        return new CuantoSabesTemaTituloController(
            $this->almacenSesionEjercicio(),
            $this->cuantoSabesTemaTituloPayloadBuilder(),
            $this->cuantoSabesTemaTituloEvaluationService(),
            $this->temaRepositorio(),
            $this->urlGenerator()
        );
    }

    private function devSesionEjercicioController(): DevSesionEjercicioController
    {
        return new DevSesionEjercicioController(
            $this->almacenSesionEjercicio(),
            $this->urlGenerator()
        );
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

    private function flash() : FlashMessenger
    {
        if ($this->flash === null) {
            $this->flash = new SessionFlashMessenger();
        }
        return $this->flash;
    }

    private function pistaServicio(): PistaService
    {
        if ($this->pistaServicio === null) {
            $this->pistaServicio = new PistaService();
        }
        return $this->pistaServicio;
    }

    private function requestContext(): RequestContext
    {
        if ($this->requestContext === null) {
            $this->requestContext = new ServerRequestContext();
        }
        return $this->requestContext;
    }

    private function temaRepositorio(): TemaRepository
    {
        if ($this->temaRepositorio === null) {
            $this->temaRepositorio = new TemaRepositorySQL();
        }
        return $this->temaRepositorio;
    }

    private function urlGenerator() : UrlGenerator
    {
        if ($this->urlGenerator === null) {
            $this->urlGenerator = new ScriptNameUrlGenerator();
        }
        return $this->urlGenerator;
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