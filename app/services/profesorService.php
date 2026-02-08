<?php

include_once __DIR__ . '/BaseService.php';

use \App\Services\BaseService; 

/**
 * Servicio para gestionar operaciones sobre el recurso 'profesores'.
 *
 * Hereda de BaseService y proporciona métodos para validar duplicidad de nombre y email,
 * agregar, actualizar y borrar profesores, asegurando la integridad de datos y evitando duplicados.
 */
class ProfesorService extends BaseService {
    protected $tabla = 'profesores';
    protected $campos = 'id, nombre, email, rol';
    protected $campos_insert = "nombre, email, rol";
    protected $fk = 'id_profesor';

    protected function comprobarNombre($nombre){
        if ($nombre) {
            $nombres = $this->obtenerNombres();
            return in_array($nombre, $nombres);
        }
        return false;
    }

    protected function comprobarEmail($email){
        if ($email) {
            $emails = $this->obtenerEmails();
            return in_array($email, $emails);
        }
        return false;
    }

    public function agregarProfesor($body){
        $existe_nombre = $this->comprobarNombre($body['nombre']);
        $existe_email = $this->comprobarEmail($body['email']);
        if ($existe_nombre && $existe_email) {
            return 'ambos';
        } elseif ($existe_nombre) {
            return 'nombre';
        } elseif ($existe_email) {
            return 'email';
        }
        $sql = "INSERT INTO {$this->tabla} ({$this->campos_insert}) VALUES (?,?,?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$body['nombre'],$body['email'],$body['rol']]);
    }

    public function actualizarProfesor($body) {
        $profesores = $this->obtenerIds($body['id']);
        $existe = in_array($body['id'], $profesores);

        // Comprobar si el nombre ya existe en otro profesor
        $profesor_actual = $this->obtenerPorID($body['id']);
        $nombre_actual = $profesor_actual[0]['nombre'] ?? null;
        $email_actual = $profesor_actual[0]['email'] ?? null;

        // Buscar nombre duplicado en otros profesores
        $stmt = $this->db->prepare("SELECT id FROM {$this->tabla} WHERE nombre = ? AND id != ?");
        $stmt->execute([$body['nombre'], $body['id']]);
        if ($stmt->fetch()) {
            return 'nombre'; 
        }

        // Buscar email duplicado en otros profesores
        $stmt = $this->db->prepare("SELECT id FROM {$this->tabla} WHERE email = ? AND id != ?");
        $stmt->execute([$body['email'], $body['id']]);
        if ($stmt->fetch()) {
            return 'email';
        }

        // Si el profesor existe, actualiza sus datos.
        if ($existe === true){
            $rol_actual = $profesor_actual[0]['rol'] ?? null;
            $nuevo_rol = ($rol_actual === 'profesor') ? $rol_actual : ($body['rol'] ?? $rol_actual);
            $sql = "UPDATE {$this->tabla} SET nombre = ?, email = ?, rol = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$body['nombre'],$body['email'],$nuevo_rol,$body['id']]);
        } else {
            return 'no_existe';
        }
    }

    public function borrarProfesor($id){
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