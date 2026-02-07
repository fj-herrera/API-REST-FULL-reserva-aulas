<?php
include_once __DIR__ . '/controller.php';
use function Config\utilities\extraerPartesEndpoint;

$peticion=[];
$peticion['metodo'] = $_SERVER['REQUEST_METHOD'] ?? null;
$peticion['endpoint'] = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? null;
$peticion['parametros'] = $_SERVER['QUERY_STRING'] ?? null;
$peticion['body'] = file_get_contents('php://input') ?? null;

// Extraer partes del endpoint (recurso, id, sub_recurso, etc.)
$peticion = array_merge($peticion, extraerPartesEndpoint($peticion['endpoint']));

// Se valora el método y se llama a la funcion del controller
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