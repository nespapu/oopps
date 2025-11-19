<?php

namespace App\Core;

class View
{
    public static function render(string $vista, array $datos = []): void
    {
        // Convierte cada clave del array en una variable
        extract($datos);

        // Capturamos el contenido de la vista
        ob_start();
        require __DIR__ . "/../../views/{$vista}";
        $contenido = ob_get_clean();

        // Cargamos el layout principal
        require __DIR__ . "/../../views/layouts/plantilla.php";
    }
}
?>