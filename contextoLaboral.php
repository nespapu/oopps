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

            $sql = "SELECT * FROM contexto_laboral WHERE codigo_oposicion = '$c_oposicion' AND orden_tema = '$c_tema'";        
            $resultado = mysqli_query($conexion, $sql);
            mysqli_close($conexion);
        ?>
        <button onClick="irPantallaEjercicioCuantoSabesTema('contextoEscolar.php', 'input');">Volver</button>
        <h1 class="titulo_ejercicio"><?php echo "$c_tema. $titulo"; ?></h1>
        <h2 class="titulo_ejercicio">Contexto laboral</h2>
        <p>Rellenar el campo, el profesional, el concepto, la tarea y el beneficio de aplicar los contenidos al contexto laboral:</p>
        <div class="contenedor_ejercicio">
        <?php 
            //Mostrar el ejercicio según la configuración elegida
            if (mysqli_num_rows($resultado) > 0){
                while($fila = mysqli_fetch_assoc($resultado)){
                    print "<div class='contenedor_pregunta_solucion'>";
                    print "<div class='contenedor_pregunta'>";
                    if ($c_campo == "true"){
                        print "<input type='text' class='contextoLaboralCampo' value='".$fila['campo']."' readonly>";    
                    }else{
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['campo'], $c_dificultad, "letras") : "";
                        print "<input type='text' class='rellenar rellenarCampo contextoLaboralCampo' placeholder='$ayuda'>";    
                    }
                    print "<br>";
                    if ($fila['profesional'] != "") { // El campo profesional del contexto laboral puede ser nulo
                        if ($c_profesional == "true"){
                            print "<input type='text' class='contextoLaboralProfesional' value='".$fila['profesional']."' readonly>";    
                        }else{
                            $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['profesional'], $c_dificultad, "letras") : "";
                            print "<input type='text' class='rellenar rellenarProfesional contextoLaboralProfesional' placeholder='$ayuda'>";    
                        }
                        print "<br>";
                    }
                    if ($c_conceptoContextoLaboral == "true"){
                        print "<textarea class='contextoLaboralConcepto' readonly>";  
                        print $fila['concepto'];
                        print "</textarea>";  
                    }else {
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['concepto'], $c_dificultad, "palabras") : "";
                        print "<textarea class='rellenar rellenarConcepto contextoLaboralConcepto' placeholder='$ayuda'></textarea>"; 
                    }
                    print "<br>";
                    if ($c_aplicacionContextoLaboral == "true"){
                        print "<textarea class='contextoLaboralAplicacion' readyonly>";  
                        print $fila['tarea'];
                        print "</textarea>";  
                    }else {
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['tarea'], $c_dificultad, "palabras") : "";
                        print "<textarea class='rellenar rellenarAplicacion contextoLaboralAplicacion' placeholder='$ayuda'></textarea>"; 
                    }
                    print "<br>";
                    if ($c_beneficio == "true"){
                        print "<textarea class='contextoLaboralBeneficio' readonly>";  
                        print $fila['beneficio'];
                        print "</textarea>";  
                    }else {
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($fila['beneficio'], $c_dificultad, "palabras") : "";
                        print "<textarea class='rellenar rellenarBeneficio contextoLaboralBeneficio' placeholder='$ayuda'></textarea>"; 
                    }
                    print "</div>";
                    print "<div class='contenedor_solucion'>";
                    print "<div class='solucion_visible'>";
                    print "</div>";
                    print "<div class='solucion_invisible'>";
                    print "<input type='hidden' class='solucionCampo' value='".$fila['campo']."'>";
                    print "<input type='hidden' class='solucionProfesional' value='".$fila['profesional']."'>";
                    print "<input type='hidden' class='solucionConcepto' value='".$fila['concepto']."'>";
                    print "<input type='hidden' class='solucionAplicacion' value='".$fila['tarea']."'>";
                    print "<input type='hidden' class='solucionBeneficio' value='".$fila['beneficio']."'>";
                    print "</div>";
                    print "</div>";
                    print "</div>";
                }
            }
        ?>
            <div class="contenedor_botones">
                <button onclick="corregirEjercicioContextoLaboral(); habilitarBotonContinuar();">Corregir</button>
                <button onClick="reiniciarEjercicioContextoLaboral();">Reiniciar</button>
                <button onClick="mostrarSolucionEjercicioContextoLaboral();">Solución</button>
                <button id="boton_continuar" onClick="irPantallaEjercicioCuantoSabesTema('bibliografia.php', 'input');" disabled>Continuar</button>
            </div>
        </div>
    </body>
</html>