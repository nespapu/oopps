<?php
    //Cargar módulos
    require 'db.php';
    //Definir variables
    $nombre = $_POST["nombre"];
    $clave = $_POST["clave"];
    $error = "";
    //Realizar acciones BD
    $sql = "SELECT * FROM usuario WHERE nombre = '$nombre'";
    $resultado = mysqli_query($conexion, $sql);
    if(mysqli_num_rows($resultado) > 0){
        $usuario = mysqli_fetch_assoc($resultado);
        if($usuario["clave"] != $clave){
            $error = "clave";
        }
    }else{
        $error = "nombre";
    }
    
    // Buscar el identificador de la oposición que estudia el usuario
    $sql = "SELECT * FROM oposicion o INNER JOIN usuario_estudiar_oposicion ueo ON o.codigo = ueo.codigo_oposicion WHERE ueo.nombre_usuario = '$nombre'";
    $resultado = mysqli_query($conexion, $sql);

    if ($error=='' && mysqli_num_rows($resultado) == 0) {
        $error = "configuracion";
    }

    $fila = mysqli_fetch_assoc($resultado);
    $oposicion = $fila['codigo'];
    
    mysqli_close($conexion);

    
    //Redirigir en función de si ha habido algún error con el usuario
    if($error != ""){
        header('Location: ../index.php?error='.$error); 
    }else{
        header('Location: ../menu.php?nombre='.$nombre.'&oposicion='.$oposicion);  
    }
?>