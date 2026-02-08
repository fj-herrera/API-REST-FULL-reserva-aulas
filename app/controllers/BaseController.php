<?php

namespace App\Controllers;

include_once __DIR__ . '/../../config/validaciones.php';

use Config\utilities\Codes;
use Config\utilities\ErrMsgs;
use Config\utilities\OkMsgs;

/**
 * Clase base para los controladores de la API.
 *
 * Proporciona métodos genéricos para manejar operaciones CRUD (GET, POST, PUT, DELETE)
 * sobre los recursos principales del sistema (aulas, profesores, franjas, reservas).
 * Centraliza la validación de datos, la gestión de respuestas y la lógica común
 * para todos los controladores específicos.
 */
class BaseController {

    protected function Get($servicio, $peticion) {
        $recurso = $peticion->getRecurso();
        $id = $peticion->getID();
        $sub_recurso = $peticion->getSubRecurso();
        $body = $peticion->getBody();
        // /api/recurso
        if ($recurso && !$id && !$sub_recurso) {
            $data = $servicio->obtenerTodos();
            $this->validarRespuesta($data);
        }
        // /api/recurso/id 
        else if ($recurso && $id && !$sub_recurso) {
            $data = $servicio->obtenerPorId($id);
            $this->validarRespuesta($data);
        }
        // /api/recurso/sub_recurso +body
        else if ($recurso && $sub_recurso === 'disponibles' && !$id) {
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
        // /api/recurso/id/sub_recurso
        else if ($recurso && $id && $sub_recurso === 'reservas') {
            $data = $servicio->obtenerPorId_Reservas($id, $sub_recurso);
            $this->validarRespuesta($data);
        }
    }

    protected function Post($servicio, $peticion){
        $recurso = $peticion->getRecurso();
        $body = $peticion->getBody();

        if ($recurso === 'aulas') {
            $nombre_valido = validarNombreAula($body['nombre']);
            if ($nombre_valido){
                $data = $servicio->agregarAula($body);
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

        if ($recurso === 'profesores') {
            $nombre_valido = validarNombre($body['nombre']);
            $email_valido = validarEmail($body['email']);
            $body['id'] = $peticion->getId();
            if ($nombre_valido && $email_valido){

                $data = $servicio->agregarProfesor($body);

                if ($data === true) {
                    $this->responder(OkMsgs::PROFESOR_OK, null, Codes::CREATED);
                } else if ($data === 'nombre'){
                    $this->responder(ErrMsgs::NOMBRE_PROFESOR_EXISTE, null, Codes::BAD_REQUEST);
                } else if ($data === 'email'){
                    $this->responder(ErrMsgs::EMAIL_PROFESOR_EXISTE, null, Codes::BAD_REQUEST);
                } else if ($data === 'ambos'){
                    $this->responder(ErrMsgs::PROFESOR_EXISTE, null, Codes::BAD_REQUEST); 
                }
            }
            else {
                if (!$nombre_valido && !$email_valido){
                    $this->responder(ErrMsgs::PROFESOR_EXISTE, null, Codes::CONFLICT);
                } else if (!$nombre_valido && $email_valido){
                    $this->responder(ErrMsgs::NOMBRE_PROFESOR, null, Codes::BAD_REQUEST);
                } else if ($nombre_valido && !$email_valido){
                    $this->responder(ErrMsgs::EMAIL_PROFESOR, null, Codes::BAD_REQUEST);
                }
            }
        }

        if ($recurso === 'franjas') {
            $nombre_valido = validarNombreFranja($body['nombre']);
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

        if ($recurso === 'reservas') {

            // Validar formato fecha y dia no pasado
            $formato_ok = validarFecha($body['fecha']);
            $no_es_pasado = comprobarFecha($body['fecha']); 

            // Validación: un profesor solo puede reservar para sí mismo
            if ($body['rol_user'] === 'profesor' && $body['id_user'] != $body['id_profesor']) {
                $this->responder('No está permitido reservar para otro profesor.', null, Codes::FORBIDDEN);
                return;
            }

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
                } else if ($data === 'aula_inexistente') {
                    $this->responder(ErrMsgs::ID_AULA, null, Codes::NOT_FOUND);
                } else if ($data === 'profesor_inexistente') {
                    $this->responder(ErrMsgs::ID_PROFESOR, null, Codes::NOT_FOUND);
                } else if ($data === 'franja_inexistente') {
                    $this->responder(ErrMsgs::ID_FRANJA, null, Codes::NOT_FOUND);
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

        $recurso = $peticion->getRecurso();
        $body = $peticion->getBody();

        if ($recurso === 'aulas') {
            $nombre_valido = validarNombreAula($body['nombre']);
            $body['id'] = $peticion->getId();
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

        if ($recurso === 'profesores') {
            $nombre_valido = validarNombre($body['nombre']);
            $email_valido = validarEmail($body['email']);
            $body['id'] = $peticion->getId();

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
                    $this->responder(ErrMsgs::PROFESOR_EXISTE, null, Codes::CONFLICT);
                } else if (!$nombre_valido && $email_valido){
                    $this->responder(ErrMsgs::NOMBRE_PROFESOR, null, Codes::BAD_REQUEST);
                } else if ($nombre_valido && !$email_valido){
                    $this->responder(ErrMsgs::EMAIL_PROFESOR, null, Codes::BAD_REQUEST);
                }
            }
        } 

        if ($recurso === 'franjas') {
            $nombre_valido = validarNombreFranja($body['nombre']);
            $hora_i_valida = validarHora($body['hora_inicio']);
            $hora_f_valida = validarHora($body['hora_fin']);
            $franja_valida = validarFranja($body['hora_inicio'], $body['hora_fin']);

            $body['id'] = $peticion->getId();
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
                } else if (!$hora_i_valida){
                    $this->responder(ErrMsgs::HORA_I_FRANJA, null, Codes::BAD_REQUEST);
                } else if (!$hora_f_valida){
                    $this->responder(ErrMsgs::HORA_F_FRANJA, null, Codes::BAD_REQUEST);
                } else if (!$franja_valida){
                    $this->responder(ErrMsgs::FRANJA_INVALIDA, null, Codes::BAD_REQUEST);
                }
            }
        }

        if ($recurso === 'reservas') {

            // 1 Obtener la reserva actual para comparar propietario
            $id_reserva = $peticion->getId();
            $reserva_actual = $servicio->obtenerPorID($id_reserva);
            if (empty($reserva_actual)) {
                $this->responder(ErrMsgs::NOT_FOUND, null, Codes::NOT_FOUND);
                return;
            }

            // 2 Validar campo obligatorio id_profesor antes de comprobar propiedad
            if (!isset($body['id_profesor'])) {
                $this->responder(ErrMsgs::FALTA_ID_PROFESOR_RESERVA, null, Codes::BAD_REQUEST);
                return;
            }
            $id_propietario = $reserva_actual[0]['id_profesor'] ?? null;
            // 3 Validación: un profesor solo puede modificar sus propias reservas
            if ($body['rol_user'] === 'profesor' && $body['id_user'] != $id_propietario) {
                $this->responder(ErrMsgs::RESERVA_AJENA, null, Codes::FORBIDDEN);
                return;
            }
            // 4 Impedir traspaso de reserva a otro profesor (solo si el usuario es el propietario)
            if ($body['id_user'] == $id_propietario && $body['id_profesor'] != $id_propietario) {
                $this->responder(ErrMsgs::TRASPASO_AJENO, null, Codes::FORBIDDEN);
                return;
            }
            // 5 Validar campos obligatorios uno a uno
            if (!isset($body['fecha'])) {
                $this->responder(ErrMsgs::FALTA_FECHA_RESERVA, null, Codes::BAD_REQUEST);
                return;
            }
            if (!isset($body['id_aula'])) {
                $this->responder(ErrMsgs::FALTA_ID_AULA_RESERVA, null, Codes::BAD_REQUEST);
                return;
            }
            if (!isset($body['id_franja'])) {
                $this->responder(ErrMsgs::FALTA_ID_FRANJA_RESERVA, null, Codes::BAD_REQUEST);
                return;
            }
            // 6 Validar formato fecha, dia no pasado, y disponibilidad franja / aula / fecha
            $formato_ok = validarFecha($body['fecha']);
            $no_es_pasado = comprobarFecha($body['fecha']);
            $body['id'] = $peticion->getId(); 
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
                } else if ($data === 'aula_inexistente') {
                    $this->responder(ErrMsgs::ID_AULA, null, Codes::NOT_FOUND);
                } else if ($data === 'profesor_inexistente') {
                    $this->responder(ErrMsgs::ID_PROFESOR, null, Codes::NOT_FOUND);
                } else if ($data === 'franja_inexistente') {
                    $this->responder(ErrMsgs::ID_FRANJA, null, Codes::NOT_FOUND);
                } else if ($data === 'no_existe') {
                    $this->responder(ErrMsgs::NOT_FOUND, null, Codes::NOT_FOUND);
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

    protected function Delete($servicio, $peticion){
        $id = $peticion->getIdBorrar();
        $recurso = $peticion->getRecurso();

        if ($recurso === 'aulas') {
            $aula_existe = $servicio->comprobarID($id);
            if ($aula_existe) {
                $data = $servicio->borrarAula($id);
                if ($data === true){
                    $this->responder(OkMsgs::AULA_DELETE, null, Codes::NO_CONTENT);
                } else if ($data === 'reservas'){
                    $this->responder(ErrMsgs::AULA_RESERVAS, null, Codes::CONFLICT);
                }
            } 
            else {
                $this->responder(ErrMsgs::NOT_FOUND, null, Codes::NOT_FOUND);    
            }
        }

        if ($recurso === 'profesores') {
            $profe_existe = $servicio->comprobarID($id);
            if ($profe_existe) {
                $data = $servicio->borrarProfesor($id);
                if ($data === true){
                    $this->responder(null, null, Codes::NO_CONTENT);
                }
            } 
            else {
                $this->responder(ErrMsgs::NOT_FOUND, null, Codes::NOT_FOUND);    
            }
        }

        if ($recurso === 'franjas') {
            $franja_existe = $servicio->comprobarID($id);
            if ($franja_existe) {
                $data = $servicio->borrarFranja($id);
                if ($data === true){
                    $this->responder(null, null, Codes::NO_CONTENT);
                }
            } 
            else {
                $this->responder(ErrMsgs::NOT_FOUND, null, Codes::NOT_FOUND);    
            }
        }

        if ($recurso === 'reservas') {
            $reserva_existe = $servicio->comprobarID($id);
            $rol = $peticion->getRol();
            $id_profesor = $peticion->getIdUser();
            if ($reserva_existe) {
                // admin
                if ($rol === 'admin'){
                    $data = $servicio->borrarReserva($id);
                }
                // profesor
                else if ($rol === 'profesor' ){
                    // Validar que id_user está presente
                    if (!$id_profesor) {
                        $this->responder('Falta el campo obligatorio: id_user', null, Codes::BAD_REQUEST);
                        return;
                    }
                    // Preguntar por el propietario de la reserva
                    $reserva = $servicio->obtenerPorID($id);
                    $id_propietario = $reserva[0]['id_profesor'] ?? null;
                    $es_propietario = ($id_profesor === $id_propietario);
                    if ($es_propietario){
                        $data = $servicio->borrarReserva($id);
                    }
                    else {
                        $this->responder(ErrMsgs::PERMISOS, null, Codes::FORBIDDEN);
                        $data = false; 
                    }
                }
                if ($data === true){
                    $this->responder(null, null, Codes::NO_CONTENT);
                }
            } 
            else {
                $this->responder(ErrMsgs::NOT_FOUND, null, Codes::NOT_FOUND);    
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