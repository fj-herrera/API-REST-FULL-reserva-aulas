<?php
use Config\utilities\Codes;
use Config\utilities\ErrMsgs;
use Config\utilities\Peticion;
use function Config\utilities\validarPeticion;
use function Config\utilities\normalizarBody;


include_once __DIR__ . ('/../controllers/BaseController.php');
include_once __DIR__ . ('/../controllers/aulaController.php');
include_once __DIR__ . ('/../controllers/profesorController.php');
include_once __DIR__ . ('/../controllers/franjaController.php');
include_once __DIR__ . ('/../controllers/reservaController.php');
include_once __DIR__ . '/../../config/validaciones.php';

/**
 * Función que maneja la petición GET y llamado al Controller específico
 */
function manejarPeticionGET($peticion){
    if (validarPeticion($peticion['endpoint']) == false){
        http_response_code(Codes::NOT_FOUND);
        exit;
    } else {
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

/**
 * Función que maneja la petición POST y llamando al Controller específico
 */
function manejarPeticionPOST($peticion){
    if (validarPeticion($peticion['endpoint']) == false){
        http_response_code(Codes::NOT_FOUND);
        exit; 
    } else {
        $body = normalizarBody($peticion['body']);
        $rol = $body['rol_user'] ?? null;
        $clave = $body['api_key'] ?? null;
        $endpoint = $peticion['endpoint'];

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

/**
 * Función que maneja la petición PUT y llamando al Controller específico
 */
function manejarPeticionPUT($peticion){
    if (validarPeticion($peticion['endpoint']) == false){
        http_response_code(Codes::NOT_FOUND);
        exit; 
    } else {
        $body = normalizarBody($peticion['body']);
        $rol = $body['rol_user'] ?? null;
        $clave = $body['api_key'] ?? null;
        $id_user = $body['id_user'] ?? null;
        $endpoint = $peticion['endpoint'];

        if (!validarAccesoPUT($endpoint, $rol, $id_user, $body, $clave)) {
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

/**
 * Función que maneja la petición DELETE y llamando al Controller específico
 */
function manejarPeticionDELETE($peticion){
    if (validarPeticion($peticion['endpoint']) == false){
        http_response_code(Codes::NOT_FOUND);
        exit; 
    } else {
        // Seguridad solo admin
        // El id se extrae del endpoint, no del body
        $body = normalizarBody($peticion['body']);
        $rol = $body['rol_user'] ?? null;
        $clave = $body['api_key'] ?? null;
        $endpoint = $peticion['endpoint'];

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
                // El id de la reserva a borrar se extrae del endpoint, no del body
                instanciarReservaController($peticion);
                break;
        }
    }
}

