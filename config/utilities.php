<?php 
namespace Config\utilities;

class ValidEndpoints {
    public const GET = [
        'aulas' =>          '#^/api/aulas$#',           // /api/aulas
        'aulas-id' =>       '#^/api/aulas/\\d+$#',      // /api/aulas/1
        'profesores' =>     '#^/api/profesores$#',      // /api/profesores
        'profesores-id' =>  '#^/api/profesores/\\d+$#', // /api/profesores/1
        'franjas' =>        '#^/api/franjas$#',         // /api/franjas
        'franjas-id' =>     '#^/api/franjas/\\d+$#',    // /api/franjas/1
        'reservas' =>       '#^/api/reservas$#',        // /api/reservas
        'reservasas-id' =>  '#^/api/reservas/\\d+$#'    // /api/reservas/1
    
    ];
    public const validPOST = [
       
    ];
    // ...otros métodos si los necesitas
}

class ResponseCodes {
    public const OK =           200;
    public const CREATED =      201;
    public const NO_CONTENT =   204;
    public const BAD_REQUEST =  400;
    public const UNAUTHORIZED = 401;
    public const FORBIDDEN =    403;
    public const NOT_FOUND =    404;
    public const SERVER_ERROR = 500;
}

class ErrMsgs {
    public const NOT_FOUND = 'Recurso no encontrado';
    public const INVALID_ENDPOINT = 'URL no válida';
    public const SERVER_ERROR = 'Error interno del servidor';
}

class OkMsgs {
    public const NOT_FOUND = 'Recurso no encontrado';
    public const INVALID_ENDPOINT = 'URL no válida';
    public const SERVER_ERROR = 'Error interno del servidor';
}
?>