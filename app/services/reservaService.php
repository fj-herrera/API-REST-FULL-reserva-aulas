<?php
include_once __DIR__ . '/BaseService.php';

class ReservaService extends \App\Services\BaseService {
    protected $tabla = 'reservas';
    protected $campos = 'id, fecha, id_profesor, id_aula, id_franja';
    protected $campos_insert = 'fecha, id_profesor, id_aula, id_franja';
    protected $fk = 'id_profesor';

    // --- NUEVO: solapamiento por rango horario ---
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