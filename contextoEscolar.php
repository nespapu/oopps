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

            $sql = "SELECT * FROM contexto_escolar WHERE codigo_oposicion = '$c_oposicion' AND orden_tema = '$c_tema'";        
            $resultado = mysqli_query($conexion, $sql);
            mysqli_close($conexion);
        ?>
        <button onClick="irPantallaEjercicioCuantoSabesTema('herramientas.php', 'input');">Volver</button>
        <h1 class="titulo_ejercicio"><?php echo "$c_tema. $titulo"; ?></h1>
        <h2 class="titulo_ejercicio">Contexto escolar</h2>
        <p>Rellenar la enseñanza, ciclo, módulos, concepto, aplicación y método:</p>
        <div class="contenedor_ejercicio">
        <?php 
            //Mostrar el ejercicio según la configuración elegida
            if (mysqli_num_rows($resultado) > 0){
                while($fila = mysqli_fetch_assoc($resultado)){
                    print "<div class='contenedor_pregunta_solucion'>";
                    print "<div class='contenedor_pregunta'>";
                    if ($c_ensenyanza == "true"){
                        print "<input type='text' class='contextoEscolarEnsenyanza' value='".$fila['ensenyanza']."' readonly>";    
                    }else{
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['ensenyanza'], $c_dificultad, "letras") : "";
                        print "<input type='text' class='rellenar rellenarEnsenyanza contextoEscolarEnsenyanza' placeholder='$ayuda'>";    
                    }
                    print "<br>";
                    if ($fila['ciclo'] != "") { // El campo ciclo del contexto escolar puede ser nulo
                        if ($c_ciclosContexto == "true"){
                            print "<input type='text' class='contextoEscolarCiclos' value='".$fila['ciclo']."' readonly>";    
                        }else{
                            $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['ciclo'], $c_dificultad, "letras") : "";
                            print "<input type='text' class='rellenar rellenarCiclos contextoEscolarCiclos' placeholder='$ayuda'>";    
                        }
                        print "<br>";
                    }
                    if ($fila['modulo'] != "") { // El campo modulos del contexto escolar puede ser nulo
                        if ($c_modulosContexto == "true"){
                            print "<input type='text' class='contextoEscolarModulos' value='".$fila['modulo']."' readonly>";    
                        }else{
                            $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['modulo'], $c_dificultad, "letras") : "";
                            print "<input type='text' class='rellenar rellenarModulos contextoEscolarModulos' placeholder='$ayuda'>";    
                        }
                        print "<br>";
                    }
                    if ($c_conceptoContextoEscolar == "true"){
                        print "<textarea class='contextoEscolarConcepto'>";  
                        print $fila['concepto'];
                        print "</textarea>";  
                    }else {
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['concepto'], $c_dificultad, "palabras") : "";
                        print "<textarea class='rellenar rellenarConcepto contextoEscolarConcepto' placeholder='$ayuda'></textarea>"; 
                    }
                    print "<br>";
                    if ($c_aplicacionContextoEscolar == "true"){
                        print "<textarea class='contextoEscolarAplicacion'>";  
                        print $fila['aplicacion'];
                        print "</textarea>";  
                    }else {
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['aplicacion'], $c_dificultad, "palabras") : "";
                        print "<textarea class='rellenar rellenarAplicacion contextoEscolarAplicacion' placeholder='$ayuda'></textarea>"; 
                    }
                    print "<br>";
                    if ($fila['metodo'] != "") { // El campo metodo del contexto escolar puede ser nulo
                        if ($c_metodo == "true"){
                            print "<textarea class='contextoEscolarMetodo'>";  
                            print $fila['metodo'];
                            print "</textarea>";  
                        }else {
                            $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['metodo'], $c_dificultad, "palabras") : "";
                            print "<textarea class='rellenar rellenarMetodo contextoEscolarMetodo' placeholder='$ayuda'></textarea>"; 
                        }
                    }
                    print "</div>";
                    print "<div class='contenedor_solucion'>";
                    print "<div class='solucion_visible'>";
                    print "</div>";
                    print "<div class='solucion_invisible'>";
                    print "<input type='hidden' class='solucionEnsenyanza' value='".$fila['ensenyanza']."'>";
                    print "<input type='hidden' class='solucionCiclos' value='".$fila['ciclo']."'>";
                    print "<input type='hidden' class='solucionModulos' value='".$fila['modulo']."'>";
                    print "<input type='hidden' class='solucionConcepto' value='".$fila['concepto']."'>";
                    print "<input type='hidden' class='solucionAplicacion' value='".$fila['aplicacion']."'>";
                    print "<input type='hidden' class='solucionMetodo' value='".$fila['metodo']."'>";
                    print "</div>";
                    print "</div>";
                    print "</div>";
                }
            }
        ?>
            <div class="contenedor_botones">
                <button onclick="corregirEjercicioContextoEscolar();  habilitarBotonContinuar();">Corregir</button>
                <button onClick="reiniciarEjercicioContextoEscolar();">Reiniciar</button>
                <button onClick="mostrarSolucionEjercicioContextoEscolar();">Solución</button>
                <button id="boton_continuar" onClick="irPantallaEjercicioCuantoSabesTema('contextoLaboral.php', 'input');" disabled>Continuar</button>
            </div>
        </div>
    </body>
</html>