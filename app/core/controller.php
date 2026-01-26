<?php

use Config\utilities\ValidEndpoints;
use Config\utilities\ResponseCodes;

include_once __DIR__ . ('/../controllers/BaseController.php');
include_once __DIR__ . ('/../controllers/aulaController.php');
include_once __DIR__ . ('/../controllers/profesorController.php');
include_once __DIR__ . ('/../controllers/franjaController.php');
include_once __DIR__ . ('/../controllers/reservaController.php');

/**
 * Función que valida loa endopoints entrantes comparando con validGET
 */
function validarPeticion($endpoint){
    $isValid = false;
    foreach (validEndpoints::GET as $clave => $valor){
        if( preg_match($valor, $endpoint, $matches)){
            $isValid = true;
        }
    }
    return $isValid;
}

function partirPeticion($endpoint){
    $parte = strtok($endpoint,"/");
    $peticion =[];
    while ($parte !== false){
        array_push($peticion, $parte);
        $parte = strtok("/");
    }
    return $peticion;
}

function obtenerParametros($parametros){
    parse_str($parametros, $grupo_parametros);
    return $grupo_parametros;
}

function comprobarFecha($fecha){
    $hoy = date('Y-m-d');
    $fecha = date($fecha);
    $resultado = ($fecha < $hoy) ? false : true; 
    return $resultado;
}

/**
 * Función que maneja la petición GET y llamando al Controller específico  
 */
function manejarPeticionGET($endpoint,$parametros){
    if (validarPeticion($endpoint) == false){
        echo 'url no valida';
        http_response_code(ResponseCodes::NOT_FOUND);
        exit;
    } else {
        $peticion = partirPeticion($endpoint);
        $parametros = obtenerParametros($parametros);
        switch ($peticion[1]){
            case 'aulas': 
                manejarGetAulas($peticion, $parametros);
                break;
            case 'profesores':    
                manejarGetProfesores($peticion, $parametros);
                break;    
            case 'franjas':
                manejarGetFranjas($peticion, $parametros);
                break;
            case 'reservas':   
                manejarGetReservas($peticion, $parametros);
                break;
        }
    }
}
?>