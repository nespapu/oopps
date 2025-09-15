<html>
    <head>
        <script src="js/misScripts.js"></script>
    </head>
    <body>
        <?php 
            //Almacenar en campos inputs escondidos los parámetros de entrada
            $nombre=$_GET["nombre"];
            print "<input type='hidden' value='$nombre' id='nombre'>";

            //Realizar consulta a BD para obtener datos
            require 'php/db.php';
            $sql = "SELECT * FROM oposicion o INNER JOIN usuario_estudiar_oposicion ueo ON o.codigo = ueo.codigo_oposicion WHERE ueo.nombre_usuario = '$nombre'";        
            $resultado = mysqli_query($conexion, $sql);
            mysqli_close($conexion);
        ?>
        <h1>Hola, <?php print ucfirst($nombre)?></h1>
        <p>Elije la oposición en la que quieres entrenar:</p>
        <?php
            //Mostrar oposiciones del usuario
            print "<label for='oposicion'>Oposición: </label>";
            print "<select name='oposicion' id='oposicion' onchange='irAMenuOposicion();'>";
            print "<option value='default' selected>Elegir oposicion</option>";
            if (mysqli_num_rows($resultado) > 0){
                while($fila = mysqli_fetch_assoc($resultado)){
                    print "<option value='".$fila['codigo']."'>".$fila['especialidad']."</option>";
                }
            }
            print "</select>";  
        ?>
    </body>
</html>