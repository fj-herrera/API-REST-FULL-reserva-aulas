
<?php
include_once __DIR__ . '/BaseService.php';

class ReservaService extends \App\Services\BaseService {
    protected $tabla = 'reservas';
    protected $campos = 'id, fecha, id_profesor, id_aula, id_franja';
    protected $campos_insert = 'fecha, id_profesor, id_aula, id_franja';
    protected $fk = 'id_profesor';

    // --- ORIGINAL ---
    /*
    protected function comprobarHoraProfesor($body){
        $reservas = $this->obtenerPorID_Reservas($body['id_profesor']);
        foreach ($reservas as $reserva) {
            if ($reserva['fecha'] === $body['fecha'] && $reserva['id_franja'] == $body['id_franja']) {

                return false;
            }
        }
        return true;
    }
    */

    // --- NUEVO: solapamiento por rango horario ---
    protected function comprobarHoraProfesor($body){
        $reservas = $this->obtenerPorID_Reservas($body['id_profesor']);
        $franjaService = new FranjaService($this->db);
        $franja_solicitada = $franjaService->obtenerPorID($body['id_franja']);
        $inicio_solicitado = $franja_solicitada[0]['hora_inicio'] ?? null;
        $fin_solicitado = $franja_solicitada[0]['hora_fin'] ?? null;
        foreach ($reservas as $reserva) {
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

    // --- ORIGINAL ---
    /*
    protected function comprobarDisponibilidad($body){
        $id_aula = $body['id_aula'];
        $disponibles = $this->obtenerAulasDisponibles($body['fecha'], $body['id_franja']);
        $ids_aulas = array_column($disponibles, 'id');
        $aula_disponible = (in_array($id_aula, $ids_aulas)) ? true : false ;
        return $aula_disponible;
    }
    */

    // --- NUEVO: solapamiento por rango horario ---
    protected function comprobarDisponibilidad($body){
        $id_aula = $body['id_aula'];
        $reservas = $this->obtenerReservasPorAula($id_aula);
        $franjaService = new FranjaService($this->db);
        $franja_solicitada = $franjaService->obtenerPorID($body['id_franja']);
        $inicio_solicitado = $franja_solicitada[0]['hora_inicio'] ?? null;
        $fin_solicitado = $franja_solicitada[0]['hora_fin'] ?? null;
        foreach ($reservas as $reserva) {
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
}
?>