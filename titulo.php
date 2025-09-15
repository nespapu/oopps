<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <link rel="stylesheet" href="css/miEstilo.css">
        <script src="js/misScripts.js"></script>
    </head>
    <body>
        <?php
            //Procesar parametros de entrada URL y crear campos ocultos 
            require 'php/definirCamposOcultos.php';
            //Realizar consulta a BD para obtener datos
            require 'php/db.php';
            require 'php/utils.php';
            require 'php/obtenerNombreTema.php';  
            $resultado = mysqli_query($conexion, $sql);
            mysqli_close($conexion);
        ?>
        <button onClick="irPantallaEjercicioCuantoSabesTema('menu.php', 'input');">Volver</button>
        <h1 class="titulo_ejercicio"><?php echo "Tema $c_tema"; ?></h1>
        <h2 class="titulo_ejercicio">Título</h2>
        <p>Rellenar el título del tema:</p>
        <div class="contenedor_ejercicio">
        <?php 
            print "<div class='contenedor_pregunta_solucion'>";
            print "<div class='contenedor_pregunta'>";
            $ayuda = ($c_dificultad != 4) ? obtenerAyuda($titulo, $c_dificultad, "letras") : "";
            print "<input type='text' class='rellenar rellenarTitulo tituloTitulo' placeholder='$ayuda'>";
            print "</div>";
            print "<div class='contenedor_solucion'>";
            print "<div class='solucion_visible'>";
            print "</div>";
            print "<div class='solucion_invisible'>";
            print "<input type='hidden' class='solucionTitulo' value='".$titulo."'>";
            print "</div>";
            print "</div>";
        ?>
            <div class="contenedor_botones">
                <button onclick="corregirEjercicioTitulo();  habilitarBotonContinuar();">Corregir</button>
                <button onClick="reiniciarEjercicioTitulo();">Reiniciar</button>
                <button onClick="mostrarSolucionEjercicioTitulo();">Solución</button>
                <button id="boton_continuar" onClick="irPantallaEjercicioCuantoSabesTema('indice.php', 'input');" disabled>Continuar</button>
            </div>
        </div>
    </body>
</html>