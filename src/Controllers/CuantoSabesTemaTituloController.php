<?php
namespace App\Controllers;

use App\Application\Exercises\StepBuilder\CuantoSabesTemaTituloPayloadBuilder;
use App\Application\Exercises\Evaluation\CuantoSabesTemaTituloEvaluationService;
use App\Core\Routes\RutasCuantoSabesTema;
use App\Core\View;
use App\Domain\Exercise\PasoEjercicio;
use App\Domain\Temas\TemaRepository;
use App\Helpers\Auth;
use App\Helpers\Http;
use App\Infrastructure\Session\AlmacenSesionEjercicio;

final class CuantoSabesTemaTituloController
{
    public function __construct(
        private readonly AlmacenSesionEjercicio $almacenSesionEjercicio,
        private readonly CuantoSabesTemaTituloPayloadBuilder $payloadBuilder,
        private readonly TemaRepository $temaRepositorio,
        private readonly CuantoSabesTemaTituloEvaluationService $evaluacionServicio
    ) {}

    public function mostrar(): void
    {
        Auth::requiereLogin();

        $sesion = $this->almacenSesionEjercicio->getSesionActual();

        $payload = $this->payloadBuilder->construir($sesion);

        $evaluacion = $sesion->getEvaluacionPaso(PasoEjercicio::TITULO);

        View::render('exercises/CuantoSabesTemaTitulo.php', [
            'payload' => $payload,
            'sesionId' => $sesion->sesionId(),
            'evaluacion' => $evaluacion
        ]);
    }

    public function evaluar(): void
    {
        Auth::requiereLogin();

        $sesion = $this->almacenSesionEjercicio->getSesionActual();

        $codigoOposicion = $sesion->contextoUsuario()['oposicionId'];
        $numeracion = $sesion->config()->tema();
             
        $respuesta = trim($_POST['titulo'] ?? '');
        $solucion = $this->temaRepositorio->buscarTituloPorCodigoOposicionYOrden($codigoOposicion, $numeracion) ?? '';

        $evaluacion = $this->evaluacionServicio->evaluar($respuesta, $solucion);

        $sesion->setEvaluacionPaso(PasoEjercicio::TITULO, $evaluacion);
        $this->almacenSesionEjercicio->guardar($sesion);

        Http::redirigir(RutasCuantoSabesTema::pasoTitulo($sesion->sesionId()));
    }

}
?>