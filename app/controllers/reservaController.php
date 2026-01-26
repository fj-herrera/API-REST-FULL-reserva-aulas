<?php
include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/reservaService.php';

class ReservaController extends \App\Controllers\BaseController {
    public function manejarGetReservas($peticion, $parametros) {
        $servicio = new ReservaService();
        return $this->GET($servicio, $peticion, $parametros);
    }
}

// Uso procedural (si no usas instancias en el router):
function manejarGetReservas($peticion, $parametros) {
    $controller = new ReservaController();
    $controller->manejarGetReservas($peticion, $parametros);
}

?>