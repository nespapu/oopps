<?php
// Título de la pestaña del navegador
$titulo = 'Login';
?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">

                    <h1 class="h4 text-center mb-3">Iniciar sesión</h1>
                    <p class="text-muted text-center mb-4">
                        Accede a tu panel de estudio de OOPPS
                    </p>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger">
                            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="/oopps/public/login/comprobar">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de usuario</label>
                            <input
                                type="text"
                                name="nombre"
                                id="nombre"
                                class="form-control"
                                placeholder="Introduce tu usuario"
                                required
                                autofocus
                                autocomplete="username"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="clave" class="form-label">Contraseña</label>
                            <input
                                type="password"
                                name="clave"
                                id="clave"
                                class="form-control"
                                placeholder="Introduce tu contraseña"
                                required
                                autocomplete="current-password"
                            >
                        </div>

                        <!-- Si tienes token CSRF, iría aquí -->
                        <!-- <input type="hidden" name="csrf_token" value="<?= $csrfToken ?? '' ?>"> -->

                        <button type="submit" class="btn btn-primary w-100">
                            Entrar
                        </button>
                    </form>

                    <p class="small text-muted text-center mt-3 mb-0">
                        ¿Has olvidado tu contraseña? (pendiente de implementar 😅)
                    </p>

                </div>
            </div>
        </div>
    </div>
</div>