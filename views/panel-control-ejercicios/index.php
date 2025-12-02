<?php
// Título de la pestaña del navegador
$titulo = 'Panel control ejercicios';

// Intentamos obtener el nombre del usuario
$nombreUsuario = $usuario ?? 'usuario';

// Aseguramos que $ejercicios sea un array para evitar avisos
$ejercicios = $ejercicios ?? [];
?>

<div class="row justify-content-center">
    <div class="col-lg-8 col-xl-9">

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body p-4 p-md-5">

                <h1 class="h3 mb-3">
                    OOPPS – Bienvenido, <?= htmlspecialchars($nombreUsuario, ENT_QUOTES, 'UTF-8') ?>
                </h1>

                <p class="text-muted mb-4">
                    ¿Qué ejercicio quieres practicar hoy?
                </p>

                <?php if (empty($ejercicios)): ?>
                    <div class="alert alert-info">
                        De momento no hay ejercicios configurados en el panel de control.
                    </div>
                <?php else: ?>

                    <div class="row g-3 mb-4">

                        <?php foreach ($ejercicios as $ejercicio): ?>
                            <?php
                                $nombre = $ejercicio['nombre'] ?? 'Ejercicio sin nombre';
                                $ruta   = $ejercicio['ruta']   ?? '#';
                                $activo = $ejercicio['activo'] ?? false;
                            ?>

                            <div class="col-md-6 col-lg-4">
                                <?php if ($activo): ?>
                                    <!-- Tarjeta de ejercicio ACTIVO (clicable) -->
                                    <a 
                                        href="/oopps/menu.php?nombre=nestor&oposicion=590107" 
                                        class="text-decoration-none"
                                    >
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body">
                                                <h2 class="h5 mb-2">
                                                    <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>
                                                </h2>
                                                <p class="small text-muted mb-0">
                                                    Haz clic para practicar este ejercicio.
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                <?php else: ?>
                                    <!-- Tarjeta de ejercicio INACTIVO (no clicable) -->
                                    <div class="card h-100 border-0 shadow-sm bg-light-subtle">
                                        <div class="card-body">
                                            <h2 class="h5 mb-2 text-muted">
                                                <?= htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') ?>
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
                    <a 
                        href="login/salir" 
                        class="btn btn-outline-danger btn-sm"
                    >
                        Cerrar sesión
                    </a>

                    <small class="text-muted">
                        Consejo: empieza por “Cuánto sabes del tema” si llevas tiempo sin repasar 😉
                    </small>
                </div>

            </div>
        </div>

    </div>
</div>