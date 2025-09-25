<?php
namespace App\Controllers;

final class MenuController {
    public function mostrar () : void {
        // Datos vista
        $titulo = "Menu";

        // Renderizar vista
        ob_start();
        require __DIR__.'/../../views/menu/formulario.php';
        $contenido = ob_get_clean();

        // Cargar plantilla
        require __DIR__.'/../../views/plantilla.php';
    }
}
?>