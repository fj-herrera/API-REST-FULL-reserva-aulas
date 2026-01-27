<?php
namespace App\Controllers;
use Config\utilities\ValidEndpoints;
use Config\utilities\ResponseCodes;
use Config\utilities\ErrMsgs;


class BaseController {

    protected function GET($servicio, $peticion) {

        $partes = count($peticion->get_endpoint());
        $recurso = $peticion->get_recurso();
        $id = $peticion->get_id();
        $parametros = $peticion->get_parametros();
        $recurso_sec = $peticion->get_recurso_sec();

        var_dump($parametros);
        // /api/xxxx -> 2 elementos
        if ($partes === 2) {
            $data = $servicio->obtenerTodos();
            $this->validarRespuesta($data);
        }

        // /api/xxxx/1 -> 3 elementos y 3er elemento es un numero  
        elseif ($partes === 3 && is_int($id)) {
            $data = $servicio->obtenerPorId($id);
            $this->validarRespuesta($data);
        }

        // /api/xxxx/xxxx -> 3 elementos sin parámetros    
        elseif ($partes === 3 && is_string($id) && empty($parametros)) {
            $data = $servicio->obtenerPorId($id);
            $this->validarRespuesta($data);
        }

        // /api/xxxx/xxxx?params -> 3 elementos + parametros    
        elseif ($partes === 3 && !empty($parametros)) {
            
            $fecha = $parametros['date'];
            $franja = $parametros['id_f'];
            if ($fecha) {
               $no_es_pasado = comprobarFecha($parametros['date']); 
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

    public function Post(){

    }

    protected function validarRespuesta($data){
        if (empty($data)) {
                $this->responder(ErrMsgs::NOT_FOUND, null, ResponseCodes::NOT_FOUND);
            }
        $this->responder(null, $data, ResponseCodes::OK);
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
