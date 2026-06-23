<?php


namespace App\Controllers\HowMuchDoYouKnow;


use App\App\Routing\HowMuchDoYouKnow\Paths;
use App\Application\Auth\AuthService;
use App\Application\Exercises\ExerciseSessionStore;
use App\Application\Exercises\HowMuchDoYouKnow\Justification\JustificationPayloadBuilder;
use App\Application\Exercises\HowMuchDoYouKnow\Shared\StepPayloadKeys;
use App\Application\Http\Redirector;
use App\Application\Routing\UrlGenerator;
use App\Core\View;
use App\Domain\Exercise\ExerciseStep;


final class JustificationController
{
    public function __construct(
        private readonly ExerciseSessionStore $exerciseSessionStore,
        private readonly AuthService $authService,
        private readonly Paths $paths,
        private readonly JustificationPayloadBuilder $payloadBuilder,
        //TODO inject justification evaluation service
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator
    ) {}


    public function show(): void
    {
        $this->authService->requireLogin();

        $session = $this->exerciseSessionStore->getCurrentSession();

        $payload = $this->payloadBuilder->build($session);

        $stepAnswer = $session->getStepAnswer(ExerciseStep::JUSTIFICATION);
        
        $evaluation = $session->getStepEvaluation(ExerciseStep::JUSTIFICATION);

        View::render('exercises/how-much-do-you-know/justification', [
            'payload' => $payload,
            'sessionId' => $session->sessionId(),
            'stepAnswer' => $stepAnswer,
            'evaluation' => $evaluation,
            'url' => $this->urlGenerator,
            'howMuchDoYouKnowPaths' => $this->paths,
        ]);

    }


    public function evaluate(): void
    {
        $this->authService->requireLogin();

        $session = $this->exerciseSessionStore->getCurrentSession();
        
        $payload = $this->payloadBuilder->build($session);
        
        $stepAnswer = $this->buildStepAnswerFromPost($payload, $_POST, ExerciseStep::JUSTIFICATION->value);
        
        $evaluation = []; //TODO
        
        $session->setStepAnswer(ExerciseStep::JUSTIFICATION, $stepAnswer);
        
        $session->setStepEvaluation(ExerciseStep::JUSTIFICATION, $evaluation);
        
        $this->exerciseSessionStore->save($session);
        
        $this->redirector->redirect($this->paths->justificationStep($session->sessionId()));
    }


    private function buildStepAnswerFromPost(array $payload, array $postData, string $step): array
    {
        $values = [];

        return [
            'step' => $step,
            'values' => $values,
        ];
    }
}