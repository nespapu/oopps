<?php
declare(strict_types=1);

namespace App\App\Routing;


final class PanelControlEjerciciosPaths
{
    private const PANEL = 'panel-control-ejercicios';
    
    public function panel(): string
    {
        return self::PANEL;
    }
}