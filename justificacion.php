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

            $sql1 = "SELECT nombre_ciclo FROM tema_enmarcar_ciclo WHERE codigo_oposicion = '$c_oposicion' AND orden_tema = '$c_tema'"; 
            $resultado1 = mysqli_query($conexion, $sql1);
            if (mysqli_num_rows($resultado1) > 0){
                $ciclos = array();
                while($fila1 = mysqli_fetch_assoc($resultado1)){
                    $ciclo = $fila1['nombre_ciclo'];
                    $ciclos[$ciclo] = array("leyes"=>array(), "modulos"=>array());
                    $sql2 = "SELECT nombre_ley FROM ley_definir_ciclo WHERE nombre_ciclo = '$ciclo'";
                    $resultado2 = mysqli_query($conexion, $sql2);
                    if (mysqli_num_rows($resultado2) > 0){
                        while($fila2 = mysqli_fetch_assoc($resultado2)){  
                            $ciclos[$ciclo]["leyes"][] = $fila2['nombre_ley'];
                        }
                    }
                    $sql3 = "SELECT mdt.nombre_modulo as modulo FROM ciclo_impartir_modulo cim
                                JOIN modulo_desarrollar_tema mdt ON mdt.nombre_modulo = cim.nombre_modulo
                                WHERE cim.nombre_ciclo = '$ciclo' AND mdt.orden_tema = '$c_tema'";
                    $resultado3 = mysqli_query($conexion, $sql3);
                    if (mysqli_num_rows($resultado3) > 0){
                        while($fila3 = mysqli_fetch_assoc($resultado3)){  
                            $ciclos[$ciclo]["modulos"][] = $fila3['modulo'];
                        }
                    }
                }
            }
            mysqli_close($conexion);
        ?>
        <button onClick="irPantallaEjercicioCuantoSabesTema('indice.php', 'input');">Volver</button>
        <h1 class="titulo_ejercicio"><?php echo "$c_tema. $titulo"; ?></h1>
        <h2 class="titulo_ejercicio">Justificación</h2>
        <p>Rellenar los ciclos, leyes y módulos del tema:</p>
        <div class="contenedor_ejercicio">
        <?php 
            //Mostrar el ejercicio según la configuración elegida
            foreach($ciclos as $ciclo => $leyesmodulos){
                print "<div class='contenedor_pregunta_solucion'>";
                print "<div class='contenedor_pregunta'>";
                print "<h3>Ciclo</h3>";
                if ($c_ciclos == "true"){
                    print "<h3>$ciclo</h3>";
                }else {
                    $ayuda = ($c_dificultad != 4) ? obtenerAyuda($ciclo, $c_dificultad, "letras") : "";
                    print "<input type='text' class=' rellenar rellenarCiclo justificacionCiclo' placeholder='$ayuda'>";
                }
                print "<h4>Leyes</h4>";
                print "<ul>";
                foreach($leyesmodulos["leyes"] as $ley){
                    print "<li>";
                    if ($c_leyes == "true"){
                        print "$ley";
                    }else {
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($ley, $c_dificultad, "letras") : "";
                        print "<input type='text' class='rellenar rellenarLey justificacionLey' placeholder='$ayuda'>";
                    }
                    print "</li>";
                }
                print "</ul>";
                print "<h4>Módulos</h4>";
                print "<ul>";
                foreach($leyesmodulos["modulos"] as $modulo){
                    print "<li>";
                    if ($c_modulos == "true"){
                        print "$modulo";
                    }else {
                        $ayuda = ($c_dificultad != 4) ? obtenerAyuda($modulo, $c_dificultad, "letras") : "";
                        print "<input type='text' class='rellenar rellenarModulo justificacionModulo' placeholder='$ayuda'>";
                    }
                    print "</li>";
                }
                print "</ul>";
                print "</div>";
                print "<div class='contenedor_solucion'>";
                print "<div class='solucion_visible'>";
                print "</div>";
                print "<div class='solucion_invisible'>";
                print "<input type='hidden' class='solucion solucionCiclo' value='$ciclo'>";
                foreach($leyesmodulos["leyes"] as $ley){
                    print "<input type='hidden' class='solucion solucionLey' value='$ley'>";
                }
                foreach($leyesmodulos["modulos"] as $modulo){
                    print "<input type='hidden' class='solucion solucionModulo' value='$modulo'>";
                }
                print "</div>";
                print "</div>";
                print "</div>";
            }
        ?>
            <div class="contenedor_botones">
                <button onclick="corregirEjercicioJustificacion();  habilitarBotonContinuar();">Corregir</button>
                <button onClick="reiniciarEjercicioJustificacion();">Reiniciar</button>
                <button onClick="mostrarSolucionEjercicioJustificacion();">Solución</button>
                <button id="boton_continuar" onClick="irPantallaEjercicioCuantoSabesTema('citas.php', 'input');" disabled>Continuar</button>
            </div>
        </div>
    </body>
</html>