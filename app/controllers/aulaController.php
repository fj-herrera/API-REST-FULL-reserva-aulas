<?php
include_once __DIR__ . '/BaseController.php';
include_once __DIR__ . '/../services/aulaService.php';

class AulaController extends \App\Controllers\BaseController {
    public function manejarGetAulas($peticion, $parametros) {
        $servicio = new AulaService();
        return $this->GET($servicio, $peticion, $parametros);
    }
}

// Uso procedural (si no usas instancias en el router):
function manejarGetAulas($peticion,$parametros) {
    $controller = new AulaController();
    $controller->manejarGetAulas($peticion,$parametros);
}

?>