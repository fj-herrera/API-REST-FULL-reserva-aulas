<?php

include_once __DIR__ . '/controller.php';
use function Config\utilities\extraerPartesEndpoint;

// Construye el array $peticion con los datos de la solicitud HTTP
$peticion = [];
$peticion['metodo'] = $_SERVER['REQUEST_METHOD'] ?? null; 
$peticion['endpoint'] = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? null;
$peticion['parametros'] = $_SERVER['QUERY_STRING'] ?? null; 
$peticion['body'] = file_get_contents('php://input') ?? null;

// Extrae partes adicionales del endpoint (como id, subrecurso) y las añade al array
$peticion = array_merge($peticion, extraerPartesEndpoint($peticion['endpoint']));

// Se valora el método y se llama a la funcion especifica del controller
switch ($peticion['metodo']){
    case 'GET':
        manejarPeticionGET($peticion);
        break;
    case 'POST':
        manejarPeticionPOST($peticion);
        break;
    case 'PUT':
        manejarPeticionPUT($peticion);
        break;
    case 'DELETE':
        manejarPeticionDELETE($peticion);   
        break;
}