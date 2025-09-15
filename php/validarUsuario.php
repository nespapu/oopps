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
    mysqli_close($conexion);
    //Redirigir en función de si ha habido algún error con el usuario
    if($error != ""){
        header('Location: ../index.php?error='.$error); 
    }else{
        header('Location: ../oposicion.php?nombre='.$nombre);  
    }
?>