<?php
namespace App\Controllers;

use App\Core\View;
use App\Helpers\Auth;

final class PanelControlEjerciciosController {
    public function mostrar () : void {
        Auth::requiereLogin();
        
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