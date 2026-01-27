<?php

use Config\utilities\ValidEndpoints;
use Config\utilities\ResponseCodes;
use Config\utilities\Peticion;

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
    foreach (validEndpoints::VALID as $clave => $valor){
        if( preg_match($valor, $endpoint, $matches)){
            $isValid = true;
        }
    }
    return $isValid;
}

function partirEndpoint($endpoint){
    $parte = strtok($endpoint,"/");
    $peticion =[];
    while ($parte !== false){
        array_push($peticion, $parte);
        $parte = strtok("/");
    }
    return $peticion;
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
function manejarPeticionGET($peticion){
    if (validarPeticion($peticion['endpoint']) == false){
        http_response_code(ResponseCodes::NOT_FOUND);
        exit;
    } else {
        
        $peticion['endpoint'] = partirEndpoint($peticion['endpoint']);
        
        $peticion = new Peticion ($peticion);
        // $peticion->printPeticion();
        
        switch ($peticion->get_recurso()){
            case 'aulas': 
                instanciarAulaController($peticion);
                break;
            case 'profesores':    
                instanciarProfesorController($peticion);
                break;    
            case 'franjas':
                instanciarFranjaController($peticion);
                break;
            case 'reservas':   
                instanciarReservaController($peticion);
                break;
        }
       
    }
}


/**
 * Función que maneja la petición POST y llamando al Controller específico  
 */
function manejarPeticionPOST($peticion){
    if (validarPeticion($peticion['endpoint']) == false){
        http_response_code(ResponseCodes::NOT_FOUND);
        exit; 
    } else {
        $peticion['endpoint'] = partirEndpoint($peticion['endpoint']);

        $peticion = new Peticion ($peticion);
        $peticion->printPeticion();
        //$body = ;
        /*
        switch ($peticion[1]){
            case 'aulas': 
                instanciarAulaController($peticion, $body);
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
        */
    }
}
?>