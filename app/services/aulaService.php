<?php
include_once __DIR__ . '/BaseService.php';

class AulaService extends \App\Services\BaseService {
    protected $tabla = 'aulas';
    protected $campos = 'id, nombre, capacidad, descripcion';
}
?>