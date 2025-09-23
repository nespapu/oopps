<?php
namespace App\Controllers;

final class OposicionController {
    public function mostrar () : void {
        // Datos vista
        $titulo = "Oposición";

        // Renderizar vista
        ob_start();
        require __DIR__.'/../../views/oposicion/formulario.php';
        $contenido = ob_get_clean();

        // Cargar plantilla
        require __DIR__.'/../../views/plantilla.php';
    }
}
?>