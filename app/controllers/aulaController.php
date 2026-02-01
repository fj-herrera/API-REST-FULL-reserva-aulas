<?php
include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/aulaService.php';

class AulaController extends \App\Controllers\BaseController {
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