<?php

include_once __DIR__ . '/BaseService.php';

use \App\Services\BaseService; 

/**
 * Servicio para gestionar reservas en el sistema.
 *
 * Hereda de BaseService y proporciona métodos para validar referencias,
 * comprobar solapamientos de franjas y disponibilidad de aulas, y realizar
 * operaciones CRUD sobre el recurso 'reservas'.
 */
class ReservaService extends BaseService {
        
    protected $tabla = 'reservas';
    protected $campos = 'id, fecha, id_profesor, id_aula, id_franja';
    protected $campos_insert = 'fecha, id_profesor, id_aula, id_franja';
    protected $fk = 'id_profesor';

    /**
     * Comprueba que los IDs de aula, profesor y franja existen en la base de datos.
     */
    protected function comprobarReferencias($body) {
        $aulaService = new AulaService();
        $id_aula = $body['id_aula'] ?? null;
        if (!$aulaService->comprobarId($id_aula)) {
            return 'aula';
        }
        $profService = new ProfesorService();
        $id_profesor = $body['id_profesor'] ?? null;
        if (!$profService->comprobarId($id_profesor)) {
            return 'profesor';
        }
        $franjaService = new FranjaService();
        $id_franja = $body['id_franja'] ?? null;
        if (!$franjaService->comprobarId($id_franja)) {
            return 'franja';
        }
        return true;
    }

    /**
     * Comprueba si el profesor tiene reservas solapadas en la misma fecha y franja horaria.
     */
    protected function comprobarHoraProfesor($body, $id_reserva_actual = null){
        $reservas = $this->obtenerPorID_Reservas($body['id_profesor']);
        $franjaService = new FranjaService($this->db);
        $franja_solicitada = $franjaService->obtenerPorID($body['id_franja']);
        $inicio_solicitado = $franja_solicitada[0]['hora_inicio'] ?? null;
        $fin_solicitado = $franja_solicitada[0]['hora_fin'] ?? null;
        foreach ($reservas as $reserva) {
            if ($id_reserva_actual && $reserva['id'] == $id_reserva_actual) continue;
            if ($reserva['fecha'] === $body['fecha']) {
                $franja_reserva = $franjaService->obtenerPorID($reserva['id_franja']);
                $inicio_reserva = $franja_reserva[0]['hora_inicio'] ?? null;
                $fin_reserva = $franja_reserva[0]['hora_fin'] ?? null;
                if ($inicio_solicitado < $fin_reserva && $inicio_reserva < $fin_solicitado) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Comprueba si el aula está disponible para la franja y fecha solicitadas.
     */
    protected function comprobarDisponibilidad($body, $id_reserva_actual = null){
        $id_aula = $body['id_aula'];
        $reservas = $this->obtenerReservasPorAula($id_aula);
        $franjaService = new FranjaService($this->db);
        $franja_solicitada = $franjaService->obtenerPorID($body['id_franja']);
        $inicio_solicitado = $franja_solicitada[0]['hora_inicio'] ?? null;
        $fin_solicitado = $franja_solicitada[0]['hora_fin'] ?? null;
        foreach ($reservas as $reserva) {
            if ($id_reserva_actual && $reserva['id'] == $id_reserva_actual) continue;
            if ($reserva['fecha'] === $body['fecha']) {
                $franja_reserva = $franjaService->obtenerPorID($reserva['id_franja']);
                $inicio_reserva = $franja_reserva[0]['hora_inicio'] ?? null;
                $fin_reserva = $franja_reserva[0]['hora_fin'] ?? null;
                if ($inicio_solicitado < $fin_reserva && $inicio_reserva < $fin_solicitado) {
                    return false;
                }
            }
        }
        return true;
    }

    public function agregarReserva($body){

        // Comprobar referencias
        $referencias = $this->comprobarReferencias($body);
        if ($referencias !== true) {
            return $referencias . '_inexistente';
        }
        $franja_disponible = $this->comprobarHoraProfesor($body);
        $aula_disponible = $this->comprobarDisponibilidad($body);
        if (!$aula_disponible && !$franja_disponible) {
            return 'ambos';
        } elseif (!$aula_disponible) {
            return 'aula';
        } elseif (!$franja_disponible) {
            return 'franja';
        }
        
        // Si todo está disponible, insertar
        $sql = "INSERT INTO {$this->tabla} ({$this->campos_insert}) VALUES (?,?,?,?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$body['fecha'],$body['id_profesor'],$body['id_aula'], $body['id_franja']]);
    }

    public function actualizarReserva($body) {
        $id_reserva = $body['id'] ?? null;
        if (!$id_reserva) {
            return 'no_existe';
        }

        // Comprobar referencias
        $referencias = $this->comprobarReferencias($body);
        if ($referencias !== true) {
            return $referencias .'_inexistente';
        }
        $franja_disponible = $this->comprobarHoraProfesor($body, $id_reserva);
        $aula_disponible = $this->comprobarDisponibilidad($body, $id_reserva);
        if (!$aula_disponible && !$franja_disponible) {
            return 'ambos';
        } elseif (!$aula_disponible) {
            return 'aula';
        } elseif (!$franja_disponible) {
            return 'franja';
        }

        // Si todo está disponible, actualizar
        $sql = "UPDATE {$this->tabla} SET fecha = ?, id_profesor = ?, id_aula = ?, id_franja = ? WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$body['fecha'],$body['id_profesor'],$body['id_aula'], $body['id_franja'], $id_reserva]);
    }
}