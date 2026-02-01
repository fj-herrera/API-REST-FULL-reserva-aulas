<?php
namespace App\Controllers;
// use Config\utilities\ValidValues;
// use Config\utilities\ValidEndpoints;
use Config\utilities\Codes;
use Config\utilities\ErrMsgs;
use Config\utilities\OkMsgs;
include_once __DIR__ . '/../../config/validaciones.php';

class BaseController {

    protected function Get($servicio, $peticion) {
        
        $partes = count($peticion->getEndpoint());
        $id = $peticion->getID();
        $recurso_sec = $peticion->getRecursoSec();
        $body = $peticion->getBody();

        // /api/xxxx 
        if ($partes === 2) {
            $data = $servicio->obtenerTodos();
            $this->validarRespuesta($data);
        }

        // /api/xxxx/1 
        elseif ($partes === 3 && is_numeric($id)) {
            $data = $servicio->obtenerPorId($id);
            $this->validarRespuesta($data);
        }

        // /api/xxxx/xxxx 
        elseif ($partes === 3  && $id ==='reservas' ) {
            $data = $servicio->obtenerPorId($id);
            $this->validarRespuesta($data);
        }

        // /api/xxxx/xxxx/ +body (aulas disponibles)
        elseif ($partes === 3 && $id === 'disponibles' ) {
            $fecha = $body['fecha'];
            $franja = $body['id_franja'];
            $data = null;
            if ($fecha) {
                $no_es_pasado = comprobarFecha($fecha);
                if (!$no_es_pasado) {
                    $this->responder(ErrMsgs::FECHA_PASADA, null, Codes::BAD_REQUEST);
                }
            }
            if ($no_es_pasado) {
                $data = $servicio->obtenerAulasDisponibles($fecha, $franja);
            }
            $this->validarRespuesta($data);
        }

        // /api/xxxx/1/reservas 
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
       // $reserva = $this->extraerDatosReserva($body);
        
        if ($partes === 2 && $recurso === 'aulas') {
            $data = $servicio->agregarAula($body);
            $nombre_valido = validarNombreAula($body['nombre']);
            if ($nombre_valido){
                if ($data === false) {
                    $this->responder(ErrMsgs::AULA_EXISTE, null, Codes::CONFLICT);
                } else {
                    $this->responder(OkMsgs::AULA_OK, null, Codes::CREATED) ;
                }
            }
            else {
                $this->responder(ErrMsgs::NOMBRE_AULA, null, Codes::BAD_REQUEST);
            }
        }    
        
        if ($partes === 2 && $recurso === 'profesores') {
            $nombre_valido = validarNombre($body['nombre']);
            $email_valido = validarEmail($body['email']);
            if ($nombre_valido && $email_valido){

                $data = $servicio->agregarProfesor($body);

                if ($data === true) {
                    $this->responder(OkMsgs::PROFESOR_OK, null, Codes::CREATED);
                } elseif ($data === 'nombre'){
                    $this->responder(ErrMsgs::NOMBRE_PROFESOR_EXISTE, null, Codes::BAD_REQUEST);
                } elseif ($data === 'email'){
                    $this->responder(ErrMsgs::EMAIL_PROFESOR_EXISTE, null, Codes::BAD_REQUEST);
                } elseif ($data === 'ambos'){
                    $this->responder(ErrMsgs::PROFESOR_EXISTE, null, Codes::BAD_REQUEST); 
                }
            }
            else {
                if (!$nombre_valido && !$email_valido){
                    // Este case hay que depurarlo
                    // $this->responder(ErrMsgs::PROFESOR_EXISTE, null, Codes::CONFLICT);
                } else if (!$nombre_valido && $email_valido){
                    $this->responder(ErrMsgs::NOMBRE_PROFESOR, null, Codes::BAD_REQUEST);
                } else if ($nombre_valido && !$email_valido){
                    $this->responder(ErrMsgs::EMAIL_PROFESOR, null, Codes::BAD_REQUEST);
                }
            }
        }

        // /api/franjas
        if ($partes === 2 && $recurso === 'franjas') {
            $nombre_valido = validarNombre($body['nombre']);
            $hora_i_valida = validarHora($body['hora_inicio']);
            $hora_f_valida = validarHora($body['hora_fin']);
            $franja_valida = validarFranja($body['hora_inicio'], $body['hora_fin']);
    
            if ($nombre_valido && $hora_i_valida && $hora_f_valida && $franja_valida){

                $data = $servicio->agregarFranja($body);

                if ($data === true){
                    $this->responder(OkMsgs::FRANJA_OK, null, Codes::CREATED);
                } else if ($data === 'nombre'){
                    $this->responder(ErrMsgs::NOMBRE_FRANJA_EXISTE, null, Codes::BAD_REQUEST);
                } else if ($data === 'franja'){
                    $this->responder(ErrMsgs::FRANJA_EXISTE, null, Codes::BAD_REQUEST);
                } else if ($data === 'ambos'){
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
                else if (!$franja_valida){
                    $this->responder(ErrMsgs::FRANJA_INVALIDA, null, Codes::BAD_REQUEST);
                }
            }
        }

        if ($partes === 2 && $recurso === 'reservas') {

            // Validar formato fecha, dia no pasado, y disponibilidad franja / aula / fecha
            $formato_ok = validarFecha($body['fecha']);
            $no_es_pasado = comprobarFecha($body['fecha']); 
            if ($no_es_pasado && $formato_ok) {
                $data = $servicio->agregarReserva($body);
                if ($data === true){
                    $this->responder(OkMsgs::RESERVA_OK, null, Codes::CREATED);
                } else if ($data === 'franja') {
                    $this->responder(ErrMsgs::PROFESOR_FRANJA, null, Codes::BAD_REQUEST);
                } else if ($data === 'aula') {
                    $this->responder(ErrMsgs::AULA_FRANJA, null, Codes::BAD_REQUEST);
                } else if ($data === 'ambos') {
                    $this->responder(ErrMsgs::AULA_FRANJA, null, Codes::BAD_REQUEST);
                }  
            }
            else if (!$no_es_pasado && $formato_ok){
                $this->responder(ErrMsgs::FECHA, null, Codes::BAD_REQUEST);
            }
            else if (!$formato_ok){
                $this->responder(ErrMsgs::FECHA_FORMATO, null, Codes::BAD_REQUEST);
            }
        }
    }

    protected function Put($servicio, $peticion){
        $partes = count($peticion->getEndpoint());
        $recurso = $peticion->getRecurso();
        $body = $peticion->getBody();

        if ($partes === 2 && $recurso === 'aulas') {
            $nombre_valido = validarNombreAula($body['nombre']);
            if ($nombre_valido){
                $data = $servicio->actualizarAula($body);
                if ($data === true){
                    $this->responder(OkMsgs::AULA_UPDATE, null, Codes::OK);
                } else if ($data === 'nombre') {
                    $this->responder(ErrMsgs::AULA_EXISTE, null, Codes::CONFLICT);
                } else if ($data === 'no_existe'){
                    $this->responder(ErrMsgs::NOT_FOUND, null, Codes::NOT_FOUND);    
                }
            } 
            else {
                $this->responder(ErrMsgs::NOMBRE_PROFESOR, null, Codes::BAD_REQUEST);
            }   
        } 

        if ($partes === 2 && $recurso === 'profesores') {
            $nombre_valido = validarNombre($body['nombre']);
            $email_valido = validarEmail($body['email']);

            if ($nombre_valido && $email_valido){
                $data = $servicio->actualizarProfesor($body);
                if ($data === true){
                    $this->responder(OkMsgs::PROFESOR_UPDATE, null, Codes::OK);
                } else if ($data === 'nombre') {
                    $this->responder(ErrMsgs::NOMBRE_PROFESOR_EXISTE, null, Codes::CONFLICT);
                } else if ($data === 'email') {
                    $this->responder(ErrMsgs::EMAIL_PROFESOR_EXISTE, null, Codes::CONFLICT);
                } else if ($data === 'no_existe'){
                    $this->responder(ErrMsgs::NOT_FOUND, null, Codes::NOT_FOUND);    
                }
            }
            else {
                if (!$nombre_valido && !$email_valido){
                    // Este case hay que depurarlo
                    // $this->responder(ErrMsgs::PROFESOR_EXISTE, null, Codes::CONFLICT);
                } else if (!$nombre_valido && $email_valido){
                    $this->responder(ErrMsgs::NOMBRE_PROFESOR, null, Codes::BAD_REQUEST);
                } else if ($nombre_valido && !$email_valido){
                    $this->responder(ErrMsgs::EMAIL_PROFESOR, null, Codes::BAD_REQUEST);
                }
            }
        } 

        if ($partes === 2 && $recurso === 'franjas') {
            $nombre_valido = validarNombreFranja($body['nombre']);
            $hora_i_valida = validarHora($body['hora_inicio']);
            $hora_f_valida = validarHora($body['hora_fin']);
            $franja_valida = validarFranja($body['hora_inicio'], $body['hora_fin']);

            if ($nombre_valido && $hora_i_valida && $hora_f_valida && $franja_valida){
                $data = $servicio->actualizarFranja($body);
                if ($data === true){
                    $this->responder(OkMsgs::FRANJA_UPDATE, null, Codes::OK);
                } else if ($data === 'nombre') {
                    $this->responder(ErrMsgs::NOMBRE_FRANJA_EXISTE, null, Codes::CONFLICT);
                } else if ($data === 'franja') {
                    $this->responder(ErrMsgs::FRANJA_EXISTE, null, Codes::CONFLICT);
                } else if ($data === 'ambos') {
                    $this->responder(ErrMsgs::FRANJA_EXISTE, null, Codes::CONFLICT);
                } else if ($data === 'no_existe'){
                    $this->responder(ErrMsgs::NOT_FOUND, null, Codes::NOT_FOUND);    
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
                else if (!$franja_valida){
                    $this->responder(ErrMsgs::FRANJA_INVALIDA, null, Codes::BAD_REQUEST);
                }
            }
        }

        if ($partes === 2 && $recurso === 'reservas') {

            // Validar formato fecha, dia no pasado, y disponibilidad franja / aula / fecha
            $formato_ok = validarFecha($body['fecha']);
            $no_es_pasado = comprobarFecha($body['fecha']); 
            if ($no_es_pasado && $formato_ok) {
                $data = $servicio->actualizarReserva($body);
                if ($data === true){
                    $this->responder(OkMsgs::RESERVA_UPDATE, null, Codes::CREATED);
                } else if ($data === 'franja') {
                    $this->responder(ErrMsgs::PROFESOR_FRANJA, null, Codes::BAD_REQUEST);
                } else if ($data === 'aula') {
                    $this->responder(ErrMsgs::AULA_FRANJA, null, Codes::BAD_REQUEST);
                } else if ($data === 'ambos') {
                    $this->responder(ErrMsgs::AULA_FRANJA, null, Codes::BAD_REQUEST);
                }  
            }
            else if (!$no_es_pasado && $formato_ok){
                $this->responder(ErrMsgs::FECHA, null, Codes::BAD_REQUEST);
            }
            else if (!$formato_ok){
                $this->responder(ErrMsgs::FECHA_FORMATO, null, Codes::BAD_REQUEST);
            }
        }
    }    

    protected function validarRespuesta($data){
        if (empty($data)) {
                $this->responder(ErrMsgs::NOT_FOUND, null, Codes::NOT_FOUND);
            }
        $this->responder(null, $data, Codes::OK);
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
