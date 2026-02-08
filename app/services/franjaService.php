<?php

include_once __DIR__ . '/BaseService.php';

use \App\Services\BaseService; 


/**
 * Servicio para gestionar operaciones sobre el recurso 'franjas'.
 *
 * Hereda de BaseService y proporciona métodos para validar duplicidad de nombre y franja,
 * agregar, actualizar y borrar franjas, asegurando la integridad de datos y evitando duplicados.
 */
class FranjaService extends BaseService {
        
    protected $tabla = 'franjas';
    protected $campos = 'id, nombre, hora_inicio, hora_fin';
    protected $campos_insert = "nombre, hora_inicio, hora_fin";
    protected $fk = 'id_franja';

    protected function comprobarNombre($nombre){
        if ($nombre) {
            $nombres = $this->obtenerNombres();
            return in_array($nombre, $nombres);
        }
        return false;
    }

    protected function comprobarFranja($hora_i, $hora_f){
        $horas = $this->obtenerFranjas();
        foreach ($horas as $franja) {
            if (
                isset($franja['hora_inicio'], $franja['hora_fin']) &&
                $franja['hora_inicio'] === $hora_i &&
                $franja['hora_fin'] === $hora_f
            ) {
                return true;
            }
        }
        return false;
    }

    public function agregarFranja($body){

        // Evalúa si el nombre o la franja ya existen para evitar duplicados
        $existe_nombre = $this->comprobarNombre($body['nombre']);
        $existe_franja = $this->comprobarFranja($body['hora_inicio'], $body['hora_fin']);
        if ($existe_nombre && $existe_franja) {
            return 'ambos';
        } elseif ($existe_nombre) {
            return 'nombre';
        } elseif ($existe_franja) {
            return 'franja';
        }

        $sql = "INSERT INTO {$this->tabla} ({$this->campos_insert}) VALUES (?,?,?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$body['nombre'],$body['hora_inicio'],$body['hora_fin']]);
    }

    public function actualizarFranja($body){

        // Comprobar si existe la franja actual
        $franjas = $this->obtenerPorId($body['id']);
        $ids_franjas = array_column($franjas, 'id');
        $existe = in_array($body['id'], $ids_franjas);

        // Comprobar si el nombre ya existe en otra franja
        $franja_actual = $this->obtenerPorId($body['id']);
        $nombre_actual = $franja_actual[0]['nombre'] ?? null;
        $stmt = $this->db->prepare("SELECT id FROM {$this->tabla} WHERE nombre = ? AND id != ?");
        $stmt->execute([$body['nombre'], $body['id']]);
        if ($stmt->fetch()) {
            return 'nombre';
        }

        // Comprobar si ya existe una franja con el mismo inicio y fin (en otra franja)
        $stmt = $this->db->prepare("SELECT id FROM {$this->tabla} WHERE hora_inicio = ? AND hora_fin = ? AND id != ?");
        $stmt->execute([$body['hora_inicio'], $body['hora_fin'], $body['id']]);
        if ($stmt->fetch()) {
            return 'franja'; 
        }

        if ($existe === true){
            $sql = "UPDATE {$this->tabla} SET nombre = ?, hora_inicio = ?, hora_fin = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$body['nombre'],$body['hora_inicio'],$body['hora_fin'],$body['id']]);
        } else {
            return 'no_existe';
        }
    }

    /**
     * Elimina una franja horaria por su ID.
     *
     * Si la franja no tiene reservas asociadas, la elimina directamente.
     * Si tiene reservas, primero elimina las reservas y luego la franja.
     */
    public function borrarFranja($id){
        $reservas = $this->obtenerPorID_Reservas($id);
        if (empty($reservas)){
            $sql = "DELETE FROM {$this->tabla} WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$id]);
        }
        else {
            $borrado_reservas = $this-> borrarReservasPoId($id);
            if ($borrado_reservas === true){
                $sql = "DELETE FROM {$this->tabla} WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                return $stmt->execute([$id]);
            }
        }
    }
}
