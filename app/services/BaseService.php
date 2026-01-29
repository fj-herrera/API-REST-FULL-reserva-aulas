<?php
namespace App\Services;
include_once __DIR__ . ('/../models/Profesor.php');

class BaseService {
    /*
    protected function instanciarProfesores($respuesta){
        $grupoProfesores = [];
        foreach ($respuesta as $objeto){
            $profesor = new Profesor(
                $objeto['id'],
                $objeto['nombre'],
                $objeto['email'],
                $objeto['rol']
            );
            $grupoProfesores[] = $profesor;
        }
        return $grupoProfesores;
    }
    */
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
        // Devolver objetos Profesores
        //return instanciarrProfesores($respuesta);
    }

    public function obtenerPorID($id){
        $sql = "SELECT {$this->campos} FROM {$this->tabla} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $respuesta = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $respuesta;
    }

    public function obtenerPorID_Reservas($id,$recurso_sec){

        // Selecciona las reservas hechas por un profesor
        $campos_reserva = 'id, fecha, id_aula, id_profesor, id_franja';
        $sql = "SELECT {$campos_reserva} FROM {$recurso_sec} WHERE {$this->fk} = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $respuesta = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $respuesta;
    }

     public function obtenerAulasDisponibles($fecha,$franja){

        // Selecciona las aulas disponibles para una fecha y franja 
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
        $sql = "SELECT hora_inicio, hora_fin FROM {$this->tabla}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $respuesta = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $horas_i = array_column($respuesta, 'hora_inicio');
        $horas_f = array_column($respuesta, 'hora_fin');
        $horas = [
            'horas_i' => $horas_i,
            'horas_f' => $horas_f
        ];
        return $horas;
    }
    
    public function agregarAula($body){
        // Usar $this->camposInsert para evitar incluir 'id' en el insert
        $sql = "INSERT INTO {$this->tabla} ({$this->campos_insert}) VALUES (?,?,?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$body['nombre'],$body['capacidad'],$body['descripcion']]);
    }

    public function agregarProfesor($body){
        
        // Usar $this->camposInsert para evitar incluir 'id' en el insert
        $sql = "INSERT INTO {$this->tabla} ({$this->campos_insert}) VALUES (?,?,?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$body['nombre'],$body['email'],$body['rol']]);
    }

    public function agregarFranja($body){
        
        // Usar $this->camposInsert para evitar incluir 'id' en el insert
        $sql = "INSERT INTO {$this->tabla} ({$this->campos_insert}) VALUES (?,?,?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$body['nombre'],$body['hora_inicio'],$body['hora_fin']]);
    }
}