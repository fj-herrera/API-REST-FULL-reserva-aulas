<?php

use Config\utilities\ValidEndpoints;
use Config\utilities\Codes;
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
 * Función que maneja la petición GET y llama al instanciador de Controller específico
 */
function manejarPeticionGET($peticion){
    if (validarPeticion($peticion['endpoint']) == false){
        http_response_code(Codes::NOT_FOUND);
        exit;
    } else {
        $peticion['endpoint'] = partirEndpoint($peticion['endpoint']);
        $peticion = new Peticion ($peticion);
        // $peticion->printPeticion();
        switch ($peticion->getRecurso()){
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
        http_response_code(Codes::NOT_FOUND);
        exit; 
    } else {
        $peticion['endpoint'] = partirEndpoint($peticion['endpoint']);

        $peticion = new Peticion ($peticion);
        //$body = ;
        
        switch ($peticion->getRecurso()){
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
?>