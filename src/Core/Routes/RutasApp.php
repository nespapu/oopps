<?php

namespace App\Core\Routes;

use App\Core\Routes\Dev\RutasDevSesionEjercicio;

final class RutasApp {
    /**
     * @return array<int, string>
     */
    public static function patrones(): array
    {
        return array_merge(
            RutasCuantoSabesTema::patrones(),
            RutasDevSesionEjercicio::patrones(),
            // Later:
            // OtherExerciseRoutes::patterns(),
            // AuthRoutes::patterns(),
            // ...
        );
    }
}
?>