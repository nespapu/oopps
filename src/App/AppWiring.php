<?php
namespace App\App;

use App\App\Http\AppKernel;
use App\App\Http\AppRoutes;
use App\App\Routing\AuthPaths;
use App\App\Routing\AuthRoutes;
use App\App\Routing\HowMuchDoYouKnow\Paths;
use App\App\Routing\HowMuchDoYouKnow\Routes;
use App\App\Routing\DevPaths;
use App\App\Routing\DevRoutes;
use App\App\Routing\ExercisesDashboardPaths;
use App\App\Routing\ExercisesDashboardRoutes;
use App\Application\Auth\AuthService;
use App\Application\Exercises\ExerciseSessionStore;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\EqualityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\TextNormalizer;
use App\Application\Exercises\HowMuchDoYouKnow\Config\ConfigPayloadBuilder;
use App\Application\Exercises\HowMuchDoYouKnow\Title\TitleEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\Title\TitlePayloadBuilder;
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
use App\Controllers\HowMuchDoYouKnow\ConfigController;
use App\Controllers\HowMuchDoYouKnow\TitleController;
use App\Controllers\Dev\DevExerciseSessionController;
use App\Domain\Auth\UserRepository;
use App\Domain\Exercise\HintService;
use App\Domain\Temas\TopicRepository;
use App\Infrastructure\Auth\DefaultAuthService;
use App\Infrastructure\Flash\SessionFlashMessenger;
use App\Infrastructure\Http\DefaultHttpMethodGuard;
use App\Infrastructure\Http\HeaderRedirector;
use App\Infrastructure\Http\ServerRequestContext;
use App\Infrastructure\Config\DatabaseConfigProvider;
use App\Infrastructure\Config\EnvDatabaseConfigProvider;
use App\Infrastructure\Persistence\PdoFactory;
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

    private ?AppKernel $appKernel = null;
    private ?AppRoutes $appRoutes = null;
    private ?RequestContext $requestContext = null;

    private ?AuthPaths $authPaths = null;
    private ?Paths $howMuchDoYouKnowPaths = null;
    private ?DevPaths $devPaths = null;
    private ?ExercisesDashboardPaths $exercisesDashboardPaths = null;

    private ?HttpMethodGuard $httpMethodGuard = null;
    private ?RouteAssembler $routeAssembler = null;
    private ?RouteCanonicalizer $routeCanonicalizer = null;
    private ?RouteUrlGenerator $routeUrlGenerator = null;

    private ?LoginController $loginController = null;
    private ?ExercisesDashboardController $exercisesDashboardController = null;
    private ?ConfigController $howMuchDoYouKnowConfigController = null;
    private ?TitleController $howMuchDoYouKnowTitleController = null;
    private ?DevExerciseSessionController $devExerciseSessionController = null;

    private ?AuthService $authService = null;
    private ?Redirector $redirector = null;
    private ?FlashMessenger $flash = null;
    private ?ExerciseSessionStore $exerciseSessionStore = null;

    private ?ConfigPayloadBuilder $howMuchDoYouKnowConfigPayloadBuilder = null;
    private ?TitlePayloadBuilder $howMuchDoYouKnowTitlePayloadBuilder = null;
    private ?TitleEvaluationService $howMuchDoYouKnowTitleEvaluationService = null;
    private ?EqualityEvaluator $equalityEvaluator = null;
    private ?TextNormalizer $textNormalizer = null;

    private ?HintService $hintService = null;

    private ?PDO $pdo = null;
    private ?DatabaseConfigProvider $databaseConfigProvider = null;
    private ?PdoFactory $pdoFactory = null;
    private ?SessionStore $sessionStore = null;
    private ?UrlGenerator $urlGenerator = null;
    private ?TopicRepository $topicRepository = null;
    private ?UserRepository $userRepository = null;

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

    private function appRoutes(): AppRoutes
    {
        if ($this->appRoutes === null) {
            $all = new RouteCollection();

            $all->merge($this->authRoutes()->routes());
            $all->merge($this->exercisesDashboardRoutes()->routes());
            $all->merge($this->howMuchDoYouKnowRoutes()->routes());
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

    private function exercisesDashboardRoutes(): ExercisesDashboardRoutes
    {
        $controller = $this->exercisesDashboardController();

        return new ExercisesDashboardRoutes(
            $this->exercisesDashboardPaths(),
            \Closure::fromCallable([$controller, 'show'])
        );
    }

    private function howMuchDoYouKnowRoutes(): Routes
    {
        $configController = $this->howMuchDoYouKnowConfigController();
        $titleController = $this->howMuchDoYouKnowTitleController();

        return new Routes(
            $this->howMuchDoYouKnowPaths(),
            \Closure::fromCallable([$configController, 'show']),
            \Closure::fromCallable([$configController, 'submit']),
            \Closure::fromCallable([$titleController, 'show']),
            \Closure::fromCallable([$titleController, 'evaluate']),
            fn() => print 'Proximamente...'
        );
    }

    private function devRoutes(): DevRoutes
    {
        $controller = $this->devExerciseSessionController();

        return new DevRoutes(
            $this->devPaths(),
            \Closure::fromCallable([$controller, 'show']),
            \Closure::fromCallable([$controller, 'next']),
            \Closure::fromCallable([$controller, 'reset'])
        );
    }

    private function authPaths(): AuthPaths
    {
        if ($this->authPaths === null) {
            $this->authPaths = new AuthPaths();
        }
        return $this->authPaths;
    }

    private function howMuchDoYouKnowPaths(): Paths
    {
        if ($this->howMuchDoYouKnowPaths === null) {
            $this->howMuchDoYouKnowPaths = new Paths(
                $this->routeUrlGenerator()
            );
        }
        return $this->howMuchDoYouKnowPaths;
    }

    private function devPaths(): DevPaths
    {
        if ($this->devPaths === null) {
            $this->devPaths = new DevPaths();
        }
        return $this->devPaths;
    }

    private function exercisesDashboardPaths(): ExercisesDashboardPaths
    {
        if ($this->exercisesDashboardPaths === null) {
            $this->exercisesDashboardPaths = new ExercisesDashboardPaths();
        }
        return $this->exercisesDashboardPaths;
    }

    private function loginController(): LoginController
    {
        if ($this->loginController === null) {
            $this->loginController = new LoginController(
                $this->authService(),
                $this->flash(),
                $this->redirector(),
                $this->sessionStore(),
                $this->userRepository()
            );
        }

        return $this->loginController;
    }

    private function exercisesDashboardController(): ExercisesDashboardController
    {
        if ($this->exercisesDashboardController === null) {
            $this->exercisesDashboardController = new ExercisesDashboardController(
                $this->authService()
            );
        }

        return $this->exercisesDashboardController;
    }

    private function howMuchDoYouKnowConfigController(): ConfigController
    {
        if ($this->howMuchDoYouKnowConfigController === null) {
            $this->howMuchDoYouKnowConfigController = new ConfigController(
                $this->exerciseSessionStore(),
                $this->authService(),
                $this->howMuchDoYouKnowConfigPayloadBuilder(),
                $this->howMuchDoYouKnowPaths(),
                $this->flash(),
                $this->redirector(),
                $this->topicRepository(),
                $this->urlGenerator()
            );
        }

        return $this->howMuchDoYouKnowConfigController;
    }

    private function howMuchDoYouKnowTitleController(): TitleController
    {
        if ($this->howMuchDoYouKnowTitleController === null) {
            $this->howMuchDoYouKnowTitleController = new TitleController(
                $this->exerciseSessionStore(),
                $this->authService(),
                $this->howMuchDoYouKnowPaths(),
                $this->howMuchDoYouKnowTitlePayloadBuilder(),
                $this->howMuchDoYouKnowTitleEvaluationService(),
                $this->redirector(),
                $this->urlGenerator()
            );
        }

        return $this->howMuchDoYouKnowTitleController;
    }

    private function devExerciseSessionController(): DevExerciseSessionController
    {
        if ($this->devExerciseSessionController === null) {
            $this->devExerciseSessionController = new DevExerciseSessionController(
                $this->exerciseSessionStore(),
                $this->redirector(),
                $this->devPaths(),
                $this->urlGenerator()
            );
        }

        return $this->devExerciseSessionController;
    }

    private function exerciseSessionStore(): ExerciseSessionStore
    {
        if ($this->exerciseSessionStore === null) {
            $this->exerciseSessionStore = new PhpExerciseSessionStore(
                $this->sessionStore()
            );
        }

        return $this->exerciseSessionStore;
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

    private function howMuchDoYouKnowConfigPayloadBuilder(): ConfigPayloadBuilder
    {
        if ($this->howMuchDoYouKnowConfigPayloadBuilder === null) {
            $this->howMuchDoYouKnowConfigPayloadBuilder = new ConfigPayloadBuilder(
                $this->topicRepository()
            );
        }

        return $this->howMuchDoYouKnowConfigPayloadBuilder;
    }

    private function howMuchDoYouKnowTitlePayloadBuilder(): TitlePayloadBuilder
    {
        if ($this->howMuchDoYouKnowTitlePayloadBuilder === null) {
            $this->howMuchDoYouKnowTitlePayloadBuilder = new TitlePayloadBuilder(
                $this->topicRepository(),
                $this->hintService()
            );
        }

        return $this->howMuchDoYouKnowTitlePayloadBuilder;
    }

    private function howMuchDoYouKnowTitleEvaluationService(): TitleEvaluationService
    {
        if ($this->howMuchDoYouKnowTitleEvaluationService === null) {
            $this->howMuchDoYouKnowTitleEvaluationService = new TitleEvaluationService(
                $this->equalityEvaluator()
            );
        }

        return $this->howMuchDoYouKnowTitleEvaluationService;
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

    private function hintService(): HintService
    {
        if ($this->hintService === null) {
            $this->hintService = new HintService();
        }

        return $this->hintService;
    }

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

    private function pdo(): PDO
    {
        if ($this->pdo === null) {
            $config = $this->databaseConfigProvider()->get();
            $this->pdo = $this->pdoFactory()->create($config);
        }
        return $this->pdo;
    }

    private function databaseConfigProvider(): DatabaseConfigProvider
    {
        if ($this->databaseConfigProvider == null) {
            $this->databaseConfigProvider = new EnvDatabaseConfigProvider();
        }
        return $this->databaseConfigProvider;
    }

    private function pdoFactory(): PdoFactory
    {
        if($this->pdoFactory == null) {
            $this->pdoFactory = new PdoFactory();
        }
        return $this->pdoFactory;
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

    private function topicRepository(): TopicRepository
    {
        if ($this->topicRepository === null) {
            $this->topicRepository = new TopicRepositorySQL(
                $this->pdo()
            );
        }

        return $this->topicRepository;
    }

    private function userRepository(): UserRepository
    {
        if ($this->userRepository === null) {
            $this->userRepository = new UserRepositorySQL(
                $this->pdo()
            );
        }

        return $this->userRepository;
    }
}