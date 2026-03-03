<?php
namespace App\App;

use App\App\Http\AppKernel;
use App\App\Http\AppRoutes;
use App\App\Routing\AuthPaths;
use App\App\Routing\AuthRoutes;
use App\App\Routing\HowMuchDoYouKnowRoutes;
use App\App\Routing\HowMuchDoYouKnowPaths;
use App\App\Routing\DevPaths;
use App\App\Routing\DevRoutes;
use App\App\Routing\ExercisesDashboardPaths;
use App\App\Routing\ExercisesDashboardRoutes;
use App\Application\Auth\AuthService;
use App\Application\Exercises\ExerciseSessionStore;
use App\Application\Exercises\Evaluation\HowMuchDoYouKnowTitleEvaluationService;
use App\Application\Exercises\Evaluation\EqualityEvaluator;
use App\Application\Exercises\Evaluation\TextNormalizer;
use App\Application\Exercises\StepBuilder\HowMuchDoYouKnowConfigPayloadBuilder;
use App\Application\Exercises\StepBuilder\HowMuchDoYouKnowTitlePayloadBuilder;
use App\Application\Flash\FlashMessenger;
use App\Application\Http\HttpMethodGuard;
use App\Application\Http\Redirector;
use App\Application\Http\RequestContext;
use App\Application\Routing\RouteCanonicalizer;
use App\Application\Routing\RouteUrlGenerator;
use App\Application\Routing\UrlGenerator;
use App\Application\Session\SessionStore;
use App\Controllers\LoginController;
use App\Controllers\ExercisesDashboardController;
use App\Controllers\HowMuchDoYouKnowConfigController;
use App\Controllers\HowMuchDoYouKnowTitleController;
use App\Controllers\Dev\DevSesionEjercicioController;
use App\Domain\Auth\UserRepository;
use App\Domain\Exercise\HintService;
use App\Domain\Temas\TopicRepository;
use App\Infrastructure\Auth\DefaultAuthService;
use App\Infrastructure\Flash\SessionFlashMessenger;
use App\Infrastructure\Http\DefaultHttpMethodGuard;
use App\Infrastructure\Http\HeaderRedirector;
use App\Infrastructure\Http\ServerRequestContext;
use App\Infrastructure\Persistence\ConexionBD;
use App\Infrastructure\Persistence\Repositories\TopicRepositorySQL;
use App\Infrastructure\Persistence\Repositories\UserRepositorySQL;
use App\Infrastructure\Routing\RouteAssembler;
use App\Infrastructure\Routing\RouteCollection;
use App\Infrastructure\Routing\ScriptNameUrlGenerator;
use App\Infrastructure\Session\PhpExerciseSessionStore;
use App\Infrastructure\Session\PhpSessionStore;
use PDO;

final class AppWiring
{
    // =========================================================================
    // Runtime / Kernel
    // =========================================================================
    private ?AppKernel $appKernel = null;
    private ?AppRoutes $appRoutes = null;
    private ?RequestContext $requestContext = null;

    // =========================================================================
    // Routing / Navigation (App layer)
    // =========================================================================
    private ?AuthPaths $authPaths = null;
    private ?HowMuchDoYouKnowPaths $cuantoSabesTemaPaths = null;
    private ?DevPaths $devPaths = null;
    private ?ExercisesDashboardPaths $panelControlEjerciciosPaths = null;

    // =========================================================================
    // Routing infrastructure
    // =========================================================================
    private ?HttpMethodGuard $httpMethodGuard = null;
    private ?RouteAssembler $routeAssembler = null;
    private ?RouteCanonicalizer $routeCanonicalizer = null;
    private ?RouteUrlGenerator $routeUrlGenerator = null;

    // =========================================================================
    // Controllers (memoized per request for consistency)
    // =========================================================================
    private ?LoginController $loginController = null;
    private ?ExercisesDashboardController $panelControlEjerciciosController = null;
    private ?HowMuchDoYouKnowConfigController $cuantoSabesTemaConfigController = null;
    private ?HowMuchDoYouKnowTitleController $cuantoSabesTemaTituloController = null;
    private ?DevSesionEjercicioController $devSesionEjercicioController = null;

    // =========================================================================
    // Application services
    // =========================================================================
    private ?AuthService $authService = null;
    private ?Redirector $redirector = null;
    private ?FlashMessenger $flash = null;
    private ?ExerciseSessionStore $almacenSesionEjercicio = null;

    // =========================================================================
    // Builders / Evaluators
    // =========================================================================
    private ?HowMuchDoYouKnowConfigPayloadBuilder $cuantoSabesTemaConfigPayloadBuilder = null;
    private ?HowMuchDoYouKnowTitlePayloadBuilder $cuantoSabesTemaTituloPayloadBuilder = null;
    private ?HowMuchDoYouKnowTitleEvaluationService $cuantoSabesTemaTituloEvaluationService = null;
    private ?EqualityEvaluator $equalityEvaluator = null;
    private ?TextNormalizer $textNormalizer = null;

    // =========================================================================
    // Domain services
    // =========================================================================
    private ?HintService $pistaServicio = null;

    // =========================================================================
    // Infrastructure (IO)
    // =========================================================================
    private ?PDO $pdo = null;
    private ?SessionStore $sessionStore = null;
    private ?UrlGenerator $urlGenerator = null;
    private ?TopicRepository $temaRepositorio = null;
    private ?UserRepository $usuarioRepositorio = null;

    // =========================================================================
    // Public API
    // =========================================================================
    public function appKernel(): AppKernel
    {
        if ($this->appKernel === null) {
            $this->appKernel = new AppKernel(
                $this->appRoutes(),
                $this->requestContext(),
                $this->routeCanonicalizer()
            );
        }

        return $this->appKernel;
    }

    // =========================================================================
    // Routing (root + modules)
    // =========================================================================
    private function appRoutes(): AppRoutes
    {
        if ($this->appRoutes === null) {
            $all = new RouteCollection();

            $all->merge($this->authRoutes()->routes());
            $all->merge($this->panelControlEjerciciosRoutes()->routes());
            $all->merge($this->cuantoSabesTemaRoutes()->routes());
            $all->merge($this->devRoutes()->routes());

            $this->appRoutes = new AppRoutes($this->routeAssembler()->assemble($all));
        }

        return $this->appRoutes;
    }

    private function authRoutes(): AuthRoutes
    {
        $controller = $this->loginController();

        return new AuthRoutes(
            $this->authPaths(),
            \Closure::fromCallable([$controller, 'show']),
            \Closure::fromCallable([$controller, 'authenticate']),
            \Closure::fromCallable([$controller, 'logout'])
        );
    }

    private function panelControlEjerciciosRoutes(): ExercisesDashboardRoutes
    {
        $controller = $this->panelControlEjerciciosController();

        return new ExercisesDashboardRoutes(
            $this->panelControlEjerciciosPaths(),
            \Closure::fromCallable([$controller, 'show'])
        );
    }

    private function cuantoSabesTemaRoutes(): HowMuchDoYouKnowRoutes
    {
        $configController = $this->cuantoSabesTemaConfigController();
        $titleController = $this->cuantoSabesTemaTituloController();

        return new HowMuchDoYouKnowRoutes(
            $this->cuantoSabesTemaPaths(),
            \Closure::fromCallable([$configController, 'show']),
            \Closure::fromCallable([$configController, 'submit']),
            \Closure::fromCallable([$titleController, 'show']),
            \Closure::fromCallable([$titleController, 'evaluate']),
            fn() => print 'Proximamente...'
        );
    }

    private function devRoutes(): DevRoutes
    {
        $controller = $this->devSesionEjercicioController();

        return new DevRoutes(
            $this->devPaths(),
            \Closure::fromCallable([$controller, 'mostrar']),
            \Closure::fromCallable([$controller, 'siguiente']),
            \Closure::fromCallable([$controller, 'reset'])
        );
    }

    // =========================================================================
    // Routing / Navigation (App layer)
    // =========================================================================
    private function authPaths(): AuthPaths
    {
        if ($this->authPaths === null) {
            $this->authPaths = new AuthPaths();
        }
        return $this->authPaths;
    }

    private function cuantoSabesTemaPaths(): HowMuchDoYouKnowPaths
    {
        if ($this->cuantoSabesTemaPaths === null) {
            $this->cuantoSabesTemaPaths = new HowMuchDoYouKnowPaths(
                $this->routeUrlGenerator()
            );
        }
        return $this->cuantoSabesTemaPaths;
    }

    private function devPaths(): DevPaths
    {
        if ($this->devPaths === null) {
            $this->devPaths = new DevPaths();
        }
        return $this->devPaths;
    }

    private function panelControlEjerciciosPaths(): ExercisesDashboardPaths
    {
        if ($this->panelControlEjerciciosPaths === null) {
            $this->panelControlEjerciciosPaths = new ExercisesDashboardPaths();
        }
        return $this->panelControlEjerciciosPaths;
    }

    // =========================================================================
    // Controllers
    // =========================================================================
    private function loginController(): LoginController
    {
        if ($this->loginController === null) {
            $this->loginController = new LoginController(
                $this->authService(),
                $this->flash(),
                $this->redirector(),
                $this->sessionStore(),
                $this->usuarioRepositorio()
            );
        }

        return $this->loginController;
    }

    private function panelControlEjerciciosController(): ExercisesDashboardController
    {
        if ($this->panelControlEjerciciosController === null) {
            $this->panelControlEjerciciosController = new ExercisesDashboardController(
                $this->authService()
            );
        }

        return $this->panelControlEjerciciosController;
    }

    private function cuantoSabesTemaConfigController(): HowMuchDoYouKnowConfigController
    {
        if ($this->cuantoSabesTemaConfigController === null) {
            $this->cuantoSabesTemaConfigController = new HowMuchDoYouKnowConfigController(
                $this->almacenSesionEjercicio(),
                $this->authService(),
                $this->cuantoSabesTemaConfigPayloadBuilder(),
                $this->cuantoSabesTemaPaths(),
                $this->flash(),
                $this->redirector(),
                $this->temaRepositorio(),
                $this->urlGenerator()
            );
        }

        return $this->cuantoSabesTemaConfigController;
    }

    private function cuantoSabesTemaTituloController(): HowMuchDoYouKnowTitleController
    {
        if ($this->cuantoSabesTemaTituloController === null) {
            $this->cuantoSabesTemaTituloController = new HowMuchDoYouKnowTitleController(
                $this->almacenSesionEjercicio(),
                $this->authService(),
                $this->cuantoSabesTemaPaths(),
                $this->cuantoSabesTemaTituloPayloadBuilder(),
                $this->cuantoSabesTemaTituloEvaluationService(),
                $this->redirector(),
                $this->urlGenerator()
            );
        }

        return $this->cuantoSabesTemaTituloController;
    }

    private function devSesionEjercicioController(): DevSesionEjercicioController
    {
        if ($this->devSesionEjercicioController === null) {
            $this->devSesionEjercicioController = new DevSesionEjercicioController(
                $this->almacenSesionEjercicio(),
                $this->redirector(),
                $this->devPaths(),
                $this->urlGenerator()
            );
        }

        return $this->devSesionEjercicioController;
    }

    // =========================================================================
    // Application services (shared)
    // =========================================================================
    private function almacenSesionEjercicio(): ExerciseSessionStore
    {
        if ($this->almacenSesionEjercicio === null) {
            $this->almacenSesionEjercicio = new PhpExerciseSessionStore(
                $this->sessionStore()
            );
        }

        return $this->almacenSesionEjercicio;
    }

    private function authService(): AuthService
    {
        if ($this->authService === null) {
            $this->authService = new DefaultAuthService(
                $this->sessionStore(),
                $this->requestContext(),
                $this->redirector(),
                $this->flash()
            );
        }

        return $this->authService;
    }

    private function flash(): FlashMessenger
    {
        if ($this->flash === null) {
            $this->flash = new SessionFlashMessenger();
        }

        return $this->flash;
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

    // =========================================================================
    // Builders / Evaluators
    // =========================================================================
    private function cuantoSabesTemaConfigPayloadBuilder(): HowMuchDoYouKnowConfigPayloadBuilder
    {
        if ($this->cuantoSabesTemaConfigPayloadBuilder === null) {
            $this->cuantoSabesTemaConfigPayloadBuilder = new HowMuchDoYouKnowConfigPayloadBuilder(
                $this->temaRepositorio()
            );
        }

        return $this->cuantoSabesTemaConfigPayloadBuilder;
    }

    private function cuantoSabesTemaTituloPayloadBuilder(): HowMuchDoYouKnowTitlePayloadBuilder
    {
        if ($this->cuantoSabesTemaTituloPayloadBuilder === null) {
            $this->cuantoSabesTemaTituloPayloadBuilder = new HowMuchDoYouKnowTitlePayloadBuilder(
                $this->temaRepositorio(),
                $this->pistaServicio()
            );
        }

        return $this->cuantoSabesTemaTituloPayloadBuilder;
    }

    private function cuantoSabesTemaTituloEvaluationService(): HowMuchDoYouKnowTitleEvaluationService
    {
        if ($this->cuantoSabesTemaTituloEvaluationService === null) {
            $this->cuantoSabesTemaTituloEvaluationService = new HowMuchDoYouKnowTitleEvaluationService(
                $this->equalityEvaluator()
            );
        }

        return $this->cuantoSabesTemaTituloEvaluationService;
    }

    private function equalityEvaluator(): EqualityEvaluator
    {
        if ($this->equalityEvaluator === null) {
            $this->equalityEvaluator = new EqualityEvaluator(
                $this->textNormalizer()
            );
        }
        return $this->equalityEvaluator;
    }

    private function textNormalizer(): TextNormalizer
    {
        if ($this->textNormalizer === null) {
            $this->textNormalizer = new TextNormalizer();
        }
        return $this->textNormalizer;
    }

    // =========================================================================
    // Domain services
    // =========================================================================
    private function pistaServicio(): HintService
    {
        if ($this->pistaServicio === null) {
            $this->pistaServicio = new HintService();
        }

        return $this->pistaServicio;
    }

    // =========================================================================
    // HTTP / Request runtime
    // =========================================================================
    private function requestContext(): RequestContext
    {
        if ($this->requestContext === null) {
            $this->requestContext = new ServerRequestContext();
        }

        return $this->requestContext;
    }

    private function httpMethodGuard(): HttpMethodGuard
    {
        if ($this->httpMethodGuard === null) {
            $this->httpMethodGuard = new DefaultHttpMethodGuard(
                $this->requestContext()
            );
        }

        return $this->httpMethodGuard;
    }

    private function routeAssembler(): RouteAssembler
    {
        if ($this->routeAssembler === null) {
            $this->routeAssembler = new RouteAssembler(
                $this->httpMethodGuard()
            );
        }

        return $this->routeAssembler;
    }

    private function routeCanonicalizer(): RouteCanonicalizer
    {
        if ($this->routeCanonicalizer === null) {
            $this->routeCanonicalizer = new RouteCanonicalizer();
        }
        return $this->routeCanonicalizer;
    }

    private function routeUrlGenerator(): RouteUrlGenerator
    {
        if ($this->routeUrlGenerator === null) {
            $this->routeUrlGenerator = new RouteUrlGenerator();
        }
        return $this->routeUrlGenerator;
    }

    // =========================================================================
    // Infrastructure (IO)
    // =========================================================================
    private function pdo(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = ConexionBD::obtener();
        }

        return $this->pdo;
    }

    private function sessionStore(): SessionStore
    {
        if ($this->sessionStore === null) {
            $this->sessionStore = new PhpSessionStore();
        }

        return $this->sessionStore;
    }

    private function urlGenerator(): UrlGenerator
    {
        if ($this->urlGenerator === null) {
            $this->urlGenerator = new ScriptNameUrlGenerator();
        }

        return $this->urlGenerator;
    }

    private function temaRepositorio(): TopicRepository
    {
        if ($this->temaRepositorio === null) {
            $this->temaRepositorio = new TopicRepositorySQL(
                $this->pdo()
            );
        }

        return $this->temaRepositorio;
    }

    private function usuarioRepositorio(): UserRepository
    {
        if ($this->usuarioRepositorio === null) {
            $this->usuarioRepositorio = new UserRepositorySQL(
                $this->pdo()
            );
        }

        return $this->usuarioRepositorio;
    }
}
