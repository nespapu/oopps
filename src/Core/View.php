<?php

namespace App\Core;

class View
{
    public static function render(string $rutaVista, array $datos = []): void
    {
        // Convierte cada clave del array en una variable
        extract($datos);

        // Ruta absoluta al archivo de la vista
        $archivo = __DIR__ . '/../../views/' . $rutaVista;

        if (!file_exists($archivo)) {
            throw new \Exception("Vista no encontrada: " . $archivo);
        }

        require $archivo;
    }
}
?>