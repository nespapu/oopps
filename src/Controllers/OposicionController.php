<?php
namespace App\Controllers;

use App\Models\Oposicion;
use App\Helpers\Auth;
use App\Helpers\Http;

final class OposicionController {
    public function mostrar () : void {
        // Datos vista
        $titulo = "Oposición";
        $oposiciones = Oposicion::buscarPorUsuario(Auth::usuario());

        // Renderizar vista
        ob_start();
        require __DIR__.'/../../views/oposicion/formulario.php';
        $contenido = ob_get_clean();

        // Cargar plantilla
        require __DIR__.'/../../views/plantilla.php';
    }

    public function comprobar () : void {
        $_SESSION['oposicion'] = $_POST['oposicion'];
        Http::redirigir("panel-control-ejercicios");
    }
}
?>