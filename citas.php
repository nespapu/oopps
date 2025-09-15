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

            $sql = "SELECT atc.concepto_cita, atc.autor_cita, atc.anyo_cita, a.orden, a.titulo, c.contenido FROM apartado_tener_cita atc
                    JOIN cita c ON atc.concepto_cita = c.concepto AND atc.autor_cita = c.autor AND atc.anyo_cita = c.anyo
                    JOIN apartado a ON atc.codigo_oposicion = a.codigo_oposicion AND atc.orden_tema = a.orden_tema AND atc.orden_apartado = a.orden 
                    WHERE atc.codigo_oposicion = '$c_oposicion' AND atc.orden_tema = '$c_tema'";        
            $resultado = mysqli_query($conexion, $sql);
            mysqli_close($conexion);
        ?>
        <button onClick="irPantallaEjercicioCuantoSabesTema('justificacion.php', 'input');">Volver</button>
        <h1 class="titulo_ejercicio"><?php echo "$c_tema. $titulo"; ?></h1>
        <h2 class="titulo_ejercicio">Citas</h2>
        <p>Rellenar los campos ocultos de las distintias citas que aparecen en el tema:</p>
        <div class="contenedor_ejercicio">
        <?php 
            //Mostrar el ejercicio según la configuración elegida
            foreach($resultado as $fila){
                print "<div class='contenedor_pregunta_solucion'>";
                print "<div class='contenedor_pregunta'>";
                if ($c_conceptoCita == "true"){
                    print "<input type='text' class='citasConcepto' value='".$fila['concepto_cita']."' readonly>";    
                }else {
                    $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['concepto_cita'], $c_dificultad, "letras") : "";
                    print "<input type='text' class=' rellenar rellenarConcepto citasConcepto' placeholder='$ayuda'>";
                }
                if ($c_autorCita == "true"){
                    print "<input type='text' class='citasAutor' value='".$fila['autor_cita']."' readonly>";    
                }else {
                    $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['autor_cita'], $c_dificultad, "letras") : "";
                    print "<input type='text' class=' rellenar rellenarAutor citasAutor' placeholder='$ayuda'>";
                }
                if ($c_anyoCita == "true"){
                    print "<input type='text' class='citasAnyo' value='".$fila['anyo_cita']."' readonly>";    
                }else {
                    $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['anyo_cita'], $c_dificultad, "letras") : "";
                    print "<input type='text' class=' rellenar rellenarAnyo citasAnyo' placeholder='$ayuda'>";
                }
                print "<br>";
                if ($c_numeracionCita == "true"){
                    print "<input type='text' class='citasNumeracion' value='".$fila['orden']."' readonly>";    
                }else {
                    $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['orden'], $c_dificultad, "letras") : "";
                    print "<input type='text' class=' rellenar rellenarNumeracion citasNumeracion' placeholder='$ayuda'>";
                }
                if ($c_apartadoCita == "true"){
                    print "<input type='text' class='citasApartado' value='".$fila['titulo']."' readonly>";    
                }else {
                    $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['titulo'], $c_dificultad, "letras") : "";
                    print "<input type='text' class=' rellenar rellenarApartado citasApartado' placeholder='$ayuda'>";
                }
                print "<br>";
                if ($c_cita == "true"){
                    print "<textarea class='citasCita'>";  
                    print $fila['contenido'];
                    print "</textarea>";  
                }else {
                    $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['contenido'], $c_dificultad, "palabras") : "";
                    print "<textarea class='rellenar rellenarCita citasCita' placeholder='$ayuda'></textarea>"; 
                }
                print "</div>";
                print "<div class='contenedor_solucion'>";
                print "<div class='solucion_visible'>";
                print "</div>";
                print "<div class='solucion_invisible'>";
                print "<input type='hidden' class='solucion solucionConcepto' value='".$fila['concepto_cita']."'>";
                print "<input type='hidden' class='solucion solucionAutor' value='".$fila['autor_cita']."'>";
                print "<input type='hidden' class='solucion solucionAnyo' value='".$fila['anyo_cita']."'>";
                print "<input type='hidden' class='solucion solucionNumeracion' value='".$fila['orden']."'>";
                print "<input type='hidden' class='solucion solucionApartado' value='".$fila['titulo']."'>";
                print "<input type='hidden' class='solucion solucionCita' value='".$fila['contenido']."'>";
                print "</div>";
                print "</div>";
                print "</div>";
            }
        ?>
            <div class="contenedor_botones">
                <button onclick="corregirEjercicioCitas();  habilitarBotonContinuar();">Corregir</button>
                <button onClick="reiniciarEjercicioCitas();">Reiniciar</button>
                <button onClick="mostrarSolucionEjercicioCitas();">Solución</button>
                <button id="boton_continuar" onClick="irPantallaEjercicioCuantoSabesTema('herramientas.php', 'input');" disabled>Continuar</button>
            </div>
        </div>
    </body>
</html>