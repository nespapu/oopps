<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $titulo ?? 'OOPPS' ?></title>

    <!-- Bootstrap CSS -->
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet">

    <!-- CSS propio opcional -->
    <link rel="stylesheet" href="/public/css/style.css">
</head>

<body class="bg-light">

    <!-- HEADER -->
    <?php require __DIR__ . '/../partials/cabecera.php'; ?>

    <main class="container mt-4">
        <?= $contenido ?>
    </main>

    <!-- FOOTER -->
    <?php require __DIR__ . '/../partials/pie.php'; ?>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>