<?php
include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/reservaService.php';

class ReservaController extends \App\Controllers\BaseController {
    public function manejarGet($peticion) {
        $servicio = new ReservaService();
        return $this->GET($servicio, $peticion);
    }
}

// Uso procedural (si no usas instancias en el router):
function instanciarReservaController($peticion) {
    $controller = new ReservaController();
    $controller->manejarGet($peticion);
}

?>