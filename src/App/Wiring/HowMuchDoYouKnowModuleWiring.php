<?php

namespace App\App\Wiring;

use App\App\Routing\ExercisesDashboardPaths;
use App\App\Routing\HowMuchDoYouKnow\Paths;
use App\App\Routing\HowMuchDoYouKnow\Routes;
use App\Application\Auth\AuthService;
use App\Application\Exercises\ExerciseSessionStore;
use App\Application\Exercises\HowMuchDoYouKnow\Bibliography\BibliographyEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\Bibliography\BibliographyPayloadBuilder;
use App\Application\Exercises\HowMuchDoYouKnow\Config\ConfigPayloadBuilder;
use App\Application\Exercises\HowMuchDoYouKnow\Index\IndexEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\Index\IndexPayloadBuilder;
use App\Application\Exercises\HowMuchDoYouKnow\Justification\JustificationEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\Justification\JustificationPayloadBuilder;
use App\Application\Exercises\HowMuchDoYouKnow\Quotes\QuotesPayloadBuilder;
use App\Application\Exercises\HowMuchDoYouKnow\Quotes\QuotesEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\SchoolContext\SchoolContextEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\SchoolContext\SchoolContextPayloadBuilder;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\DiceCoefficientSimilarityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\EqualityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\SimilarityEvaluator;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\TextNormalizer;
use App\Application\Exercises\HowMuchDoYouKnow\Title\TitleEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\Title\TitlePayloadBuilder;
use App\Application\Exercises\HowMuchDoYouKnow\Tools\ToolsEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\Tools\ToolsPayloadBuilder;
use App\Application\Exercises\HowMuchDoYouKnow\Webgraphy\WebgraphyEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\Webgraphy\WebgraphyPayloadBuilder;
use App\Application\Exercises\HowMuchDoYouKnow\WorkContext\WorkContextEvaluationService;
use App\Application\Exercises\HowMuchDoYouKnow\WorkContext\WorkContextPayloadBuilder;
use App\Application\Flash\FlashMessenger;
use App\Application\Http\Redirector;
use App\Application\Routing\RouteUrlGenerator;
use App\Application\Routing\UrlGenerator;
use App\Controllers\HowMuchDoYouKnow\BibliographyController;
use App\Controllers\HowMuchDoYouKnow\ConfigController;
use App\Controllers\HowMuchDoYouKnow\IndexController;
use App\Controllers\HowMuchDoYouKnow\JustificationController;
use App\Controllers\HowMuchDoYouKnow\QuotesController;
use App\Controllers\HowMuchDoYouKnow\ResultController;
use App\Controllers\HowMuchDoYouKnow\SchoolContextController;
use App\Controllers\HowMuchDoYouKnow\TitleController;
use App\Controllers\HowMuchDoYouKnow\ToolsController;
use App\Controllers\HowMuchDoYouKnow\WebgraphyController;
use App\Controllers\HowMuchDoYouKnow\WorkContextController;
use App\Domain\Exercise\HintService;
use App\Domain\Temas\BookRepository;
use App\Domain\Temas\JustificationRepository;
use App\Domain\Temas\QuotesRepository;
use App\Domain\Temas\SectionRepository;
use App\Domain\Temas\SchoolContextRepository;
use App\Domain\Temas\TopicRepository;
use App\Domain\Temas\ToolsRepository;
use App\Domain\Temas\WebsiteRepository;
use App\Domain\Temas\WorkContextRepository;

final class HowMuchDoYouKnowModuleWiring
{
    private ?Paths $paths = null;
    private ?Routes $routes = null;

    private ?BibliographyController $bibliographyController = null;
    private ?ConfigController $configController = null;
    private ?IndexController $indexController = null;
    private ?JustificationController $justificationController = null;
    private ?QuotesController $quotesController = null;
    private ?ResultController $resultController = null;
    private ?SchoolContextController $schoolContextController = null;
    private ?TitleController $titleController = null;
    private ?ToolsController $toolsController = null;
    private ?WebgraphyController $webgraphyController = null;
    private ?WorkContextController $workContextController = null;

    private ?BibliographyPayloadBuilder $bibliographyPayloadBuilder = null;
    private ?BibliographyEvaluationService $bibliographyEvaluationService = null;
    private ?ConfigPayloadBuilder $configPayloadBuilder = null;
    private ?IndexPayloadBuilder $indexPayloadBuilder = null;
    private ?IndexEvaluationService $indexEvaluationService = null;
    private ?JustificationPayloadBuilder $justificationPayloadBuilder = null;
    private ?JustificationEvaluationService $justificationEvaluationService = null;
    private ?QuotesPayloadBuilder $quotesPayloadBuilder = null;
    private ?QuotesEvaluationService $quotesEvaluationService = null;
    private ?SchoolContextPayloadBuilder $schoolContextPayloadBuilder = null;
    private ?SchoolContextEvaluationService $schoolContextEvaluationService = null;
    private ?TitlePayloadBuilder $titlePayloadBuilder = null;
    private ?TitleEvaluationService $titleEvaluationService = null;
    private ?ToolsPayloadBuilder $toolsPayloadBuilder = null;
    private ?ToolsEvaluationService $toolsEvaluationService = null;
    private ?WebgraphyPayloadBuilder $webgraphyPayloadBuilder = null;
    private ?WebgraphyEvaluationService $webgraphyEvaluationService = null;
    private ?WorkContextPayloadBuilder $workContextPayloadBuilder = null;
    private ?WorkContextEvaluationService $workContextEvaluationService = null;

    private ?EqualityEvaluator $equalityEvaluator = null;
    private ?SimilarityEvaluator $similarityEvaluator = null;
    private ?TextNormalizer $textNormalizer = null;

    public function __construct(
        private readonly ExerciseSessionStore $exerciseSessionStore,
        private readonly ExercisesDashboardPaths $dashboardPaths,
        private readonly AuthService $authService,
        private readonly FlashMessenger $flash,
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator,
        private readonly RouteUrlGenerator $routeUrlGenerator,
        private readonly BookRepository $bookRepository,
        private readonly JustificationRepository $justificationRepository,
        private readonly QuotesRepository $quotesRepository,
        private readonly SectionRepository $sectionRepository,
        private readonly SchoolContextRepository $schoolContextRepository,
        private readonly ToolsRepository $toolsRepository,
        private readonly TopicRepository $topicRepository,
        private readonly WorkContextRepository $workContextRepository,
        private readonly WebsiteRepository $websiteRepository,
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
            $indexController = $this->indexController();
            $justificationController = $this->justificationController();
            $quotesController = $this->quotesController();
            $schoolContextController = $this->schoolContextController();
            $toolsController = $this->toolsController();
            $workContextController = $this->workContextController();
            $bibliographyController = $this->bibliographyController();
            $webgraphyController = $this->webgraphyController();
            $resultController = $this->resultController();

            return new Routes(
                $this->paths(),
                \Closure::fromCallable([$configController, 'show']),
                \Closure::fromCallable([$configController, 'submit']),
                \Closure::fromCallable([$titleController, 'show']),
                \Closure::fromCallable([$titleController, 'evaluate']),
                \Closure::fromCallable([$indexController, 'show']),
                \Closure::fromCallable([$indexController, 'evaluate']),
                \Closure::fromCallable([$justificationController, 'show']),
                \Closure::fromCallable([$justificationController, 'evaluate']),
                \Closure::fromCallable([$quotesController, 'show']),
                \Closure::fromCallable([$quotesController, 'evaluate']),
                \Closure::fromCallable([$toolsController, 'show']),
                \Closure::fromCallable([$toolsController, 'evaluate']),
                \Closure::fromCallable([$schoolContextController, 'show']),
                \Closure::fromCallable([$schoolContextController, 'evaluate']),
                \Closure::fromCallable([$workContextController, 'show']),
                \Closure::fromCallable([$workContextController, 'evaluate']),
                \Closure::fromCallable([$bibliographyController, 'show']),
                \Closure::fromCallable([$bibliographyController, 'evaluate']),
                \Closure::fromCallable([$webgraphyController, 'show']),
                \Closure::fromCallable([$webgraphyController, 'evaluate']),
                \Closure::fromCallable([$resultController, 'show']),
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

    private function bibliographyController() : BibliographyController
    {
        /** @var BibliographyController $controller */
        $controller = $this->memoize($this->bibliographyController, function (): BibliographyController {
            return new BibliographyController(
                $this->exerciseSessionStore,
                $this->authService,
                $this->paths(),
                $this->bibliographyPayloadBuilder(),
                $this->bibliographyEvaluationService(),
                $this->redirector,
                $this->urlGenerator
            );
        });
        return $controller;
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

    private function indexController() : IndexController
    {
        /** @var IndexController $controller */
        $controller = $this->memoize($this->indexController, function (): IndexController {
            return new IndexController(
                $this->exerciseSessionStore,
                $this->authService,
                 $this->paths(),
                $this->indexPayloadBuilder(),
                $this->indexEvaluationService(),
                $this->redirector,
                $this->urlGenerator
            );
        });
        return $controller;
    }

    private function justificationController() : JustificationController
    {
        /** @var JustificationController $controller */
        $controller = $this->memoize($this->justificationController, function (): JustificationController {
            return new JustificationController(
                $this->exerciseSessionStore,
                $this->authService,
                $this->paths(),
                $this->justificationPayloadBuilder(),
                $this->justificationEvaluationService(),
                $this->redirector,
                $this->urlGenerator
            );
        });
        return $controller;
    }

    private function quotesController() : QuotesController
    {
        /** @var QuotesController $controller */
        $controller = $this->memoize($this->quotesController, function (): QuotesController {
            return new QuotesController(
                $this->exerciseSessionStore,
                $this->authService,
                $this->paths(),
                $this->quotesPayloadBuilder(),
                $this->quotesEvaluationService(),
                $this->redirector,
                $this->urlGenerator
            );
        });
        return $controller;
    }

    private function resultController() : ResultController
    {
        /** @var ResultController $controller */
        $controller = $this->memoize($this->resultController, function (): ResultController {
            return new ResultController(
                $this->authService,
                $this->dashboardPaths,
                $this->paths(),
                $this->urlGenerator
            );
        });
        return $controller;
    }

    private function schoolContextController() : SchoolContextController
    {
        /** @var SchoolContextController $controller */
        $controller = $this->memoize($this->schoolContextController, function (): SchoolContextController {
            return new SchoolContextController(
                $this->exerciseSessionStore,
                $this->authService,
                $this->paths(),
                $this->schoolContextPayloadBuilder(),
                $this->schoolContextEvaluationService(),
                $this->redirector,
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

    private function toolsController(): ToolsController
    {
        /** @var ToolsController $controller */
        $controller = $this->memoize($this->toolsController, function (): ToolsController {
            return new ToolsController(
                $this->exerciseSessionStore,
                $this->authService,
                $this->paths(),
                $this->toolsPayloadBuilder(),
                $this->toolsEvaluationService(),
                $this->redirector,
                $this->urlGenerator
            );
        });

        return $controller;
    }

    private function webgraphyController() : WebgraphyController
    {
        /** @var WebgraphyController $controller */
        $controller = $this->memoize($this->webgraphyController, function (): WebgraphyController {
            return new WebgraphyController(
                $this->exerciseSessionStore,
                $this->authService,
                $this->paths(),
                $this->webgraphyPayloadBuilder(),
                $this->webgraphyEvaluationService(),
                $this->redirector,
                $this->urlGenerator
            );
        });
        return $controller;
    }

    private function workContextController() : WorkContextController
    {
        /** @var WorkContextController $controller */
        $controller = $this->memoize($this->workContextController, function (): WorkContextController {
            return new WorkContextController(
                $this->exerciseSessionStore,
                $this->authService,
                $this->paths(),
                $this->workContextPayloadBuilder(),
                $this->workContextEvaluationService(),
                $this->redirector,
                $this->urlGenerator
            );
        });
        return $controller;
    }

    private function bibliographyPayloadBuilder(): BibliographyPayloadBuilder
    {
        /** @var  BibliographyPayloadBuilder $builder */
        $builder = $this->memoize($this->bibliographyPayloadBuilder, fn(): BibliographyPayloadBuilder => new BibliographyPayloadBuilder(
            $this->bookRepository,
            $this->hintService
        ));


        return $builder;
    }

    private function configPayloadBuilder(): ConfigPayloadBuilder
    {
        /** @var ConfigPayloadBuilder $builder */
        $builder = $this->memoize($this->configPayloadBuilder, fn(): ConfigPayloadBuilder => new ConfigPayloadBuilder(
            $this->topicRepository
        ));

        return $builder;
    }

    private function indexPayloadBuilder(): IndexPayloadBuilder
    {
        /** @var  IndexPayloadBuilder $builder */
        $builder = $this->memoize($this->indexPayloadBuilder, fn(): IndexPayloadBuilder => new IndexPayloadBuilder(
            $this->sectionRepository,
            $this->hintService
        ));

        return $builder;
    }

    private function justificationPayloadBuilder(): JustificationPayloadBuilder
    {
        /** @var  JustificationPayloadBuilder $builder */
        $builder = $this->memoize($this->justificationPayloadBuilder, fn(): JustificationPayloadBuilder => new JustificationPayloadBuilder(
            $this->justificationRepository,
            $this->hintService
        ));


        return $builder;
    }

    private function quotesPayloadBuilder(): QuotesPayloadBuilder
    {
        /** @var  QuotesPayloadBuilder $builder */
        $builder = $this->memoize($this->quotesPayloadBuilder, fn(): QuotesPayloadBuilder => new QuotesPayloadBuilder(
            $this->quotesRepository,
            $this->hintService
        ));

        return $builder;
    }

    private function schoolContextPayloadBuilder(): SchoolContextPayloadBuilder
    {
        /** @var SchoolContextPayloadBuilder $builder */
        $builder = $this->memoize($this->schoolContextPayloadBuilder, fn(): SchoolContextPayloadBuilder => new SchoolContextPayloadBuilder(
            $this->schoolContextRepository,
            $this->hintService
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

    private function toolsPayloadBuilder(): ToolsPayloadBuilder
    {
        /** @var ToolsPayloadBuilder $builder */
        $builder = $this->memoize($this->toolsPayloadBuilder, fn(): ToolsPayloadBuilder => new ToolsPayloadBuilder(
            $this->toolsRepository,
            $this->hintService
        ));

        return $builder;
    }

    private function webgraphyPayloadBuilder(): WebgraphyPayloadBuilder
    {
        /** @var webgraphyPayloadBuilder $builder */
        $builder = $this->memoize($this->webgraphyPayloadBuilder, fn(): WebgraphyPayloadBuilder => new WebgraphyPayloadBuilder(
            $this->websiteRepository,
            $this->hintService
        ));

        return $builder;
    }

    private function workContextPayloadBuilder(): WorkContextPayloadBuilder
    {
        /** @var WorkContextPayloadBuilder $builder */
        $builder = $this->memoize($this->workContextPayloadBuilder, fn(): WorkContextPayloadBuilder => new WorkContextPayloadBuilder(
            $this->workContextRepository,
            $this->hintService
        ));

        return $builder;
    }

    private function bibliographyEvaluationService(): BibliographyEvaluationService
    {
        /** @var BibliographyEvaluationService $service */
        $service = $this->memoize($this->bibliographyEvaluationService, fn(): BibliographyEvaluationService => new BibliographyEvaluationService(
            $this->equalityEvaluator()
        ));


        return $service;
    }

    private function indexEvaluationService(): IndexEvaluationService
    {
        /** @var IndexEvaluationService $service */
        $service = $this->memoize($this->indexEvaluationService, fn(): IndexEvaluationService => new IndexEvaluationService(
            $this->equalityEvaluator()
        ));

        return $service;
    }

    private function justificationEvaluationService(): JustificationEvaluationService
    {
        /** @var JustificationEvaluationService $service */
        $service = $this->memoize($this->justificationEvaluationService, fn(): JustificationEvaluationService => new JustificationEvaluationService(
            $this->equalityEvaluator()
        ));
        
        return $service;
    }

    private function quotesEvaluationService(): QuotesEvaluationService
    {
        /** @var QuotesEvaluationService $service */
        $service = $this->memoize($this->quotesEvaluationService, fn(): QuotesEvaluationService => new QuotesEvaluationService(
            $this->equalityEvaluator(),
            $this->similarityEvaluator()
        ));
        
        return $service;
    }

    private function schoolContextEvaluationService(): SchoolContextEvaluationService
    {
        /** @var SchoolContextEvaluationService $service */
        $service = $this->memoize($this->schoolContextEvaluationService, fn(): SchoolContextEvaluationService => new SchoolContextEvaluationService(
            $this->equalityEvaluator(),
            $this->similarityEvaluator()
        ));
        
        return $service;
    }

    private function titleEvaluationService(): TitleEvaluationService
    {
        /** @var TitleEvaluationService $service */
        $service = $this->memoize($this->titleEvaluationService, fn(): TitleEvaluationService => new TitleEvaluationService(
            $this->equalityEvaluator()
        ));

        return $service;
    }

    private function toolsEvaluationService(): ToolsEvaluationService
    {
        /** @var ToolsEvaluationService $service */
        $service = $this->memoize($this->toolsEvaluationService, fn(): ToolsEvaluationService => new ToolsEvaluationService(
            $this->equalityEvaluator(),
            $this->similarityEvaluator()
        ));

        return $service;
    }

    private function webgraphyEvaluationService(): WebgraphyEvaluationService
    {
        /** @var WebgraphyEvaluationService $service */
        $service = $this->memoize($this->webgraphyEvaluationService, fn(): WebgraphyEvaluationService => new WebgraphyEvaluationService(
            $this->equalityEvaluator()
        ));
        
        return $service;
    }

    private function workContextEvaluationService(): WorkContextEvaluationService
    {
        /** @var WorkContextEvaluationService $service */
        $service = $this->memoize($this->workContextEvaluationService, fn(): WorkContextEvaluationService => new WorkContextEvaluationService(
            $this->equalityEvaluator(),
            $this->similarityEvaluator()
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

    private function similarityEvaluator(): SimilarityEvaluator
    {
        /** @var SimilarityEvaluator $evaluator */
        $evaluator = $this->memoize($this->similarityEvaluator, fn(): SimilarityEvaluator => new DiceCoefficientSimilarityEvaluator());

        return $evaluator;
    }

    private function textNormalizer(): TextNormalizer
    {
        /** @var TextNormalizer $normalizer */
        $normalizer = $this->memoize($this->textNormalizer, fn(): TextNormalizer => new TextNormalizer());
        return $normalizer;
    }
}