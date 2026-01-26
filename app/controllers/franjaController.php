<?php
include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/franjaService.php';

class FranjaController extends \App\Controllers\BaseController {
    public function manejarGetFranjas($peticion, $parametros) {
        $servicio = new FranjaService();
        return $this->GET($servicio, $peticion, $parametros);
    }
}

// Uso procedural (si no usas instancias en el router):
function manejarGetFranjas($peticion, $parametros) {
    $controller = new FranjaController();
    $controller->manejarGetFranjas($peticion, $parametros);
}

?>