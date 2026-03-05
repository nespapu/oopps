<?php

namespace App\Controllers\HowMuchDoYouKnow;

use App\App\Routing\HowMuchDoYouKnow\Paths;
use App\Application\Auth\AuthService;
use App\Application\Exercises\ExerciseSessionStore;
use App\Application\Exercises\HowMuchDoYouKnow\Config\ConfigPayloadBuilder;
use App\Application\Flash\FlashMessenger;
use App\Application\Http\Redirector;
use App\Application\Routing\UrlGenerator;
use App\Core\View;
use App\Domain\Exercise\Difficulty;
use App\Domain\Exercise\ExerciseConfig;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\ExerciseType;
use App\Domain\Temas\TopicRepository;

final class ConfigController
{
    public function __construct(
        private readonly ExerciseSessionStore $exerciseSessionStore,
        private readonly AuthService $authService,
        private readonly ConfigPayloadBuilder $payloadBuilder,
        private readonly Paths $paths,
        private readonly FlashMessenger $flash,
        private readonly Redirector $redirector,
        private readonly TopicRepository $topicRepository,
        private readonly UrlGenerator $urlGenerator
    ) {}

    public function show(): void
    {
        $this->authService->requireOppositionContext();

        $error = $this->flash->get('error');
        $userContext = $this->authService->userContext();

        $payload = $this->payloadBuilder->build($userContext);
        $payload['error'] = $error;

        View::render('exercises/how-much-do-you-know/config', [
            'payload' => $payload,
            'url' => $this->urlGenerator,
            'howMuchDoYouKnowPaths' => $this->paths,
        ]);
    }

    public function submit(): void
    {
        $this->authService->requireOppositionContext();
        $userContext = $this->authService->userContext();

        $rawTopicOrder = $_POST['topicOrder'] ?? null;
        $rawDifficulty = $_POST['difficulty'] ?? null;

        $topicOrder = is_numeric($rawTopicOrder) ? (int) $rawTopicOrder : -1;
        $difficultyValue = is_numeric($rawDifficulty) ? (int) $rawDifficulty : -1;

        if ($topicOrder < 0) {
            $this->flash->set('error', 'Tema no válido.');
            $this->redirector->redirect($this->paths->config());
        }

        $difficulty = Difficulty::tryFrom($difficultyValue);
        if ($difficulty === null) {
            $this->flash->set('error', 'Dificultad no válida.');
            $this->redirector->redirect($this->paths->config());
        }

        if ($topicOrder === 0) {
            $randomTopicOrder = $this->topicRepository->findRandomOrderByOppositionCode($userContext->oppositionCode());

            if ($randomTopicOrder === null) {
                $this->flash->set('error', 'No hay temas disponibles para esta oposición.');
                $this->redirector->redirect($this->paths->config());
            }

            $topicOrder = $randomTopicOrder;
        }

        $topicTitle = $this->topicRepository->findTitleByOppositionCodeAndOrder($userContext->oppositionCode(), $topicOrder);
        if ($topicTitle === null) {
            $this->flash->set('error', 'El tema seleccionado no existe.');
            $this->redirector->redirect($this->paths->config());
        }

        $exerciseConfig = new ExerciseConfig(
            $topicOrder,
            $difficultyValue,
            []
        );

        $exerciseType = ExerciseType::howMuchDoYouKnowTopic();
        $firstExerciseStep = ExerciseStep::first();

        $session = $this->exerciseSessionStore->create(
            $exerciseType,
            $userContext,
            $exerciseConfig,
            $firstExerciseStep
        );

        $this->redirector->redirect($this->paths->titleStep($session->sessionId()));
    }
}