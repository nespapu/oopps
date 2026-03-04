<?php
$title = 'Panel de ejercicios';

$username = $username ?? 'usuario';
$exercises = $exercises ?? [];
?>

<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-9">

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4 p-md-5">

                <h1 class="h3 mb-3">
                    OOPPS – Bienvenido, <?= htmlspecialchars((string) $username, ENT_QUOTES, 'UTF-8') ?>
                </h1>

                <p class="text-muted mb-4">
                    ¿Qué ejercicio quieres practicar hoy?
                </p>

                <?php if (empty($exercises)): ?>
                    <div class="alert alert-info">
                        De momento no hay ejercicios configurados en el panel de control.
                    </div>
                <?php else: ?>

                    <div class="row g-3 mb-4">

                        <?php foreach ($exercises as $exercise): ?>
                            <?php
                               $name = $exercise['name'] ?? 'Ejercicio sin nombre';
                                $path = $exercise['path'] ?? '#';
                                $isActive = (bool) ($exercise['is_active'] ?? false);
                            ?>

                            <div class="col-md-6 col-lg-4">
                                <?php if ($isActive): ?>
                                    <a
                                        href="<?= htmlspecialchars((string) $path, ENT_QUOTES, 'UTF-8') ?>"
                                        class="text-decoration-none"
                                    >
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body">
                                                <h2 class="h5 mb-2">
                                                    <?= htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8') ?>
                                                </h2>
                                                <p class="small text-muted mb-0">
                                                    Haz clic para practicar este ejercicio.
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                <?php else: ?>
                                    <div class="card h-100 border-0 shadow-sm bg-light-subtle">
                                        <div class="card-body">
                                            <h2 class="h5 mb-2 text-muted">
                                                <?= htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8') ?>
                                            </h2>
                                            <p class="small text-muted mb-1">
                                                Este ejercicio todavía no está disponible.
                                            </p>
                                            <span class="badge text-bg-secondary">Próximamente</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                    <form action="login/salir" method="post" style="display:inline;">
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            Cerrar sesión
                        </button>
                    </form>

                    <small class="text-muted">
                        Consejo: empieza por “Cuánto sabes del tema” si llevas tiempo sin repasar 😉
                    </small>
                </div>

            </div>
        </div>

    </div>
</div>