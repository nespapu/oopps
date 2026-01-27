<?php
namespace App\Controllers;

use App\Core\View;
use App\Helpers\Auth;
use App\Helpers\Http;
use App\Application\Exercises\CuantoSabesTemaConfigPayloadBuilder;
use App\Application\Flash\FlashMessenger;
use App\Core\Routes\RutasCuantoSabesTema;
use App\Domain\Exercise\ConfigEjercicio;
use App\Domain\Exercise\Dificultad;
use App\Domain\Exercise\PasoEjercicio;
use App\Domain\Exercise\TipoEjercicio;
use App\Domain\Temas\TemaRepository;
use App\Infrastructure\Session\AlmacenSesionEjercicio;

final class CuantoSabesTemaConfigController
{
    public function __construct(
        private readonly AlmacenSesionEjercicio $almacenSesionEjercicio,
        private readonly CuantoSabesTemaConfigPayloadBuilder $payloadBuilder,
        private readonly FlashMessenger $flash,
        private readonly TemaRepository $temaRepositorio
    ) {}

    public function mostrar(): void
    {
        Auth::requiereContextoOposicion();

        $error = $this->flash->get('error');
        $contextoUsuario = Auth::contextoUsuario();

        $payload = $this->payloadBuilder->construir($contextoUsuario);
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
            $this->flash->set('error', 'Tema inválido.');
            Http::redirigir(RutasCuantoSabesTema::CONFIG);
        }

        $dificultadEnum = Dificultad::tryFrom($dificultad);
        if ($dificultadEnum === null) {
            $this->flash->set('error', 'Dificultad inválida.');
            Http::redirigir(RutasCuantoSabesTema::CONFIG);
        }

        if ($numeracion === 0) {
            $numeracionAleatoria = $this->temaRepositorio->buscarOrdenAleatorioPorCodigoOposicion($codigoOposicion);

            if ($numeracionAleatoria === null) {
                $this->flash->set('error', 'No hay temas disponibles para esta oposición.');
                Http::redirigir(RutasCuantoSabesTema::CONFIG);
            }

            $numeracion = $numeracionAleatoria;
        }

        $tituloTema = $this->temaRepositorio->buscarTituloPorCodigoOposicionYOrden($codigoOposicion, $numeracion);
        if ($tituloTema === null) {
            $this->flash->set('error', 'El tema seleccionado no existe.');
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

        $sesion = $this->almacenSesionEjercicio->crear(
            $tipoEjercicio,
            $contextoUsuarioArray,
            $configEjercicio,
            $primerPasoEjercicio
        );

        Http::redirigir(RutasCuantoSabesTema::pasoTitulo($sesion->sesionId()));
    }
}
?>