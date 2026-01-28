<?php
namespace App\App;

use App\App\Http\AppKernel;
use App\App\Http\AppRoutes;
use App\Application\Auth\AuthService;
use App\Application\Exercises\CuantoSabesTemaConfigPayloadBuilder;
use App\Application\Exercises\Evaluation\CuantoSabesTemaTituloEvaluationService;
use App\Application\Exercises\StepBuilder\CuantoSabesTemaTituloPayloadBuilder;
use App\Application\Flash\FlashMessenger;
use App\Application\Http\HttpMethodGuard;
use App\Application\Http\Redirector;
use App\Application\Http\RequestContext;
use App\Application\Routing\UrlGenerator;
use App\Application\Session\SessionStore;
use App\Controllers\LoginController;
use App\Controllers\PanelControlEjerciciosController;
use App\Controllers\CuantoSabesTemaConfigController;
use App\Controllers\CuantoSabesTemaTituloController;
use App\Controllers\Dev\DevSesionEjercicioController;
use App\Core\Routes\RutasCuantoSabesTema;
use App\Core\Routes\Dev\RutasDevSesionEjercicio;
use App\Domain\Exercise\PistaService;
use App\Domain\Temas\TemaRepository;
use App\Infrastructure\Auth\DefaultAuthService;
use App\Infrastructure\Flash\SessionFlashMessenger;
use App\Infrastructure\Http\DefaultHttpMethodGuard;
use App\Infrastructure\Http\HeaderRedirector;
use App\Infrastructure\Http\ServerRequestContext;
use App\Infrastructure\Persistence\Repositories\TemaRepositorySQL;
use App\Infrastructure\Routing\ScriptNameUrlGenerator;
use App\Infrastructure\Session\AlmacenSesionEjercicio;
use App\Infrastructure\Session\PhpSessionStore;

final class AppWiring
{
    private ?AppKernel $appKernel = null;
    private ?AppRoutes $appRoutes = null;
    private ?AlmacenSesionEjercicio $almacenSesionEjercicio = null;
    private ?AuthService $authService = null;
    private ?FlashMessenger $flash = null;
    private ?HttpMethodGuard $httpMethodGuard = null;
    private ?PistaService $pistaServicio = null;
    private ?Redirector $redirector = null;
    private ?RequestContext $requestContext = null;
    private ?SessionStore $sessionStore = null;
    private ?ScriptNameUrlGenerator $urlGenerator = null;
    private ?TemaRepository $temaRepositorio = null;

    // -----------------
    // Controllers
    // -----------------
    public function appKernel(): AppKernel
    {
        if ($this->appKernel === null) {
            return new AppKernel(
                $this->appRoutes(),
                $this->requestContext()
            );
        }
        return $this->appKernel;
    }

    private function appRoutes(): AppRoutes
    {
        if ($this->appRoutes === null) {
            return new AppRoutes(
                [
                    'login' => $this->httpMethodGuard()->byMethod(
                        fn () => $this->loginController()->mostrar(),
                        fn () => $this->loginController()->comprobar()
                    ),

                    'login/salir' => $this->httpMethodGuard()->onlyPost(
                        fn () => $this->loginController()->salir()
                    ),

                    'panel-control-ejercicios' => $this->httpMethodGuard()->onlyGet(
                        fn () => $this->panelControlEjerciciosController()->mostrar()
                    ),

                    RutasCuantoSabesTema::CONFIG => $this->httpMethodGuard()->onlyGet(
                        fn () => $this->cuantoSabesTemaConfigController()->mostrar()
                    ),

                    RutasCuantoSabesTema::INICIO => $this->httpMethodGuard()->onlyPost(
                        fn () => $this->cuantoSabesTemaConfigController()->comprobar()
                    ),

                    RutasCuantoSabesTema::PASO_TITULO => $this->httpMethodGuard()->onlyGet(
                        fn () => $this->cuantoSabesTemaTituloController()->mostrar()
                    ),

                    RutasCuantoSabesTema::EVAL_TITULO => $this->httpMethodGuard()->onlyPost(
                        fn () => $this->cuantoSabesTemaTituloController()->evaluar()
                    ),
                    
                    RutasCuantoSabesTema::PASO_INDICE => $this->httpMethodGuard()->onlyGet(
                        fn () => print "Paso índice. Próximamente..."
                    ),
                    
                    // DEV
                    RutasDevSesionEjercicio::BASE => $this->httpMethodGuard()->onlyGet(
                        fn () => $this->devSesionEjercicioController()->mostrar()
                    ),

                    RutasDevSesionEjercicio::SIGUIENTE => $this->httpMethodGuard()->onlyPost(
                        fn () => $this->devSesionEjercicioController()->siguiente()
                    ),

                    RutasDevSesionEjercicio::RESET => $this->httpMethodGuard()->onlyPost(
                        fn () => $this->devSesionEjercicioController()->reset()
                    ),
                ]
            );
        }
        return $this->appRoutes;
    }

    private function loginController(): LoginController
    {
        return new LoginController(
            $this->authService(),
            $this->flash(),
            $this->redirector()
        );
    }

    private function panelControlEjerciciosController(): PanelControlEjerciciosController
    {
        return new PanelControlEjerciciosController(
            $this->authService()
        );
    }

    private function cuantoSabesTemaConfigController(): CuantoSabesTemaConfigController
    {
        return new CuantoSabesTemaConfigController(
            $this->almacenSesionEjercicio(),
            $this->authService(),
            $this->cuantoSabesTemaConfigPayloadBuilder(),
            $this->flash(),
            $this->redirector(),
            $this->temaRepositorio(),
            $this->urlGenerator()
        );
    }

    private function cuantoSabesTemaTituloController(): CuantoSabesTemaTituloController
    {
        return new CuantoSabesTemaTituloController(
            $this->almacenSesionEjercicio(),
            $this->authService(),
            $this->cuantoSabesTemaTituloPayloadBuilder(),
            $this->cuantoSabesTemaTituloEvaluationService(),
            $this->redirector(),
            $this->temaRepositorio(),
            $this->urlGenerator()
        );
    }

    private function devSesionEjercicioController(): DevSesionEjercicioController
    {
        return new DevSesionEjercicioController(
            $this->almacenSesionEjercicio(),
            $this->redirector(),
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

    private function authService() : AuthService
    {
        if($this->authService === null) {
            $this->authService = new DefaultAuthService(
                $this->sessionStore(),
                $this->requestContext(),
                $this->redirector(),
                $this->flash()
            );
        }
        return $this->authService;
    }

    private function flash() : FlashMessenger
    {
        if ($this->flash === null) {
            $this->flash = new SessionFlashMessenger();
        }
        return $this->flash;
    }

    private function httpMethodGuard() : HttpMethodGuard 
    {
        if ($this->httpMethodGuard === null) {
            $this->httpMethodGuard = new DefaultHttpMethodGuard(
                $this->requestContext()
            );
        }
        return $this->httpMethodGuard;
    }

    private function pistaServicio(): PistaService
    {
        if ($this->pistaServicio === null) {
            $this->pistaServicio = new PistaService();
        }
        return $this->pistaServicio;
    }

    private function redirector(): Redirector
    {
        if ($this->redirector === null) {
            $this->redirector = new HeaderRedirector(
                $this->urlGenerator()
            );
        }
        return $this->redirector;
    }

    private function requestContext(): RequestContext
    {
        if ($this->requestContext === null) {
            $this->requestContext = new ServerRequestContext();
        }
        return $this->requestContext;
    }

    private function sessionStore() : SessionStore
    {
        if ($this->sessionStore === null) {
            $this->sessionStore = new PhpSessionStore();
        }
        return $this->sessionStore;
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