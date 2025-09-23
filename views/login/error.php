<?php /** @var string $error */ ?>
<form action="/oopps/public/login/comprobar" method="post">
    <input type="text" name="nombre" id="nombre" placeholder="Introducir nombre" <?php if ($error === 'nombre') echo "class='incorrecto'" ?>>
    <input type="password" name="clave" id="clave" placeholder="Introducir clave" <?php if ($error === 'clave') echo "class='incorrecto'" ?>>
    <input type="submit" value="Entrar">
</form>