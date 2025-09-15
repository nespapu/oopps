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
            $sql = "SELECT * FROM apartado WHERE codigo_oposicion='$c_oposicion' AND orden_tema='$c_tema' ORDER BY numeracion ASC";        
            $resultado = mysqli_query($conexion, $sql);
            mysqli_close($conexion);
        ?>
        <button onClick="irPantallaEjercicioCuantoSabesTema('titulo.php', 'input');">Volver</button>
        <h1 class="titulo_ejercicio"><?php echo "$c_tema. $titulo"; ?></h1>
        <h2 class="titulo_ejercicio">Índices</h2>
        <p>Rellenar lo numeración y el título de los apartados del tema:</p>
        <div class="contenedor_ejercicio">
        <?php 
            //Mostrar el ejercicio según la configuración elegida
            if (mysqli_num_rows($resultado) > 0){
                while($fila = mysqli_fetch_assoc($resultado)){
                    print "<div class='contenedor_pregunta_solucion'>";
                    print "<div class='contenedor_pregunta'>";
                    if ($c_numeracion == "true"){
                        print "<input type='text' class='indiceNumeracion' value='".$fila['orden']."' readonly>";    
                    }else{
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['orden'], $c_dificultad, "letras") : "";
                        print "<input type='text' class='rellenar rellenarNumeracion indiceNumeracion' placeholder='$ayuda'>";    
                    }
                    if ($c_apartado == "true"){
                        print "<input type='text' class='indiceApartado' value='".$fila['titulo']."' readonly>";    
                    }else {
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['titulo'], $c_dificultad, "letras") : "";
                        print "<input type='text' class='rellenar rellenarApartado indiceApartado' placeholder='$ayuda'>";
                    }
                    print "</div>";
                    print "<div class='contenedor_solucion'>";
                    print "<div class='solucion_visible'>";
                    print "</div>";
                    print "<div class='solucion_invisible'>";
                    print "<input type='hidden' class='solucionNumeracion' value='".$fila['orden']."'>";
                    print "<input type='hidden' class='solucionApartado' value='".$fila['titulo']."'>";
                    print "</div>";
                    print "</div>";
                    print "</div>";
                }
            }
        ?>
            <div class="contenedor_botones">
                <button onclick="corregirEjercicioIndice();  habilitarBotonContinuar();">Corregir</button>
                <button onClick="reiniciarEjercicioIndice();">Reiniciar</button>
                <button onClick="mostrarSolucionEjercicioIndice();">Solución</button>
                <button id="boton_continuar" onClick="irPantallaEjercicioCuantoSabesTema('justificacion.php', 'input');" disabled>Continuar</button>
            </div>
        </div>
    </body>
</html>