<?php
namespace App\Controllers;

use App\App\Routing\CuantoSabesTemaPaths;
use App\Application\Auth\AuthService;
use App\Application\Exercises\AlmacenSesionEjercicio;
use App\Application\Exercises\StepBuilder\CuantoSabesTemaTituloPayloadBuilder;
use App\Application\Exercises\Evaluation\CuantoSabesTemaTituloEvaluationService;
use App\Application\Http\Redirector;
use App\Application\Routing\UrlGenerator;
use App\Core\View;
use App\Domain\Exercise\PasoEjercicio;

final class CuantoSabesTemaTituloController
{
    public function __construct(
        private readonly AlmacenSesionEjercicio $almacenSesionEjercicio,
        private readonly AuthService $authService,
        private readonly CuantoSabesTemaPaths $cuantoSabesTemaPaths,
        private readonly CuantoSabesTemaTituloPayloadBuilder $payloadBuilder,
        private readonly CuantoSabesTemaTituloEvaluationService $evaluacionServicio,
        private readonly Redirector $redirector,
        private readonly UrlGenerator $urlGenerator
    ) {}

    public function mostrar(): void
    {
        $this->authService->requireLogin();

        $sesion = $this->almacenSesionEjercicio->getSesionActual();

        $payload = $this->payloadBuilder->construir($sesion);

        $evaluacion = $sesion->getEvaluacionPaso(PasoEjercicio::TITULO);

        View::render('exercises/CuantoSabesTemaTitulo.php', [
            'payload' => $payload,
            'sesionId' => $sesion->sesionId(),
            'evaluacion' => $evaluacion,
            'url' => $this->urlGenerator,
            'cuantoSabesTemaPaths' => $this->cuantoSabesTemaPaths
        ]);
    }

    public function evaluar(): void
    {
        $this->authService->requireLogin();

        $sesion = $this->almacenSesionEjercicio->getSesionActual();

        $payload = $this->payloadBuilder->construir($sesion);
             
        $stepAnswer = $this->buildStepAnswerFromPost($payload, PasoEjercicio::TITULO->value);

        $evaluacion = $this->evaluacionServicio->evaluate($payload, $stepAnswer);

        $sesion->setEvaluacionPaso(PasoEjercicio::TITULO, $evaluacion);
        $this->almacenSesionEjercicio->guardar($sesion);

        $this->redirector->redirect($this->cuantoSabesTemaPaths->pasoTitulo($sesion->sesionId()));
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