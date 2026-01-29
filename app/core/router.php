<?php

include_once __DIR__ . ('/controller.php');


$peticion=[];
$peticion['metodo'] = $_SERVER['REQUEST_METHOD'] ?? null;
$peticion['endpoint'] = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? null;
$peticion['parametros'] = $_SERVER['QUERY_STRING'] ?? null;
$peticion['body'] = file_get_contents('php://input') ?? null;

// Se valora el método y se llama a la funcion del controller
switch ($peticion['metodo']){
    case 'GET':
        manejarPeticionGET($peticion);
        break;
    case 'POST':
        manejarPeticionPOST($peticion);
        break;
    case 'PUT':

        break;
    case 'DELETE':
           
        break;
}
?>