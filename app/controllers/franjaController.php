<?php

include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/franjaService.php';

use App\Controllers\BaseController;

/**
 * Controlador para gestionar las operaciones sobre el recurso 'franjas'.
 * Hereda de BaseController y delega las operaciones CRUD al servicio especifico, 
 * usando el método correspondiente según la petición.
 */
class FranjaController extends BaseController {
    public function manejarGet($peticion) {
        $servicio = new FranjaService();
        return $this->GET($servicio, $peticion);
    }
    
    public function manejarPost($peticion) {
        $servicio = new FranjaService();
        return $this->Post($servicio, $peticion);
    }

    public function manejarPut($peticion) {
        $servicio = new FranjaService();
        return $this->Put($servicio, $peticion);
    }

    public function manejarDelete($peticion) {
        $servicio = new FranjaService();
        return $this->Delete($servicio, $peticion);
    }
}

/**
 * Instancia el controlador franjaController y ejecuta el método correspondiente
 * según el método HTTP de la petición.
 */
function instanciarFranjaController($peticion) {
    $metodo = $peticion->getMetodo();  
    if ($metodo === 'GET') {
        $controller = new FranjaController();
        $controller->manejarGet($peticion);
    }
    else if($metodo === 'POST') {
        $controller = new FranjaController();
        $controller->manejarPost($peticion);
    }
    else if($metodo === 'PUT') {
        $controller = new FranjaController();
        $controller->manejarPut($peticion);
    }
    else if($metodo === 'DELETE') {
        $controller = new FranjaController();
        $controller->manejarDelete($peticion);
    }
}