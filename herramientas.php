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

            $sql = "SELECT h.nombre, h.descripcion FROM tema_usar_herramienta tuh
                    JOIN herramienta h ON h.nombre = tuh.nombre_herramienta
                    WHERE tuh.codigo_oposicion = '$c_oposicion' AND tuh.orden_tema = '$c_tema'";        
            $resultado = mysqli_query($conexion, $sql);
            mysqli_close($conexion);
        ?>
        <button onClick="irPantallaEjercicioCuantoSabesTema('citas.php', 'input');">Volver</button>
        <h1 class="titulo_ejercicio"><?php echo "$c_tema. $titulo"; ?></h1>
        <h2 class="titulo_ejercicio">Herramientas</h2>
        <p>Rellenar el nombre de las herramientas y su descripción (si tiene) del tema:</p>
        <div class="contenedor_ejercicio">
        <?php 
            //Mostrar el ejercicio según la configuración elegida
            if (mysqli_num_rows($resultado) > 0){
                while($fila = mysqli_fetch_assoc($resultado)){
                    print "<div class='contenedor_pregunta_solucion'>";
                    print "<div class='contenedor_pregunta'>";
                    if ($c_herramienta == "true"){
                        print "<input type='text' class='herramientasNombre' value='".$fila['nombre']."' readonly>";    
                    }else{
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['nombre'], $c_dificultad, "letras") : "";
                        print "<input type='text' class='rellenar rellenarNombre herramientasNombre' placeholder='$ayuda'>";    
                    }
                    print "<br>";
                    if ($fila['descripcion'] != "") { // El campo descripción de la herramienta puede ser nulo
                        if ($c_descripcionHerramienta == "true"){
                            print "<textarea class='herramientasDescripcion'>";  
                            print $fila['descripcion'];
                            print "</textarea>";  
                        }else {
                            $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['descripcion'], $c_dificultad, "palabras") : "";
                            print "<textarea class='rellenar rellenarDescripcion herramientasDescripcion' placeholder='$ayuda'></textarea>"; 
                        }
                    }
                    print "</div>";
                    print "<div class='contenedor_solucion'>";
                    print "<div class='solucion_visible'>";
                    print "</div>";
                    print "<div class='solucion_invisible'>";
                    print "<input type='hidden' class='solucionNombre' value='".$fila['nombre']."'>";
                    print "<input type='hidden' class='solucionDescripcion' value='".$fila['descripcion']."'>";
                    print "</div>";
                    print "</div>";
                    print "</div>";
                }
            }
        ?>
            <div class="contenedor_botones">
                <button onclick="corregirEjercicioHerramientas();  habilitarBotonContinuar();">Corregir</button>
                <button onClick="reiniciarEjercicioHerramientas();">Reiniciar</button>
                <button onClick="mostrarSolucionEjercicioHerramientas();">Solución</button>
                <button id="boton_continuar" onClick="irPantallaEjercicioCuantoSabesTema('contextoEscolar.php', 'input');" disabled>Continuar</button>
            </div>
        </div>
    </body>
</html>