<?php

include_once __DIR__ . '/BaseService.php';

use \App\Services\BaseService;

/**
 * Servicio para gestionar operaciones sobre el recurso 'aulas'.
 *
 * Hereda de BaseService y proporciona métodos para validar duplicidad de nombre,
 * agregar, actualizar y borrar aulas, asegurando la integridad de datos y evitando duplicados.
 */
class AulaService extends BaseService {
    protected $tabla = 'aulas';
    protected $campos = 'id, nombre, capacidad, descripcion';
    protected $campos_insert = "nombre, capacidad, descripcion";
    protected $fk = 'id_aula';

    protected function comprobarNombre($nombre){
        if ($nombre) {
            $nombres = $this->obtenerNombres();
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

        // 1 Obtiene el aula por ID y verifica si existe antes de actualizar
        $aulas = $this->obtenerPorID($body['id']);
        $ids_aulas = array_column($aulas, 'id');
        $existe = in_array($body['id'], $ids_aulas);

        // 2 Comprobar si el nombre ya existe en otra aula
        $nombres = $this->obtenerNombres();
        $aula_actual = $this->obtenerPorID($body['id']);
        $nombre_actual = $aula_actual[0]['nombre'] ?? null;

        // 3 Si el nombre ya existe en otra aula
        if ($body['nombre'] !== $nombre_actual && in_array($body['nombre'], $nombres)) {
            return 'nombre'; // Nombre duplicado en otra aula
        }
        
        if ($existe === true){
            $sql = "UPDATE {$this->tabla} SET nombre = ?, capacidad = ?, descripcion = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            // Si descripcion no está, usar cadena vacía
            $descripcion = isset($body['descripcion']) ? $body['descripcion'] : '';
            return $stmt->execute([$body['nombre'],$body['capacidad'],$descripcion,$body['id']]);
        } else {
            return 'no_existe';
        }
    }
    /**
     * Elimina un aula por su ID.
     * Si el aula no tiene reservas asociadas, la elimina directamente.
     * Si tiene reservas, devuelve 'reservas' para indicar que no se puede eliminar.
     */
    public function borrarAula($id){
        $reservas = $this->obtenerPorID_Reservas($id);
        if (empty($reservas)){
            $sql = "DELETE FROM {$this->tabla} WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        }
        else {
            return 'reservas';
        }
    }
}