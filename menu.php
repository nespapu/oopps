<html>
    <head>
        <link rel="stylesheet" href="css/miEstilo.css">
        <script src="js/misScripts.js"></script>
    </head>
    <body>
        <?php 
            //Almacenar en campos inputs escondidos los parámetros de entrada
            $nombre=$_GET["nombre"];
            $oposicion=$_GET["oposicion"];
            print "<input type='hidden' value='$nombre' id='nombre'>";
            print "<input type='hidden' value='$oposicion' id='oposicion'>";

            //Realizar consulta a BD para obtener datos
            require 'php/db.php';
            $sql = "SELECT * FROM tema t WHERE codigo_oposicion = '$oposicion' ORDER BY numeracion ASC";  
            $resultado = mysqli_query($conexion, $sql);
            mysqli_close($conexion);
        ?>
        <h1>Hola, <?php print ucfirst($nombre)?></h1>
        <p>Elije el ejercicio que quieres realizar, configúralo y pon a prueba tus conocimientos</p>
        <hr>
        <h2><u>¿Cuánto sabes de un tema?</u></h2>
        <h3>Tema y dificultad:</h3>
        <?php
            //Mostrar temas disponibles para el usuario
            print "<select id='tema'>";
            print "<option value='default' selected>Elegir tema</option>";
            print "<option value='0'>Aleatorio</option>";
            if (mysqli_num_rows($resultado) > 0){
                while($fila = mysqli_fetch_assoc($resultado)){
                    print "<option value='".$fila['orden']."'>".$fila['orden']."-".$fila['titulo']."</option>";
                }
            }
            print "</select>";
        ?>
        <select id="dificultad">
            <option value='default' selected>Elegir dificultad</option>
            <option value="1">Fácil</option>
            <option value="2">Normal</option>
            <option value="3">Difícil</option>
            <option value="4">Muy difícil</option>
        </select>
        <h3>Índice:</h3>
        <input type="checkbox" id="numeracion"> <label for="numeracion">Numeración</label>
        <input type="checkbox" id="apartado"> <label for="apartado">Apartado</label>
        <h3>Ciclos, leyes y módulos:</h3>
        <input type="checkbox" id="ciclos"> <label for="ciclos">Ciclos</label>
        <input type="checkbox" id="leyes"> <label for="leyes">Leyes</label>
        <input type="checkbox" id="modulos"> <label for="modulos">Módulos</label>
        <h3>Citas:</h3>
        <input type="checkbox" id="conceptoCita"> <label for="conceptoCita">Concepto</label>
        <input type="checkbox" id="autorCita"> <label for="autorCita">Autor</label>
        <input type="checkbox" id="anyoCita"> <label for="anyoCita">Año</label>
        <input type="checkbox" id="cita"> <label for="cita">Cita</label>
        <input type="checkbox" id="numeracionCita"> <label for="numeracionCita">Numeración</label>
        <input type="checkbox" id="apartadoCita"> <label for="apartadoCita">Apartado</label>
        <h3>Herramientas:</h3>
        <input type="checkbox" id="herramienta"> <label for="herramienta">Herramienta</label>
        <input type="checkbox" id="descripcionHerramienta"> <label for="descripcionHerramienta">Descripción</label>
        <h3>Contexto escolar:</h3>
        <input type="checkbox" id="ensenyanza"> <label for="ensenyanza">Enseñanza</label>
        <input type="checkbox" id="ciclosContexto"> <label for="ciclosContexto">Ciclos</label>
        <input type="checkbox" id="modulosContexto"> <label for="modulosContexto">Módulos</label>
        <input type="checkbox" id="conceptoContextoEscolar"> <label for="conceptoContextoEscolar">Concepto</label>
        <input type="checkbox" id="aplicacionContextoEscolar"> <label for="aplicacionContextoEscolar">Aplicación</label>
        <input type="checkbox" id="metodo"> <label for="metodo">Método</label>
        <h3>Contexto laboral</h3>
        <input type="checkbox" id="campo"> <label for="campo">Campo</label>
        <input type="checkbox" id="profesional"> <label for="profesional">Ciclos</label>
        <input type="checkbox" id="conceptoContextoLaboral"> <label for="conceptoContextoLaboral">Concepto</label>
        <input type="checkbox" id="aplicacionContextoLaboral"> <label for="aplicacionContextoLaboral">Aplicación</label>
        <input type="checkbox" id="beneficio"> <label for="beneficio">Beneficio</label>
        <h3>Bibliografía:</h3>
        <input type="checkbox" id="autorLibro"> <label for="autorLibro">Autor</label>
        <input type="checkbox" id="anyoLibro"> <label for="anyoLibro">Año</label>
        <input type="checkbox" id="tituloLibro"> <label for="tituloLibro">Título</label>
        <input type="checkbox" id="editorial"> <label for="editorial">Editorial</label>
        <h3>Webgrafía:</h3>
        <input type="checkbox" id="nombreWeb"> <label for="nombreWeb">Sitio web</label>
        <input type="checkbox" id="url"> <label for="url">URL</label>
        <br>
        <button onclick="irPantallaEjercicioCuantoSabesTema('titulo.php', 'checkbox');">Comenzar</button>
        <h2><u>Simulacro de examen</u></h2>
        <p>Genera un tema al azar y desarróllalo antes de que se acabe el tiempo</p>
        <button onclick="irPantallaEjercicioSimulacro();">Comenzar</button>
    </body>
</html>