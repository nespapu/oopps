<?php
namespace App\App;

use App\Controllers\LoginController;
use App\Controllers\PanelControlEjerciciosController;
use App\Controllers\CuantoSabesTemaConfigController;
use App\Controllers\CuantoSabesTemaTituloController;
use App\Controllers\Dev\DevSesionEjercicioController;
use App\Core\Routes\RutasCuantoSabesTema;
use App\Core\Routes\Dev\RutasDevSesionEjercicio;
use App\Helpers\Router;
use App\Infrastructure\Session\AlmacenSesionEjercicio;

final class AppWiring
{
    private ?AlmacenSesionEjercicio $almacenSesionEjercicio = null;

    public function rutas(): array
    {
        return [
            // DEV
            RutasDevSesionEjercicio::BASE => function (): void {
                if (!Router::esGet()) { http_response_code(405); return; }
                $this->devSesionEjercicioController()->mostrar();
            },

            RutasDevSesionEjercicio::SIGUIENTE => function (): void {
                if (!Router::esPost()) { http_response_code(405); return; }
                $this->devSesionEjercicioController()->siguiente();
            },

            RutasDevSesionEjercicio::RESET => function (): void {
                if (!Router::esPost()) { http_response_code(405); return; }
                $this->devSesionEjercicioController()->reset();
            },
        ];
    }

    // -----------------
    // Controllers
    // -----------------
    private function loginController(): LoginController
    {
        return new LoginController(/* deps cuando toque */);
    }

    private function panelControlEjerciciosController(): PanelControlEjerciciosController
    {
        return new PanelControlEjerciciosController(/* deps cuando toque */);
    }

    private function cuantoSabesTemaConfigController(): CuantoSabesTemaConfigController
    {
        return new CuantoSabesTemaConfigController(/* deps */);
    }

    private function cuantoSabesTemaTituloController(): CuantoSabesTemaTituloController
    {
        return new CuantoSabesTemaTituloController(/* deps */);
    }

    private function devSesionEjercicioController(): DevSesionEjercicioController
    {
        return new DevSesionEjercicioController($this->almacenSesionEjercicio());
    }

    // -----------------
    // Shared deps
    // -----------------
    private function almacenSesionEjercicio(): AlmacenSesionEjercicio
    {
        if ($this->almacenSesionEjercicio === null) {
            $this->almacenSesionEjercicio = new AlmacenSesionEjercicio();
        }
        return $this->almacenSesionEjercicio;
    }
}

?>