<?php

namespace App\App\Wiring;

use App\App\Routing\HowMuchDoYouKnow\Paths;
use App\App\Routing\HowMuchDoYouKnow\Routes;
use App\Application\Auth\AuthService;
use App\Application\Exercises\ExerciseSessionStore;
use App\Application\Exercises\HowMuchDoYouKnow\Config\ConfigPayloadBuilder;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\EqualityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\TextNormalizer;
use App\Application\Exercises\HowMuchDoYouKnow\Title\TitleEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\Title\TitlePayloadBuilder;
use App\Application\Flash\FlashMessenger;
use App\Application\Http\Redirector;
use App\Application\Routing\RouteUrlGenerator;
use App\Application\Routing\UrlGenerator;
use App\Controllers\HowMuchDoYouKnow\ConfigController;
use App\Controllers\HowMuchDoYouKnow\TitleController;
use App\Domain\Exercise\HintService;
use App\Domain\Temas\TopicRepository;

final class HowMuchDoYouKnowModuleWiring
{
    private ?Paths $paths = null;
    private ?Routes $routes = null;

    private ?ConfigController $configController = null;
    private ?TitleController $titleController = null;

    private ?ConfigPayloadBuilder $configPayloadBuilder = null;
    private ?TitlePayloadBuilder $titlePayloadBuilder = null;
    private ?TitleEvaluationService $titleEvaluationService = null;

    private ?EqualityEvaluator $equalityEvaluator = null;
    private ?TextNormalizer $textNormalizer = null;

    public function __construct(
        private readonly ExerciseSessionStore $exerciseSessionStore,
        private readonly AuthService $authService,
        private readonly FlashMessenger $flash,
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator,
        private readonly RouteUrlGenerator $routeUrlGenerator,
        private readonly TopicRepository $topicRepository,
        private readonly HintService $hintService
    ) {}

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

    public function routes(): Routes
    {
        /** @var Routes $routes */
        $routes = $this->memoize($this->routes, function (): Routes {
            $configController = $this->configController();
            $titleController = $this->titleController();

            return new Routes(
                $this->paths(),
                \Closure::fromCallable([$configController, 'show']),
                \Closure::fromCallable([$configController, 'submit']),
                \Closure::fromCallable([$titleController, 'show']),
                \Closure::fromCallable([$titleController, 'evaluate']),
                fn() => print 'Proximamente...'
            );
        });

        return $routes;
    }

    public function paths(): Paths
    {
        /** @var Paths $paths */
        $paths = $this->memoize($this->paths, fn(): Paths => new Paths($this->routeUrlGenerator));
        return $paths;
    }

    private function configController(): ConfigController
    {
        /** @var ConfigController $controller */
        $controller = $this->memoize($this->configController, function (): ConfigController {
            return new ConfigController(
                $this->exerciseSessionStore,
                $this->authService,
                $this->configPayloadBuilder(),
                $this->paths(),
                $this->flash,
                $this->redirector,
                $this->topicRepository,
                $this->urlGenerator
            );
        });

        return $controller;
    }

    private function titleController(): TitleController
    {
        /** @var TitleController $controller */
        $controller = $this->memoize($this->titleController, function (): TitleController {
            return new TitleController(
                $this->exerciseSessionStore,
                $this->authService,
                $this->paths(),
                $this->titlePayloadBuilder(),
                $this->titleEvaluationService(),
                $this->redirector,
                $this->urlGenerator
            );
        });

        return $controller;
    }

    private function configPayloadBuilder(): ConfigPayloadBuilder
    {
        /** @var ConfigPayloadBuilder $builder */
        $builder = $this->memoize($this->configPayloadBuilder, fn(): ConfigPayloadBuilder => new ConfigPayloadBuilder(
            $this->topicRepository
        ));

        return $builder;
    }

    private function titlePayloadBuilder(): TitlePayloadBuilder
    {
        /** @var TitlePayloadBuilder $builder */
        $builder = $this->memoize($this->titlePayloadBuilder, fn(): TitlePayloadBuilder => new TitlePayloadBuilder(
            $this->topicRepository,
            $this->hintService
        ));

        return $builder;
    }

    private function titleEvaluationService(): TitleEvaluationService
    {
        /** @var TitleEvaluationService $service */
        $service = $this->memoize($this->titleEvaluationService, fn(): TitleEvaluationService => new TitleEvaluationService(
            $this->equalityEvaluator()
        ));

        return $service;
    }

    private function equalityEvaluator(): EqualityEvaluator
    {
        /** @var EqualityEvaluator $evaluator */
        $evaluator = $this->memoize($this->equalityEvaluator, fn(): EqualityEvaluator => new EqualityEvaluator(
            $this->textNormalizer()
        ));

        return $evaluator;
    }

    private function textNormalizer(): TextNormalizer
    {
        /** @var TextNormalizer $normalizer */
        $normalizer = $this->memoize($this->textNormalizer, fn(): TextNormalizer => new TextNormalizer());
        return $normalizer;
    }
}