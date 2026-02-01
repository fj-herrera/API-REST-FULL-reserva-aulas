<?php
namespace App\Services;

class BaseService {
    protected $db;
    protected $tabla;
    protected $campos; // Para SELECT
    protected $campos_insert; // Para INSERT
    protected $fk;

    public function __construct() {
        $this->db = getDbConnection();
    }

    public function obtenerTodos(){
        $sql = "SELECT {$this->campos} FROM {$this->tabla}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $respuesta = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $respuesta;
    }

    public function comprobarId($id){
        if ($id){
            $ids = $this->obtenerIds();
            return in_array($id, $ids); 
        }
    }

    public function obtenerPorID($id){
        $sql = "SELECT {$this->campos} FROM {$this->tabla} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $respuesta = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $respuesta;
    }

    public function obtenerPorID_Reservas($id){
        // Selecciona las reservas hechas por un profesor
        $campos_reserva = 'id, fecha, id_aula, id_profesor, id_franja';
        $sql = "SELECT {$campos_reserva} FROM reservas WHERE {$this->fk} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $respuesta = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $respuesta;
    }

    // Selecciona las aulas disponibles para una fecha y franja 
    public function obtenerAulasDisponibles($fecha,$franja){
        $sql = "SELECT a.id, a.nombre
                FROM aulas a
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM reservas r
                    WHERE r.id_aula = a.id
                    AND r.id_franja = ?
                    AND r.fecha = ?
                );";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([ $franja, $fecha]);
        $respuesta = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $respuesta;
    }

    public function obtenerIds(){
        $sql = "SELECT id FROM {$this->tabla}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $respuesta = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $ids = array_column($respuesta, 'id');
        return $ids;
    }

    public function obtenerNombres(){
        $sql = "SELECT nombre FROM {$this->tabla}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $respuesta = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $nombres = array_column($respuesta, 'nombre');
        return $nombres;
    }

    public function obtenerEmails(){
        $sql = "SELECT email FROM {$this->tabla}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $respuesta = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $emails = array_column($respuesta, 'email');
        return $emails;
    }

    public function obtenerFranjas(){
        $sql = "SELECT id, hora_inicio, hora_fin FROM {$this->tabla}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $franjas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $franjas;
    }
    // Método auxiliar para obtener reservas por aula
    public function obtenerReservasPorAula($id_aula){
        $campos_reserva = 'id, fecha, id_aula, id_profesor, id_franja';
        $sql = "SELECT {$campos_reserva} FROM reservas WHERE id_aula = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_aula]);
        $respuesta = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $respuesta;
    }

    public function borrarReservasPoId($id) {
        $sql = "DELETE FROM reservas WHERE {$this->fk} = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function borrarReserva($id) {
        $sql = "DELETE FROM reservas WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}