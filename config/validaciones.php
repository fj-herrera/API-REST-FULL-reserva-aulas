<?php

use Config\utilities\ValidValues;

    function validarNombre($nombre) {
        $isValid = false;
        if( preg_match(ValidValues::NOMBRE, $nombre, $matches)){
            $isValid = true;
        }
        return $isValid;
    }

    function validarEmail($email) {
        $isValid = false;
        if( preg_match(ValidValues::EMAIL, $email, $matches)){
            $isValid = true;
        }
        return $isValid;
    }

    function validarHora($hora) {
        $isValid = false;
        if( preg_match(ValidValues::HORA, $hora, $matches)){
            $isValid = true;
        }
        return $isValid;
    }

    function validarFecha($fecha) {
        return preg_match(ValidValues::FECHA, $fecha, $matches) ? true : false;
    }
    function comprobarFecha($fecha){
        $hoy = date('Y-m-d');
        $fecha = date($fecha);
        $resultado = ($fecha < $hoy) ? false : true;
        return $resultado;
    }
    function validarFranja($hora_i, $hora_f){
    // Comprobacion Franja horaria
        $isOk = true;
        // Si son iguales
        if ($hora_i === $hora_f) {
            $isOk = false;
        }
        // Si el fin es anterior al inicio
        else if ($hora_f < $hora_i) {
            $isOk = false;
        }     
        return $isOk;
    }
?>