<?php

include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/aulaService.php';

use App\Controllers\BaseController;

/**
 * Controlador para gestionar las operaciones sobre el recurso 'aulas'.
 * Hereda de BaseController y delega las operaciones CRUD al servicio especifico, 
 * usando el método correspondiente según la petición.
 */
class AulaController extends BaseController {
    public function manejarGet($peticion) {
        $servicio = new AulaService();
        return $this->Get($servicio, $peticion);
    }

    public function manejarPost($peticion) {
        $servicio = new AulaService();
        return $this->Post($servicio, $peticion);
    }

    public function manejarPut($peticion) {
        $servicio = new AulaService();
        return $this->Put($servicio, $peticion);
    }

    public function manejarDelete($peticion) {
        $servicio = new AulaService();
        return $this->Delete($servicio, $peticion);
    }
}

/**
 * Instancia el controlador aulaController y ejecuta el método correspondiente
 * según el método HTTP de la petición.
 */
function instanciarAulaController($peticion) {
    $metodo = $peticion->getMetodo();  
    if ($metodo === 'GET') {
        $controller = new AulaController();
        $controller->manejarGet($peticion);
    }
    else if($metodo === 'POST') {
        $controller = new AulaController();
        $controller->manejarPost($peticion);
    }
    else if($metodo === 'PUT') {
        $controller = new AulaController();
        $controller->manejarPut($peticion);
    }
    else if($metodo === 'DELETE') {
        $controller = new AulaController();
        $controller->manejarDelete($peticion);
    }
}