<?php

use Config\utilities\ValidEndpoints;
use Config\utilities\AdminEndpoints;
use Config\utilities\Codes;
use Config\utilities\ErrMsgs;
use Config\utilities\Peticion;
use function Config\utilities\validarPeticion;
use function Config\utilities\partirEndpoint;


include_once __DIR__ . ('/../controllers/BaseController.php');
include_once __DIR__ . ('/../controllers/aulaController.php');
include_once __DIR__ . ('/../controllers/profesorController.php');
include_once __DIR__ . ('/../controllers/franjaController.php');
include_once __DIR__ . ('/../controllers/reservaController.php');
include_once __DIR__ . '/../../config/validaciones.php';

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

/**
 * Función que maneja la petición PUT y llamando al Controller específico
 */
function manejarPeticionPUT($peticion){
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

/**
 * Función que maneja la petición DELETE y llamando al Controller específico
 */
function manejarPeticionDELETE($peticion){
    if (validarPeticion($peticion['endpoint']) == false){
        http_response_code(Codes::NOT_FOUND);
        exit; 
    } else {
        $peticion['endpoint'] = partirEndpoint($peticion['endpoint']);

        // Seguridad solo admin
        $body = json_decode(file_get_contents('php://input'), true);
        $rol = $body['rol_user'] ?? null;
        $clave = $body['api_key'] ?? null;
        $endpoint = $_SERVER['REQUEST_URI'];

        if (!validarAcceso($endpoint, $rol, $clave)) {
            http_response_code(403);
            echo json_encode(['Message' => ErrMsgs::PERMISOS]);
            exit;
        }        
        
        $peticion = new Peticion ($peticion);
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