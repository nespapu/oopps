<?php

/**
 * @var \App\Application\Routing\UrlGenerator $url
 * @var \App\App\Routing\HowMuchDoYouKnow\Paths $howMuchDoYouKnowPaths
 * @var \App\App\Routing\ExercisesDashboardPaths $exercisesDashboardPaths
 */

?>

<div class="container py-5">

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-6">

            <div class="card shadow-sm">

                <div class="card-body text-center p-5">

                    <h1 class="h2 mb-3">
                        Ejercicio finalizado
                    </h1>

                    <p class="text-muted mb-5">
                        Has completado el ejercicio. ¿Qué deseas hacer ahora?
                    </p>

                    <div class="d-grid gap-3">

                        <a
                            class="btn btn-primary btn-lg"
                            href="<?= $url->to($howMuchDoYouKnowPaths->config()) ?>"
                        >
                            Reiniciar ejercicio
                        </a>

                        <a
                            class="btn btn-outline-secondary"
                            href="<?= $url->to($exercisesDashboardPaths->dashboard()) ?>"
                        >
                            Volver al panel de ejercicios
                        </a>

                    </div>

                </div>

            </div>

        </div>
    </div>

</div>