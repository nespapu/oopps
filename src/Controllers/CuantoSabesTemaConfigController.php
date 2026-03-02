<?php
namespace App\Controllers;

use App\App\Routing\CuantoSabesTemaPaths;
use App\Application\Auth\AuthService;
use App\Application\Exercises\ExerciseSessionStore;
use App\Application\Exercises\CuantoSabesTemaConfigPayloadBuilder;
use App\Application\Flash\FlashMessenger;
use App\Application\Http\Redirector;
use App\Application\Routing\UrlGenerator;
use App\Core\View;
use App\Domain\Exercise\ExerciseConfig;
use App\Domain\Exercise\Difficulty;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\ExerciseType;
use App\Domain\Temas\TopicRepository;

final class CuantoSabesTemaConfigController
{
    public function __construct(
        private readonly ExerciseSessionStore $almacenSesionEjercicio,
        private readonly AuthService $authService,
        private readonly CuantoSabesTemaConfigPayloadBuilder $payloadBuilder,
        private readonly CuantoSabesTemaPaths $cuantoSabesTemaPaths,
        private readonly FlashMessenger $flash,
        private readonly Redirector $redirector,
        private readonly TopicRepository $temaRepositorio,
        private readonly UrlGenerator $urlGenerator
    ) {}

    public function mostrar(): void
    {
        $this->authService->requireOppositionContext();

        $error = $this->flash->get('error');
        $contextoUsuario = $this->authService->userContext();

        $payload = $this->payloadBuilder->construir($contextoUsuario);
        $payload['error'] = $error;
        $payload['titulo'] = "Configuración";

        View::render('exercises/CuantoSabesTemaConfiguracion.php', [
            'payload' => $payload,
            'url' => $this->urlGenerator,
            'cuantoSabesTemaPaths' => $this->cuantoSabesTemaPaths
        ]);
    }

    public function comprobar(): void
    {
        $this->authService->requireOppositionContext();
        $contextoUsuario = $this->authService->userContext();

        $numeracionTemaBruto = $_POST['numeracionTema'] ?? null;
        $dificultadBruto = $_POST['dificultad'] ?? null;

        $numeracion = is_numeric($numeracionTemaBruto) ? (int)$numeracionTemaBruto : -1;
        $dificultad = is_numeric($dificultadBruto) ? (int)$dificultadBruto : -1;

        if ($numeracion < 0) {
            $this->flash->set('error', 'Tema inválido.');
            $this->redirector->redirect($this->cuantoSabesTemaPaths->config());
        }

        $dificultadEnum = Difficulty::tryFrom($dificultad);
        if ($dificultadEnum === null) {
            $this->flash->set('error', 'Dificultad inválida.');
            $this->redirector->redirect($this->cuantoSabesTemaPaths->config());
        }

        if ($numeracion === 0) {
            $numeracionAleatoria = $this->temaRepositorio->findRandomOrderByOppositionCode($contextoUsuario->oppositionCode());

            if ($numeracionAleatoria === null) {
                $this->flash->set('error', 'No hay temas disponibles para esta oposición.');
                $this->redirector->redirect($this->cuantoSabesTemaPaths->config());
            }

            $numeracion = $numeracionAleatoria;
        }

        $tituloTema = $this->temaRepositorio->findTitleByOppositionCodeAndOrder($contextoUsuario->oppositionCode(), $numeracion);
        if ($tituloTema === null) {
            $this->flash->set('error', 'El tema seleccionado no existe.');
            $this->redirector->redirect($this->cuantoSabesTemaPaths->config());
        }

        $exerciseConfig = new ExerciseConfig(
            $numeracion,
            $dificultad,
            []
        );
        $tipoEjercicio = ExerciseType::howMuchDoYouKnowTopic();
        $firstExerciseStep = ExerciseStep::first();

        $sesion = $this->almacenSesionEjercicio->create(
            $tipoEjercicio,
            $contextoUsuario,
            $exerciseConfig,
            $firstExerciseStep
        );

        $this->redirector->redirect($this->cuantoSabesTemaPaths->pasoTitulo($sesion->sessionId()));
    }
}
?>