<?php
include_once __DIR__ . '/BaseService.php';

class FranjaService extends \App\Services\BaseService {
    protected $tabla = 'franjas';
    protected $campos = 'id, nombre, hora_inicio, hora_fin';
}
?>