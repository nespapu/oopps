<?php /** @var string $contenido */ ?>
<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <title><?= htmlspecialchars($titulo ?? 'oopps') ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="/oopps/css/miEstilo.css">
        <script src="/oopps/js/misScripts.js"></script>
    </head>
    <body>
        <?php require __DIR__ . '/partials/menu.php'; ?>
        <main>
            <?= $contenido /* aquí se incrusta la vista concreta */ ?>
        </main>

    </body>
</html>