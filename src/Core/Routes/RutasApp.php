<?php

namespace App\Core\Routes;

final class RutasApp {
    /**
     * @return array<int, string>
     */
    public static function patrones(): array
    {
        return array_merge(
            RutasCuantoSabesTema::patrones()
            // Later:
            // OtherExerciseRoutes::patterns(),
            // AuthRoutes::patterns(),
            // ...
        );
    }
}
?>