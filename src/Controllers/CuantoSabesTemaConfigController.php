<?php
namespace App\Controllers;

use App\Core\View;
use App\Helpers\Auth;
use App\Helpers\Flash;
use App\Helpers\Http;
use App\Infrastructure\Persistence\Repositories\TemaRepositorySQL;
use App\Application\Exercises\CuantoSabesTemaConfigPayloadBuilder;
use App\Core\Routes\RutasCuantoSabesTema;
use App\Domain\Exercise\ConfigEjercicio;
use App\Domain\Exercise\Dificultad;
use App\Domain\Exercise\PasoEjercicio;
use App\Domain\Exercise\TipoEjercicio;
use App\Infrastructure\Session\AlmacenSesionEjercicio;
use App\Infrastructure\Wiring\CuantoSabesTemaConfigFactory;

final class CuantoSabesTemaConfigController
{
    public function mostrar(): void
    {
        Auth::requiereContextoOposicion();

        $error = Flash::get('error');
        $contextoUsuario = Auth::contextoUsuario();

        $factoria = new CuantoSabesTemaConfigFactory();
        $payloadBuilder = $factoria->createPayloadBuilder();

        $payload = $payloadBuilder->construir($contextoUsuario);
        $payload['error'] = $error;
        $payload['titulo'] = "Configuración";

        View::render('exercises/CuantoSabesTemaConfiguracion.php', ['payload' => $payload]);
    }

    public function comprobar(): void
    {
        Auth::requiereContextoOposicion();

        $contextoUsuario = Auth::contextoUsuario();
        $codigoOposicion = $contextoUsuario->codigoOposicion();

        $numeracionTemaBruto = $_POST['numeracionTema'] ?? null;
        $dificultadBruto = $_POST['dificultad'] ?? null;

        $numeracion = is_numeric($numeracionTemaBruto) ? (int)$numeracionTemaBruto : -1;
        $dificultad = is_numeric($dificultadBruto) ? (int)$dificultadBruto : -1;

        if ($numeracion < 0) {
            Flash::set('error', 'Tema inválido.');
            Http::redirigir(RutasCuantoSabesTema::CONFIG);
        }

        $dificultadEnum = Dificultad::tryFrom($dificultad);
        if ($dificultadEnum === null) {
            Flash::set('error', 'Dificultad inválida.');
            Http::redirigir(RutasCuantoSabesTema::CONFIG);
        }

         $temaRepositorio = new TemaRepositorySQL();

        if ($numeracion === 0) {
            $numeracionAleatoria = $temaRepositorio->buscarOrdenAleatorioPorCodigoOposicion($codigoOposicion);

            if ($numeracionAleatoria === null) {
                Flash::set('error', 'No hay temas disponibles para esta oposición.');
                Http::redirigir(RutasCuantoSabesTema::CONFIG);
            }

            $numeracion = $numeracionAleatoria;
        }

        $tituloTema = $temaRepositorio->buscarTituloPorCodigoOposicionYOrden($codigoOposicion, $numeracion);
        if ($tituloTema === null) {
            Flash::set('error', 'El tema seleccionado no existe.');
            Http::redirigir(RutasCuantoSabesTema::CONFIG);
        }

        $configEjercicio = new ConfigEjercicio(
            $numeracion,
            $dificultad,
            []
        );
        $tipoEjercicio = TipoEjercicio::cuantoSabesTema();
        $primerPasoEjercicio = PasoEjercicio::primero();
        $contextoUsuarioArray = [
            'usuario' => $contextoUsuario->usuario(),
            'oposicionId' => $contextoUsuario->codigoOposicion()
        ];

        $almacenSesionEjercicio = new AlmacenSesionEjercicio();
        $sesion = $almacenSesionEjercicio->crear(
            $tipoEjercicio,
            $contextoUsuarioArray,
            $configEjercicio,
            $primerPasoEjercicio
        );

        Http::redirigir(RutasCuantoSabesTema::pasoTitulo($sesion->sesionId()));
    }
}
?>