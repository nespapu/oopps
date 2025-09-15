<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <link rel="stylesheet" href="css/miEstilo.css">
        <script src="js/misScripts.js"></script>
        <script src="js/string-similarity.min.js"></script>
    </head>
    <body>
        <?php
            //Procesar parametros de entrada URL y crear campos ocultos 
            require 'php/definirCamposOcultos.php';
            //Realizar consulta a BD para obtener datos
            require 'php/db.php';
            require 'php/utils.php';
            require 'php/obtenerNombreTema.php';
            $sql = "SELECT w.nombre, w.url FROM tema_referenciar_web trw
                    JOIN web w ON w.url = trw.url_web
                    WHERE codigo_oposicion = '$c_oposicion' AND orden_tema = '$c_tema'";        
            $resultado = mysqli_query($conexion, $sql);
            mysqli_close($conexion);
        ?>
        <button onClick="irPantallaEjercicioCuantoSabesTema('webgrafia.php', 'input');">Volver</button>
        <h1 class="titulo_ejercicio"><?php echo "$c_tema. $titulo"; ?></h1>
        <h2 class="titulo_ejercicio">Webgrafía</h2>
        <p>Rellenar el nombre y la url de los sitios web que componen la webgrafía del tema:</p>
        <div class="contenedor_ejercicio">
        <?php 
            //Mostrar el ejercicio según la configuración elegida
            if (mysqli_num_rows($resultado) > 0){
                while($fila = mysqli_fetch_assoc($resultado)){
                    print "<div class='contenedor_pregunta_solucion'>";
                    print "<div class='contenedor_pregunta'>";
                    if ($c_nombreWeb == "true"){
                        print "<input type='text' class='webgrafiaNombre' value='".$fila['nombre']."' readonly>";    
                    }else{
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['nombre'], $c_dificultad, "letras") : "";
                        print "<input type='text' class='rellenar rellenarNombre webgrafiaNombre' placeholder='$ayuda'>";    
                    }
                    if ($c_url == "true"){
                        print "<input type='text' class='webgrafiaUrl' value='".$fila['url']."' readonly>";    
                    }else{
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['url'], $c_dificultad, "letras") : "";
                        print "<input type='text' class='rellenar rellenarUrl webgrafiaUrl' placeholder='$ayuda'>";    
                    }                   
                    print "</div>";
                    print "<div class='contenedor_solucion'>";
                    print "<div class='solucion_visible'>";
                    print "</div>";
                    print "<div class='solucion_invisible'>";
                    print "<input type='hidden' class='solucionNombre' value='".$fila['nombre']."'>";
                    print "<input type='hidden' class='solucionUrl' value='".$fila['url']."'>";
                    print "</div>";
                    print "</div>";
                    print "</div>";
                }
            }
        ?>
            <div class="contenedor_botones">
                <button onclick="corregirEjercicioWebgrafia(); habilitarBotonContinuar();">Corregir</button>
                <button onClick="reiniciarEjercicioWebgrafia();">Reiniciar</button>
                <button onClick="mostrarSolucionEjercicioWebgrafia();">Solución</button>
                <button id="boton_continuar" onClick="irPantallaEjercicioCuantoSabesTema('menu.php', 'input');" disabled>Fin</button>
            </div>
        </div>
    </body>
</html>