<?php

include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/reservaService.php';

use App\Controllers\BaseController;

/**
 * Controlador para gestionar las operaciones sobre el recurso 'reservas'.
 * Hereda de BaseController y delega las operaciones CRUD al servicio especifico, 
 * usando el método correspondiente según la petición.
 */
class ReservaController extends BaseController {
    public function manejarGet($peticion) {
        $servicio = new ReservaService();
        return $this->GET($servicio, $peticion);
    }

    public function manejarPost($peticion) {
        $servicio = new ReservaService();
        return $this->Post($servicio, $peticion);
    }

    public function manejarPut($peticion) {
        $servicio = new ReservaService();
        return $this->Put($servicio, $peticion);
    }

    public function manejarDelete($peticion) {
        $servicio = new ReservaService();
        return $this->Delete($servicio, $peticion);
    }
}

/**
 * Instancia el controlador reservaController y ejecuta el método correspondiente
 * según el método HTTP de la petición.
 */
function instanciarReservaController($peticion) {
    $metodo = $peticion->getMetodo();  
    if ($metodo === 'GET') {
        $controller = new ReservaController();
        $controller->manejarGet($peticion);
    }
    else if($metodo === 'POST') {
        $controller = new ReservaController();
        $controller->manejarPost($peticion);
    }
    else if($metodo === 'PUT') {
        $controller = new ReservaController();
        $controller->manejarPut($peticion);
    }
    else if($metodo === 'DELETE') {
        $controller = new ReservaController();
        $controller->manejarDelete($peticion);
    }
}