<?php
namespace App\Controllers;

use App\App\Routing\CuantoSabesTemaPaths;
use App\Application\Auth\AuthService;
use App\Application\Exercises\ExerciseSessionStore;
use App\Application\Exercises\StepBuilder\HowMuchDoYouKnowTitlePayloadBuilder;
use App\Application\Exercises\Evaluation\HowMuchDoYouKnowTitleEvaluationService;
use App\Application\Http\Redirector;
use App\Application\Routing\UrlGenerator;
use App\Core\View;
use App\Domain\Exercise\ExerciseStep;

final class CuantoSabesTemaTituloController
{
    public function __construct(
        private readonly ExerciseSessionStore $almacenSesionEjercicio,
        private readonly AuthService $authService,
        private readonly CuantoSabesTemaPaths $cuantoSabesTemaPaths,
        private readonly HowMuchDoYouKnowTitlePayloadBuilder $payloadBuilder,
        private readonly HowMuchDoYouKnowTitleEvaluationService $evaluacionServicio,
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator
    ) {}

    public function mostrar(): void
    {
        $this->authService->requireLogin();

        $sesion = $this->almacenSesionEjercicio->getCurrentSession();

        $payload = $this->payloadBuilder->build($sesion);

        $evaluacion = $sesion->getStepEvaluation(ExerciseStep::TITLE);

        View::render('exercises/CuantoSabesTemaTitulo.php', [
            'payload' => $payload,
            'sesionId' => $sesion->sessionId(),
            'evaluacion' => $evaluacion,
            'url' => $this->urlGenerator,
            'cuantoSabesTemaPaths' => $this->cuantoSabesTemaPaths
        ]);
    }

    public function evaluar(): void
    {
        $this->authService->requireLogin();

        $sesion = $this->almacenSesionEjercicio->getCurrentSession();

        $payload = $this->payloadBuilder->build($sesion);
             
        $stepAnswer = $this->buildStepAnswerFromPost($payload, ExerciseStep::TITLE->value);

        $evaluacion = $this->evaluacionServicio->evaluate($payload, $stepAnswer);

        $sesion->setStepEvaluation(ExerciseStep::TITLE, $evaluacion);
        $this->almacenSesionEjercicio->save($sesion);

        $this->redirector->redirect($this->cuantoSabesTemaPaths->pasoTitulo($sesion->sessionId()));
    }

    private function buildStepAnswerFromPost(array $payload, string $step): array
    {
        $values = [];

        foreach ($payload['items'] as $item) {
            $key = $item['key'];
            $values[$key] = isset($_POST[$key]) ? trim((string) $_POST[$key]) : '';
        }

        return ['step' => $step, 'values' => $values];
    }


}
?>