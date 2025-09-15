<?php
    //Script abrir conexión BD y proporcionar variable $conexion para realizar acciones
    $servidor = "localhost:3306";
    $usuario = "root";
    $clave = "";
    $basedatos = "oopps";

    $conexion = mysqli_connect($servidor, $usuario, $clave, $basedatos);
    if (!$conexion) {
        die("Conexión fallida: " . mysqli_connect_error());
    }
?>