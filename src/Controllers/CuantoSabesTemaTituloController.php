<?php
namespace App\Controllers;

use App\Application\Auth\AuthService;
use App\Application\Exercises\StepBuilder\CuantoSabesTemaTituloPayloadBuilder;
use App\Application\Exercises\Evaluation\CuantoSabesTemaTituloEvaluationService;
use App\Application\Http\Redirector;
use App\Application\Routing\UrlGenerator;
use App\Core\Routes\RutasCuantoSabesTema;
use App\Core\View;
use App\Domain\Exercise\PasoEjercicio;
use App\Domain\Temas\TemaRepository;
use App\Infrastructure\Session\AlmacenSesionEjercicio;

final class CuantoSabesTemaTituloController
{
    public function __construct(
        private readonly AlmacenSesionEjercicio $almacenSesionEjercicio,
        private readonly AuthService $authService,
        private readonly CuantoSabesTemaTituloPayloadBuilder $payloadBuilder,
        private readonly CuantoSabesTemaTituloEvaluationService $evaluacionServicio,
        private readonly Redirector $redirector,
        private readonly TemaRepository $temaRepositorio,
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
            'url' => $this->urlGenerator
        ]);
    }

    public function evaluar(): void
    {
        $this->authService->requireLogin();

        $sesion = $this->almacenSesionEjercicio->getSesionActual();

        $codigoOposicion = $sesion->contextoUsuario()['oposicionId'];
        $numeracion = $sesion->config()->tema();
             
        $respuesta = trim($_POST['titulo'] ?? '');
        $solucion = $this->temaRepositorio->buscarTituloPorCodigoOposicionYOrden($codigoOposicion, $numeracion) ?? '';

        $evaluacion = $this->evaluacionServicio->evaluar($respuesta, $solucion);

        $sesion->setEvaluacionPaso(PasoEjercicio::TITULO, $evaluacion);
        $this->almacenSesionEjercicio->guardar($sesion);

        $this->redirector->redirect(RutasCuantoSabesTema::pasoTitulo($sesion->sesionId()));
    }

}
?>