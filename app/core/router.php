<?php 

include_once __DIR__ . ('/controller.php');


$metodo = $_SERVER['REQUEST_METHOD'];
$endpoint = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Se volra la peticion y se llama al controller
switch ($metodo){
    case 'GET': 
        manejarPeticionGET($endpoint);
        break;

    case 'POST': 

        break;
    case 'PUT': 

        break;
    case 'DELETE': 
           
        break;
}
?>