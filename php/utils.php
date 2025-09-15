<?php
    function obtenerAyuda($cadena, $nivel, $modo){
        return ($modo == "letras") ? ocultarLetrasCadena($cadena, $nivel) : ocultarPalabrasCadena($cadena, $nivel);
    }

    function ocultarLetrasCadena($cadena, $nivel) {
        $factor = obtenerFactorOcultacion($nivel);
        $palabras = explode(" ", $cadena);
        foreach($palabras as &$palabra){
            $numLetrasOcultar = ceil(strlen($palabra) * $factor);
            $posicionesOcultas = array();
            for($i=0; $i<$numLetrasOcultar; $i++){
                $posicionNoOcupadaEncontrada=false;
                while(!$posicionNoOcupadaEncontrada){
                    $posicionCandidata = rand(0, strlen($palabra)-1);
                    if(!array_key_exists($posicionCandidata, $posicionesOcultas)){
                        $posicionesOcultas[$posicionCandidata] = "_";
                        $posicionNoOcupadaEncontrada = true;
                    }
                }
            }
            foreach($posicionesOcultas as $posicion => $valor){
                $palabra = mb_substr_replace($palabra, $valor, $posicion, 1);
            }
            $posicionesOcultas = array();
        }
        return implode(" ", $palabras);
    }

    function ocultarPalabrasCadena($cadena, $nivel){
        $factor = obtenerFactorOcultacion($nivel);
        $palabras = preg_split("/[\s,:]+/", $cadena);
        $numPalabrasOcultar = ceil(count($palabras) * $factor);
        $posicionesOcultas = array();
        for($i=0; $i<$numPalabrasOcultar; $i++){
            $posicionNoOcupadaEncontrada=false;
            while(!$posicionNoOcupadaEncontrada){
                $posicionCandidata = rand(0, count($palabras)-1);
                if(!array_key_exists($posicionCandidata, $posicionesOcultas)){
                    $posicionesOcultas[$posicionCandidata] = strlen($palabras[$posicionCandidata]);//Guardar la longitud de la palabra para la sustitución posterior por caracteres _
                    $posicionNoOcupadaEncontrada = true;
                }
            }
        }
        foreach($posicionesOcultas as $posicion => $valor){
            $palabras[$posicion] = obtenerMascaraOcultacion($valor);
        }
        return implode(" ", $palabras);
    }

    function obtenerFactorOcultacion($nivel){
        $factor = 0;
        switch($nivel){
            case 1:
                $factor = 0.25;
                break;
            case 2:
                $factor = 0.5;
                break;
            case 3:
                $factor = 0.75;
                break;
        }
        return $factor;
    }

    function obtenerMascaraOcultacion($longitud) {
        $mascara = "";
        for ($i=0; $i<$longitud; $i++){
            $mascara .= "_";
        }
        return $mascara;
    }

    function mb_substr_replace($original, $replacement, $position, $length)
    {
        $startString = mb_substr($original, 0, $position, "UTF-8");
        $endString = mb_substr($original, $position + $length, mb_strlen($original), "UTF-8");

        $out = $startString . $replacement . $endString;

        return $out;
    }
?>