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
            $sql = "SELECT l.autor, l.anyo, l.titulo, l.editorial FROM tema_referenciar_libro trl
                    JOIN libro l ON l.autor = trl.autor_libro AND l.titulo = trl.titulo_libro
                    WHERE codigo_oposicion = '$c_oposicion' AND orden_tema = '$c_tema'";        
            $resultado = mysqli_query($conexion, $sql);
            mysqli_close($conexion);
        ?>
        <button onClick="irPantallaEjercicioCuantoSabesTema('contextoLaboral.php', 'input');">Volver</button>
        <h1 class="titulo_ejercicio"><?php echo "$c_tema. $titulo"; ?></h1>
        <h2 class="titulo_ejercicio">Bibliografía</h2>
        <p>Rellenar el autor, año de publicación, título y editorial de los libros que forman la bibliografía del tema:</p>
        <div class="contenedor_ejercicio">
        <?php 
            //Mostrar el ejercicio según la configuración elegida
            if (mysqli_num_rows($resultado) > 0){
                while($fila = mysqli_fetch_assoc($resultado)){
                    print "<div class='contenedor_pregunta_solucion'>";
                    print "<div class='contenedor_pregunta'>";
                    if ($c_autorLibro == "true"){
                        print "<input type='text' class='bibliografiaAutor' value='".$fila['autor']."' readonly>";    
                    }else{
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['autor'], $c_dificultad, "letras") : "";
                        print "<input type='text' class='rellenar rellenarAutor bibliografiaAutor' placeholder='$ayuda'>";    
                    }
                    if ($c_anyoLibro == "true"){
                        print "<input type='text' class='bibliografiaAnyo' value='".$fila['anyo']."' readonly>";    
                    }else{
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['anyo'], $c_dificultad, "letras") : "";
                        print "<input type='text' class='rellenar rellenarAnyo bibliografiaAnyo' placeholder='$ayuda'>";    
                    }
                    if ($c_tituloLibro == "true"){
                        print "<input type='text' class='bibliografiaTitulo' value='".$fila['titulo']."' readonly>";    
                    }else{
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['titulo'], $c_dificultad, "letras") : "";
                        print "<input type='text' class='rellenar rellenarTitulo bibliografiaTitulo' placeholder='$ayuda'>";    
                    }
                    if ($c_editorial == "true"){
                        print "<input type='text' class='bibliografiaEditorial' value='".$fila['editorial']."' readonly>";    
                    }else{
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['editorial'], $c_dificultad, "letras") : "";
                        print "<input type='text' class='rellenar rellenarEditorial bibliografiaEditorial' placeholder='$ayuda'>";    
                    }                    
                    print "</div>";
                    print "<div class='contenedor_solucion'>";
                    print "<div class='solucion_visible'>";
                    print "</div>";
                    print "<div class='solucion_invisible'>";
                    print "<input type='hidden' class='solucionAutor' value='".$fila['autor']."'>";
                    print "<input type='hidden' class='solucionAnyo' value='".$fila['anyo']."'>";
                    print "<input type='hidden' class='solucionTitulo' value='".$fila['titulo']."'>";
                    print "<input type='hidden' class='solucionEditorial' value='".$fila['editorial']."'>";
                    print "</div>";
                    print "</div>";
                    print "</div>";
                }
            }
        ?>
            <div class="contenedor_botones">
                <button onclick="corregirEjercicioBibliografia(); habilitarBotonContinuar();">Corregir</button>
                <button onClick="reiniciarEjercicioBibliografia();">Reiniciar</button>
                <button onClick="mostrarSolucionEjercicioBibliografia();">Solución</button>
                <button id="boton_continuar" onClick="irPantallaEjercicioCuantoSabesTema('webgrafia.php', 'input');" disabled>Continuar</button>
            </div>
        </div>
    </body>
</html>