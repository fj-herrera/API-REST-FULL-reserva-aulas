<?php
namespace App\Controllers;
use Config\utilities\ValidValues;
use Config\utilities\ValidEndpoints;
use Config\utilities\Codes;
use Config\utilities\ErrMsgs;
use Config\utilities\OkMsgs;


class BaseController {

    private function extraerDatosReserva($body) {
        return [
            'nombre' => $body['nombre'] ?? null,
            'email' => $body['email'] ?? null,
            'hora_inicio' => $body['hora_inicio'] ?? null,
            'hora_fin' => $body['hora_fin'] ?? null,
            'fecha' => $body['fecha'] ?? null,
            'profesor' => (string)($body['id_profesor'] ?? ''),
            'aula' => $body['id_aula'] ?? null,
            'franja' => $body['id_franja'] ?? null,
        ];
    }

    protected function Get($servicio, $peticion) {
        
        $partes = count($peticion->getEndpoint());
        $id = $peticion->getID();
        $recurso_sec = $peticion->getRecursoSec();
        $body = $peticion->getBody();

        // /api/xxxx -> 2 elementos
        if ($partes === 2) {
            $data = $servicio->obtenerTodos();
            $this->validarRespuesta($data);
        }

        // /api/xxxx/1 -> 3 elementos y 3er elemento es un numero
        elseif ($partes === 3 && is_numeric($id)) {
            $data = $servicio->obtenerPorId($id);
            $this->validarRespuesta($data);
        }

        // /api/xxxx/xxxx -> 3 elementos
        elseif ($partes === 3  && $id ==='reservas' ) {
            $data = $servicio->obtenerPorId($id);
            $this->validarRespuesta($data);
        }

        // /api/xxxx/xxxx?params -> 3 elementos
        elseif ($partes === 3 && $id === 'disponibles' ) {
            $fecha = $body['fecha'];
            $franja = $body['id_franja'];
            if ($fecha) {
                $no_es_pasado = comprobarFecha($fecha);
            }
            if ($no_es_pasado) {
                $data = $servicio->obtenerAulasDisponibles($fecha, $franja);
            }
            $this->validarRespuesta($data);
        }

        // /api/xxxx/1/reservas = 4 elementos y 3er elemento es numero  
        elseif ($partes === 4 && $recurso_sec === 'reservas') {
            $data = $servicio->obtenerPorId_Reservas($id, $recurso_sec);
            $this->validarRespuesta($data);
        }
    }

    protected function Post($servicio, $peticion){
        $partes = count($peticion->getEndpoint());
        $recurso = $peticion->getRecurso();
        $recurso_sec = $peticion->getRecursoSec() ?? null;
        $id = $peticion->getID();
        $body = $peticion->getBody();
        $reserva = $this->extraerDatosReserva($body);
               
        
        // /api/aulas
        if ($partes === 2 && $recurso === 'aulas') {
            $existe = $this->comprobarNombre($servicio, $reserva['nombre']);
            if (!$existe){
                $data = $servicio->agregarAula($body);
                $this->validarRespuesta($data);
            }
            else {
                $this->responder(ErrMsgs::AULA_EXISTE, null, Codes::CONFLICT);
            }
        }

        // /api/profesores
        if ($partes === 2 && $recurso === 'profesores') {
            $nombre_valido = $this->validarNombre($reserva['nombre']);
            $email_valido = $this->validarEmail($reserva['nombre']);
            if ($nombre_valido && $email_valido){
                $existe_nombre = $this->comprobarNombre($servicio, $reserva['nombre']);
                $existe_email = $this->comprobarEmail($servicio, $reserva['email']);
                if (!$existe_nombre && !$existe_email){
                    $data = $servicio->agregarProfesor($body);
                    ($data) 
                        ? $this->responder(OkMsgs::PROFESOR_OK, null, Codes::CREATED) 
                        : null; 
                }
                else if ($existe_nombre && !$existe_email){
                    $this->responder(ErrMsgs::NOMBRE_PROFESOR_EXISTE, null, Codes::BAD_REQUEST);
                }
                else if (!$existe_nombre && $existe_email){
                    $this->responder(ErrMsgs::EMAIL_PROFESOR_EXISTE, null, Codes::BAD_REQUEST);
                }
                else {
                    $this->responder(ErrMsgs::PROFESOR_EXISTE, null, Codes::BAD_REQUEST); 
                }
            }
            else {
                if (!$nombre_valido && !$email_valido){
                    // Este case hay que depurarlo
                    // $this->responder(ErrMsgs::PROFESOR_EXISTE, null, Codes::CONFLICT);
                }
                else if (!$nombre_valido && $email_valido){
                    $this->responder(ErrMsgs::NOMBRE_PROFESOR, null, Codes::BAD_REQUEST);
                }
                else if ($nombre_valido && !$email_valido){
                    $this->responder(ErrMsgs::EMAIL_PROFESOR, null, Codes::BAD_REQUEST);
                }
            }
        }

        // /api/franjas
        if ($partes === 2 && $recurso === 'franjas') {
            $nombre_valido = $this->validarNombre($reserva['nombre']);
            $hora_i_valida = $this->validarHora($reserva['hora_inicio']);
            $hora_f_valida = $this->validarHora($reserva['hora_fin']);
            $franja_valida = $this->validarFranja($reserva['hora_inicio'], $reserva['hora_fin']);
    
            if ($nombre_valido && $hora_i_valida && $hora_f_valida && $franja_valida){
                $existe_nombre = $this->comprobarNombre($servicio, $reserva['nombre']);
                $existe_franja = $this->comprobarFranja($servicio, $reserva['hora_inicio'], $reserva['hora_fin']);

                if (!$existe_nombre && !$existe_franja){
                    $data = $servicio->agregarFranja($body);
                    ($data) 
                        ? $this->responder(OkMsgs::FRANJA_OK, null, Codes::CREATED) 
                        : null; 
                }
                else if ($existe_nombre && !$existe_franja){
                    $this->responder(ErrMsgs::NOMBRE_FRANJA_EXISTE, null, Codes::BAD_REQUEST);
                }
                else if (!$existe_nombre && $existe_franja){
                    $this->responder(ErrMsgs::FRANJA_EXISTE, null, Codes::BAD_REQUEST);
                }
                else {
                    $this->responder(ErrMsgs::FRANJA_EXISTE, null, Codes::BAD_REQUEST); 
                } 
            }
            else {
                if (!$nombre_valido){
                    $this->responder(ErrMsgs::NOMBRE_FRANJA, null, Codes::BAD_REQUEST);
                }
                else if (!$hora_i_valida){
                    $this->responder(ErrMsgs::HORA_I_FRANJA, null, Codes::BAD_REQUEST);
                }
                else if (!$hora_f_valida){
                    $this->responder(ErrMsgs::HORA_F_FRANJA, null, Codes::BAD_REQUEST);
                }
            }
        }

        if ($partes === 2 && $recurso === 'reservas') {

            // Validar formato fecha, dia no pasado, y disponibilidad franja / aula / fecha
            $formato_ok = $this->validarFecha($reserva['fecha']);
            $no_es_pasado = comprobarFecha($reserva['fecha']); 
            $franja_disponible = $this->comprobarHoraProfesor($servicio, $reserva);
            $aula_disponible = $this->comprobarDisponibilidad($servicio, $reserva);

            if ($no_es_pasado) {

                if ($aula_disponible && $franja_disponible) {
                    $data = $servicio->agregarReserva($body);
                    ($data) 
                        ? $this->responder(OkMsgs::RESERVA_OK, null, Codes::CREATED) 
                        : null;  
                } 
                else if ($aula_disponible && !$franja_disponible) {
                    $this->responder(ErrMsgs::PROFESOR_FRANJA, null, Codes::BAD_REQUEST);
                } 
                else if (!$aula_disponible && $franja_disponible) {
                    $this->responder(ErrMsgs::AULA_FRANJA, null, Codes::BAD_REQUEST);
                }  
            }
            else {
                $this->responder(ErrMsgs::FECHA, null, Codes::BAD_REQUEST);
            }
        }
    }

    

    protected function validarNombre($nombre) {
        $isValid = false;
        if( preg_match(ValidValues::NOMBRE, $nombre, $matches)){
            $isValid = true;
        }
        return $isValid;
    }

    protected function validarEmail($email) {
        $isValid = false;
        if( preg_match(ValidValues::EMAIL, $email, $matches)){
            $isValid = true;
        }
        return $isValid;
    }
    protected function validarHora($hora) {
        $isValid = false;
        if( preg_match(ValidValues::HORA, $hora, $matches)){
            $isValid = true;
        }
        return $isValid;
    }

    protected function validarFecha($fecha) {
        return preg_match(ValidValues::FECHA, $fecha, $matches) ? true : false;
    }

    protected function validarRespuesta($data){
        if (empty($data)) {
                $this->responder(ErrMsgs::NOT_FOUND, null, Codes::NOT_FOUND);
            }
        $this->responder(null, $data, Codes::OK);
    }

    protected function validarFranja($hora_i, $hora_f){
    // Comprobacion Franja horaria
        $isOk = true;
        // Si son iguales
        if ($hora_i === $hora_f) {
            $isOk = false;
        }
        // Si el fin es anterior al inicio
        else if ($hora_f < $hora_i) {
            $isOk = false;
        }     
        return $isOk;
    }

    protected function comprobarId($servicio, $id){
    // Comprobacion id
        if ($id){
            $ids = $servicio->obtenerIds();
            return in_array($id, $ids); 
        }
    }
    protected function comprobarNombre($servicio, $nombre){
    // Comprobacion nombres
        if ($nombre) {
            $nombres = $servicio->obtenerNombres();
            // Comprobar si $nombre está en el array de nombres
            return in_array($nombre, $nombres);
        }
    }

    protected function comprobarEmail($servicio, $email){
    // Comprobacion emails
        if ($email) {
            $emails = $servicio->obtenerEmails();
            return in_array($email, $emails);
        }
    }

    protected function comprobarFranja($servicio, $hora_i, $hora_f){
    // Comprobacion franjas
        $horas = $servicio->obtenerFranjas();
        // Si ya esxiste una franja con el mismo inicio y final
        $inicio = (in_array($hora_i, $horas['horas_i'])) ? true : false;
        $fin = (in_array($hora_f, $horas['horas_f'])) ? true : false;
        if ($inicio && $fin){
            return true;
        }
        return false; 
    }

    protected function comprobarHoraProfesor($servicio, $reserva){
    // Comprobacion Disponibilidad hora profesor
        $reservas = $servicio->obtenerPorID_Reservas($reserva['profesor']);
        $ids_franjas = array_column($reservas, 'id_franja');
        // esta la franja dsponible para el profesor ese dia?
        $franja_disponible = (in_array($reserva['franja'], $ids_franjas)) ? true : false ;
        return $franja_disponible;
        
    }

    protected function comprobarDisponibilidad($servicio, $reserva){
        $id_aula = $reserva['aula'];
        $disponibles = $servicio->obtenerAulasDisponibles($reserva['fecha'], $reserva['franja']);
        $ids_aulas = array_column($disponibles, 'id');
        $aula_disponible = (in_array($id_aula, $ids_aulas)) ? true : false ;
        return $aula_disponible;
    }

    protected function responder($message, $data, $code) {
        http_response_code($code);
        $respuesta = [];
        ($message) ? $respuesta['Message'] = $message : null;
        ($data) ? $respuesta = $data : null;
        printJSON($respuesta);
        exit;
    }
}
