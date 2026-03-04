<?php
// Browser tab title
$title = 'Login';
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
                            <?= htmlspecialchars((string) $error, ENT_QUOTES, 'UTF-8') ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" action="login">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nombre de usuario</label>
                            <input
                                type="text"
                                name="username"
                                id="username"
                                class="form-control"
                                placeholder="Introduce tu usuario"
                                required
                                autofocus
                                autocomplete="username"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control"
                                placeholder="Introduce tu contraseña"
                                required
                                autocomplete="current-password"
                            >
                        </div>

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