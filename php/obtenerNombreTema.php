<?php
    $sql = "SELECT * FROM tema WHERE codigo_oposicion='$c_oposicion' AND orden='$c_tema'";
    $resultado = mysqli_query($conexion, $sql);
    $titulo = mysqli_fetch_assoc($resultado)['titulo'];
?>