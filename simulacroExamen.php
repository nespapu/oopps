<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
        <link rel="stylesheet" href="css/miEstilo.css">
        <script src="js/misScripts.js"></script>
        <style>
            h2 {
                text-align: center;
            }
            p {
            text-align: center;
            font-size: 60px;
            margin-top: 0px;
            }
        </style>
    </head>
    <body>
        <?php 
            $c_nombre=$_GET["nombre"];
            $c_oposicion=$_GET["oposicion"];
            print "<input type='hidden' value='$c_nombre' id='nombre'>";
            print "<input type='hidden' value='$c_oposicion' id='oposicion'>";
            require 'php/db.php';
            $sql = "SELECT * FROM tema WHERE codigo_oposicion='".$_GET['oposicion']."'";
            $resultado = mysqli_query($conexion, $sql);
            $numTemas = mysqli_num_rows($resultado);
            $aleatorio = rand(0, $numTemas-1 );
            for($i=0; $i<=$aleatorio; $i++){
                $tema = mysqli_fetch_assoc($resultado);
            } 
            $orden = $tema['orden'];
            $titulo = $tema['titulo'];
            print "<button onClick='irAMenuOposicion();'>Volver</button>";
            print "<h2>Tema $orden $titulo</h2>";
        ?>
        <p id="temporizador"></p>
    </body>
    <script>
        // Set the date we're counting down to
        var countDownDate = new Date(new Date().getTime() + 2 * (60 * 60 * 1000)).getTime()

        // Update the count down every 1 second
        var x = setInterval(function() {

        // Get today's date and time
        var now = new Date().getTime();

        // Find the distance between now and the count down date
        var distance = countDownDate - now;

        // Time calculations for hours, minutes and seconds
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Display the result in the element with id="demo"
        document.getElementById("temporizador").innerHTML = hours + "h " + minutes + "m " + seconds + "s ";

        // If the count down is finished, write some text
        if (distance < 0) {
            clearInterval(x);
            document.getElementById("temporizador").innerHTML = "¡MANOS ARRIBA!";
        }
        }, 1000);
    </script>
</html>