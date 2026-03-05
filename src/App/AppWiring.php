<?php

namespace App\App;

use App\App\Http\AppKernel;
use App\App\Http\AppRoutes;
use App\App\Routing\AuthPaths;
use App\App\Routing\AuthRoutes;
use App\App\Routing\DevPaths;
use App\App\Routing\DevRoutes;
use App\App\Routing\ExercisesDashboardPaths;
use App\App\Routing\ExercisesDashboardRoutes;
use App\App\Wiring\HowMuchDoYouKnowModuleWiring;
use App\Application\Auth\AuthService;
use App\Application\Exercises\ExerciseSessionStore;
use App\Application\Flash\FlashMessenger;
use App\Application\Http\HttpMethodGuard;
use App\Application\Http\Redirector;
use App\Application\Http\RequestContext;
use App\Application\Routing\RouteCanonicalizer;
use App\Application\Routing\RouteUrlGenerator;
use App\Application\Routing\UrlGenerator;
use App\Application\Session\SessionStore;
use App\Controllers\Dev\DevExerciseSessionController;
use App\Controllers\ExercisesDashboardController;
use App\Controllers\LoginController;
use App\Domain\Auth\UserRepository;
use App\Domain\Exercise\HintService;
use App\Domain\Temas\TopicRepository;
use App\Infrastructure\Auth\DefaultAuthService;
use App\Infrastructure\Config\DatabaseConfigProvider;
use App\Infrastructure\Config\EnvDatabaseConfigProvider;
use App\Infrastructure\Flash\SessionFlashMessenger;
use App\Infrastructure\Http\DefaultHttpMethodGuard;
use App\Infrastructure\Http\HeaderRedirector;
use App\Infrastructure\Http\ServerRequestContext;
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
    private ?DevPaths $devPaths = null;
    private ?ExercisesDashboardPaths $exercisesDashboardPaths = null;

    private ?HttpMethodGuard $httpMethodGuard = null;
    private ?RouteAssembler $routeAssembler = null;
    private ?RouteCanonicalizer $routeCanonicalizer = null;
    private ?RouteUrlGenerator $routeUrlGenerator = null;

    private ?LoginController $loginController = null;
    private ?ExercisesDashboardController $exercisesDashboardController = null;
    private ?DevExerciseSessionController $devExerciseSessionController = null;

    private ?HowMuchDoYouKnowModuleWiring $howMuchDoYouKnowModule = null;

    private ?AuthService $authService = null;
    private ?Redirector $redirector = null;
    private ?FlashMessenger $flash = null;
    private ?ExerciseSessionStore $exerciseSessionStore = null;

    private ?HintService $hintService = null;

    private ?PDO $pdo = null;
    private ?DatabaseConfigProvider $databaseConfigProvider = null;
    private ?PdoFactory $pdoFactory = null;
    private ?SessionStore $sessionStore = null;
    private ?UrlGenerator $urlGenerator = null;
    private ?TopicRepository $topicRepository = null;
    private ?UserRepository $userRepository = null;

    /**
     * @template T of object
     * @param T|null $slot
     * @param callable():T $factory
     * @return T
     */
    private function memoize(?object &$slot, callable $factory): object
    {
        if ($slot === null) {
            $slot = $factory();
        }

        return $slot;
    }

    public function appKernel(): AppKernel
    {
        /** @var AppKernel $kernel */
        $kernel = $this->memoize($this->appKernel, fn(): AppKernel => new AppKernel(
            $this->appRoutes(),
            $this->requestContext(),
            $this->routeCanonicalizer()
        ));

        return $kernel;
    }

    private function appRoutes(): AppRoutes
    {
        /** @var AppRoutes $routes */
        $routes = $this->memoize($this->appRoutes, function (): AppRoutes {
            $all = new RouteCollection();

            $all->merge($this->authRoutes()->routes());
            $all->merge($this->exercisesDashboardRoutes()->routes());
            $all->merge($this->howMuchDoYouKnowModule()->routes()->routes());
            $all->merge($this->devRoutes()->routes());

            return new AppRoutes($this->routeAssembler()->assemble($all));
        });

        return $routes;
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
        /** @var AuthPaths $paths */
        $paths = $this->memoize($this->authPaths, fn(): AuthPaths => new AuthPaths());
        return $paths;
    }

    private function devPaths(): DevPaths
    {
        /** @var DevPaths $paths */
        $paths = $this->memoize($this->devPaths, fn(): DevPaths => new DevPaths());
        return $paths;
    }

    private function exercisesDashboardPaths(): ExercisesDashboardPaths
    {
        /** @var ExercisesDashboardPaths $paths */
        $paths = $this->memoize($this->exercisesDashboardPaths, fn(): ExercisesDashboardPaths => new ExercisesDashboardPaths());
        return $paths;
    }

    private function loginController(): LoginController
    {
        /** @var LoginController $controller */
        $controller = $this->memoize($this->loginController, fn(): LoginController => new LoginController(
            $this->authService(),
            $this->flash(),
            $this->redirector(),
            $this->sessionStore(),
            $this->userRepository()
        ));

        return $controller;
    }

    private function exercisesDashboardController(): ExercisesDashboardController
    {
        /** @var ExercisesDashboardController $controller */
        $controller = $this->memoize($this->exercisesDashboardController, fn(): ExercisesDashboardController => new ExercisesDashboardController(
            $this->authService()
        ));

        return $controller;
    }

    private function devExerciseSessionController(): DevExerciseSessionController
    {
        /** @var DevExerciseSessionController $controller */
        $controller = $this->memoize($this->devExerciseSessionController, fn(): DevExerciseSessionController => new DevExerciseSessionController(
            $this->exerciseSessionStore(),
            $this->redirector(),
            $this->devPaths(),
            $this->urlGenerator()
        ));

        return $controller;
    }

    private function howMuchDoYouKnowModule(): HowMuchDoYouKnowModuleWiring
    {
        /** @var HowMuchDoYouKnowModuleWiring $module */
        $module = $this->memoize($this->howMuchDoYouKnowModule, fn(): HowMuchDoYouKnowModuleWiring => new HowMuchDoYouKnowModuleWiring(
            $this->exerciseSessionStore(),
            $this->authService(),
            $this->flash(),
            $this->redirector(),
            $this->urlGenerator(),
            $this->routeUrlGenerator(),
            $this->topicRepository(),
            $this->hintService()
        ));

        return $module;
    }

    private function exerciseSessionStore(): ExerciseSessionStore
    {
        /** @var ExerciseSessionStore $store */
        $store = $this->memoize($this->exerciseSessionStore, fn(): ExerciseSessionStore => new PhpExerciseSessionStore(
            $this->sessionStore()
        ));

        return $store;
    }

    private function authService(): AuthService
    {
        /** @var AuthService $service */
        $service = $this->memoize($this->authService, fn(): AuthService => new DefaultAuthService(
            $this->sessionStore(),
            $this->requestContext(),
            $this->redirector(),
            $this->flash()
        ));

        return $service;
    }

    private function flash(): FlashMessenger
    {
        /** @var FlashMessenger $flash */
        $flash = $this->memoize($this->flash, fn(): FlashMessenger => new SessionFlashMessenger());
        return $flash;
    }

    private function redirector(): Redirector
    {
        /** @var Redirector $redirector */
        $redirector = $this->memoize($this->redirector, fn(): Redirector => new HeaderRedirector(
            $this->urlGenerator()
        ));

        return $redirector;
    }

    private function hintService(): HintService
    {
        /** @var HintService $service */
        $service = $this->memoize($this->hintService, fn(): HintService => new HintService());
        return $service;
    }

    private function requestContext(): RequestContext
    {
        /** @var RequestContext $ctx */
        $ctx = $this->memoize($this->requestContext, fn(): RequestContext => new ServerRequestContext());
        return $ctx;
    }

    private function httpMethodGuard(): HttpMethodGuard
    {
        /** @var HttpMethodGuard $guard */
        $guard = $this->memoize($this->httpMethodGuard, fn(): HttpMethodGuard => new DefaultHttpMethodGuard(
            $this->requestContext()
        ));

        return $guard;
    }

    private function routeAssembler(): RouteAssembler
    {
        /** @var RouteAssembler $assembler */
        $assembler = $this->memoize($this->routeAssembler, fn(): RouteAssembler => new RouteAssembler(
            $this->httpMethodGuard()
        ));

        return $assembler;
    }

    private function routeCanonicalizer(): RouteCanonicalizer
    {
        /** @var RouteCanonicalizer $canonicalizer */
        $canonicalizer = $this->memoize($this->routeCanonicalizer, fn(): RouteCanonicalizer => new RouteCanonicalizer());
        return $canonicalizer;
    }

    private function routeUrlGenerator(): RouteUrlGenerator
    {
        /** @var RouteUrlGenerator $generator */
        $generator = $this->memoize($this->routeUrlGenerator, fn(): RouteUrlGenerator => new RouteUrlGenerator());
        return $generator;
    }

    private function pdo(): PDO
    {
        /** @var PDO $pdo */
        $pdo = $this->memoize($this->pdo, function (): PDO {
            $config = $this->databaseConfigProvider()->get();
            return $this->pdoFactory()->create($config);
        });

        return $pdo;
    }

    private function databaseConfigProvider(): DatabaseConfigProvider
    {
        /** @var DatabaseConfigProvider $provider */
        $provider = $this->memoize($this->databaseConfigProvider, fn(): DatabaseConfigProvider => new EnvDatabaseConfigProvider());
        return $provider;
    }

    private function pdoFactory(): PdoFactory
    {
        /** @var PdoFactory $factory */
        $factory = $this->memoize($this->pdoFactory, fn(): PdoFactory => new PdoFactory());
        return $factory;
    }

    private function sessionStore(): SessionStore
    {
        /** @var SessionStore $store */
        $store = $this->memoize($this->sessionStore, fn(): SessionStore => new PhpSessionStore());
        return $store;
    }

    private function urlGenerator(): UrlGenerator
    {
        /** @var UrlGenerator $generator */
        $generator = $this->memoize($this->urlGenerator, fn(): UrlGenerator => new ScriptNameUrlGenerator());
        return $generator;
    }

    private function topicRepository(): TopicRepository
    {
        /** @var TopicRepository $repo */
        $repo = $this->memoize($this->topicRepository, fn(): TopicRepository => new TopicRepositorySQL(
            $this->pdo()
        ));

        return $repo;
    }

    private function userRepository(): UserRepository
    {
        /** @var UserRepository $repo */
        $repo = $this->memoize($this->userRepository, fn(): UserRepository => new UserRepositorySQL(
            $this->pdo()
        ));

        return $repo;
    }
}