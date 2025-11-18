<?php
namespace App\Controllers;

use App\Core\View;

final class PanelControlEjerciciosController {
    public function mostrar () : void {
        // Datos vista
        $titulo = "Panel control ejercicios";
        $usuario = $_SESSION['usuario'];
        $ejercicios = require __DIR__.'/../../config/Ejercicios.php';

        View::render('panel-control-ejercicios/index.php', [
            'usuario' => $usuario,
            'ejercicios' => $ejercicios
        ]);
    }
}
?>