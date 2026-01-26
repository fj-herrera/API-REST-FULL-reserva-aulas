<?php 

include_once __DIR__ . ('/controller.php');

$metodo = $_SERVER['REQUEST_METHOD'] ?? null;
$endpoint = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? null;
$parametros = $_SERVER['QUERY_STRING'] ?? null;

// Se valora el método y se llama a la funcion del controller
switch ($metodo){
    case 'GET': 
        manejarPeticionGET($endpoint,$parametros);
        break;
    case 'POST': 

        break;
    case 'PUT': 

        break;
    case 'DELETE': 
           
        break;
}
?>