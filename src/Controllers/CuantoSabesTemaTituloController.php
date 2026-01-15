<?php
namespace App\Controllers;

use App\Application\Exercises\Evaluation\CuantoSabesTemaTituloEvaluationService;
use App\Application\Exercises\StepBuilder\CuantoSabesTemaTituloPayloadBuilder;
use App\Core\Routes\RutasCuantoSabesTema;
use App\Core\View;
use App\Domain\Exercise\PasoEjercicio;
use App\Helpers\Auth;
use App\Helpers\Http;
use App\Infrastructure\Persistence\Repositories\TemaRepositorio;
use App\Infrastructure\Session\AlmacenSesionEjercicio;

final class CuantoSabesTemaTituloController
{
    public function mostrar(): void
    {
        Auth::requiereLogin();

        $almacenSesionEjercicio = new AlmacenSesionEjercicio();
        $sesion = $almacenSesionEjercicio->getSesionActual();

        $temaRepositorio = new TemaRepositorio();
        $payloadBuilder = new CuantoSabesTemaTituloPayloadBuilder($temaRepositorio);

        $payload = $payloadBuilder->construir($sesion);

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

        $almacenSesionEjercicio = new AlmacenSesionEjercicio();
        $sesion = $almacenSesionEjercicio->getSesionActual();

        $codigoOposicion = $sesion->contextoUsuario()['oposicionId'];
        $numeracion = $sesion->config()->tema();

        $temaRepositorio = new TemaRepositorio();
             
        $respuesta = trim($_POST['titulo'] ?? '');
        $solucion = $temaRepositorio->buscarTituloPorCodigoOposicionYOrden($codigoOposicion, $numeracion);

        $evaluador = new CuantoSabesTemaTituloEvaluationService();
        $evaluacion = $evaluador->evaluar($respuesta, $solucion);

        $sesion->setEvaluacionPaso(PasoEjercicio::TITULO, $evaluacion);
        $almacenSesionEjercicio->guardar($sesion);

        Http::redirigir(RutasCuantoSabesTema::pasoTitulo($sesion->sesionId()));
    }

}
?>