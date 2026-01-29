<?php
include_once __DIR__ . '/BaseService.php';

class ProfesorService extends \App\Services\BaseService {
    protected $tabla = 'profesores';
    protected $campos = 'id, nombre, email, rol';
    protected $campos_insert = "nombre, email, rol";
    protected $fk = 'id_profesor';
}
?>