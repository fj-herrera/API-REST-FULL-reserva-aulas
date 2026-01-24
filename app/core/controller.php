<?php

use Config\utilities\ValidEndpoints;


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

/**
 * Función que maneja la perición GET, valido, pariend y llamando al 
 * Controller específico  
 */
function manejarPeticionGET($endpoint){
    if (validarPeticion($endpoint) == false){
        echo 'url no valida';
        http_response_code(ResponseCodes::NOT_FOUND);
        exit;
    } else {
        $peticion = partirPeticion($endpoint);

        switch ($peticion[1]){
            case 'aulas': 
                manejarGetAulas($peticion);
                break;
            case 'profesores':    
                manejarGetProfesores($peticion);
                break;    
            case 'franjas':
                manejarGetFranjas($peticion);
                break;
            case 'reservas':   
                manejarGetReservas($peticion);
                break;
        }
    }
}
?>