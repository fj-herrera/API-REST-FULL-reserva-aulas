<?php
include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/profesorService.php';

use App\Controllers\BaseController;

/**
 * Controlador para gestionar las operaciones sobre el recurso 'profesor'.
 * Hereda de BaseController y delega las operaciones CRUD al servicio especifico, 
 * usando el método correspondiente según la petición.
 */
class ProfesorController extends BaseController {
    public function manejarGet($peticion) {
        $servicio = new ProfesorService();
        return $this->GET($servicio, $peticion);
    }

    public function manejarPost($peticion) {
        $servicio = new ProfesorService();
        return $this->Post($servicio, $peticion);
    }

    public function manejarPut($peticion) {
        $servicio = new ProfesorService();
        return $this->Put($servicio, $peticion);
    }

    public function manejarDelete($peticion) {
        $servicio = new ProfesorService();
        return $this->Delete($servicio, $peticion);
    }
}

/**
 * Instancia el controlador profesorController y ejecuta el método correspondiente
 * según el método HTTP de la petición.
 */
function instanciarProfesorController($peticion) {
    $metodo = $peticion->getMetodo();  
    if ($metodo === 'GET') {
        $controller = new ProfesorController();
        $controller->manejarGet($peticion);
    }
    else if($metodo === 'POST') {
        $controller = new ProfesorController();
        $controller->manejarPost($peticion);
    }
    else if($metodo === 'PUT') {
        $controller = new ProfesorController();
        $controller->manejarPut($peticion);
    }
    else if($metodo === 'DELETE') {
        $controller = new ProfesorController();
        $controller->manejarDelete($peticion);
    }
}