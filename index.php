<html>
    <head>
        <link rel="stylesheet" href="css/miEstilo.css">
    </head>
    <body>
        <form action="php/validarUsuario.php" method="post">
            <input type="text" name="nombre" id="nombre" placeholder="Introducir nombre">
            <input type="password" name="clave" id="clave" placeholder="Introducir clave">
            <input type="submit" value="Entrar">
            <?php 
                if (isset($_GET['error'])) {
                    $error = $_GET['error'];
                    print "<script>";
                    print "document.getElementById('".$error."').classList.add('incorrecto')";
                    print "</script>";
                }
            ?>
        </form>
    </body>
</html>