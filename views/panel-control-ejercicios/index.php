<h1>Bienvenido, <?= htmlspecialchars($usuario) ?></h1>

<h2>Ejercicios disponibles</h2>

<ul>
    <?php foreach ($ejercicios as $e): ?>
        <li>
            <a href="<?= $e['ruta'] ?>">
                <?= htmlspecialchars($e['nombre']) ?>
            </a>
        </li>
    <?php endforeach; ?>
</ul>