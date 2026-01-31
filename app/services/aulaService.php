<?php
include_once __DIR__ . '/BaseService.php';

class AulaService extends \App\Services\BaseService {
    protected $tabla = 'aulas';
    protected $campos = 'id, nombre, capacidad, descripcion';
    protected $campos_insert = "nombre, capacidad, descripcion";
    protected $fk = 'id_aula';

    protected function comprobarNombre($nombre){
        if ($nombre) {
            $nombres = $this->obtenerNombres();
            // Comprobar si $nombre está en el array de nombres
            return in_array($nombre, $nombres);
        }
        return false;
    }

    public function agregarAula($body){
        // Si el nombre ya existe
        if ($this->comprobarNombre($body['nombre'])) {
            return false;
        }
        $sql = "INSERT INTO {$this->tabla} ({$this->campos_insert}) VALUES (?,?,?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$body['nombre'],$body['capacidad'],$body['descripcion']]);
    }

    public function actualizarAula($body) {
        $aulas = $this->obtenerPorID($body['id']);
        $ids_aulas = array_column($aulas, 'id');
        $existe = in_array($body['id'], $ids_aulas);

        // Comprobar si el nombre ya existe en otra aula
        $nombres = $this->obtenerNombres();
        $aula_actual = $this->obtenerPorID($body['id']);
        $nombre_actual = $aula_actual[0]['nombre'] ?? null;

        if ($body['nombre'] !== $nombre_actual && in_array($body['nombre'], $nombres)) {
            return 'nombre'; // Nombre duplicado en otra aula
        }
        
        if ($existe === true){
            $sql = "UPDATE {$this->tabla} SET nombre = ?, capacidad = ?, descripcion = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$body['nombre'],$body['capacidad'],$body['descripcion'],$body['id']]);
        } else {
            return 'no_existe';
        }
    }
}
?>