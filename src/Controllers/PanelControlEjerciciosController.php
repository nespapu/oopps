<?php
namespace App\Controllers;

use App\Application\Auth\AuthService;
use App\Core\View;

final class PanelControlEjerciciosController {
    public function __construct(
        private readonly AuthService $authService
    ){}
    
    public function mostrar () : void {
        $this->authService->requireLogin();
        
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