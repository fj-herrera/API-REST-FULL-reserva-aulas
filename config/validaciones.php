<?php

use Config\utilities\ValidValues;
use Config\utilities\AdminEndpoints;

function validarAcceso($endpoint, $rol, $clave = null) {
   
    foreach (AdminEndpoints::ADMIN_ONLY as $adminEndpoint) {
        if (strpos($endpoint, $adminEndpoint) === 0) {
            // Solo admin y con clave secreta
            return $rol === 'admin' && $clave === API_SECRET_KEY;
        }
    }
    // Para reservas, admin y user pueden acceder (sin clave)
    if (strpos($endpoint, '/api/reservas') === 0) {
        return $rol === 'admin' || $rol === 'profesor';
    }
    // Por defecto, denegar
    return false;
}

/**
 * Validación personalizada para PUT según rol y recurso
 * Si el rol es admin, permite todo (no requiere id_user).
 * Si el rol es profesor, solo permite actualizar su perfil y sus reservas.
 */
function validarAccesoPUT($endpoint, $rol, $id_user, $body, $clave = null) {
    foreach (AdminEndpoints::ADMIN_ONLY as $adminEndpoint) {
        if (strpos($endpoint, $adminEndpoint) === 0) {
            return $rol === 'admin' && $clave === API_SECRET_KEY;
        }
    }
    if ($rol === 'admin') return true;
    if ($rol === 'profesor') {
        if (strpos($endpoint, '/profesores/') !== false && isset($body['id']) && $body['id'] == $id_user) {
            return true;
        }
        if (strpos($endpoint, '/reservas/') !== false) {
            // Permitir acceso si el usuario es el propietario actual de la reserva
            // El controller se encargará de validar el traspaso
            return true;
        }
        // No puede modificar aulas ni franjas
        return false;
    }
    // Otros roles: denegar
    return false;
}

function validarNombre($nombre) {
    $isValid = false;
    if( preg_match(ValidValues::NOMBRE, $nombre, $matches)){
        $isValid = true;
    }
    return $isValid;
}

function validarNombreAula($nombre) {
    $isValid = false;
    if( preg_match(ValidValues::NOMBRE_AULA, $nombre, $matches)){
        $isValid = true;
    }
    return $isValid;
}

function validarNombreFranja($nombre) {
    $isValid = false;
    if( preg_match(ValidValues::NOMBRE_FRANJA, $nombre, $matches)){
        $isValid = true;
    }
    return $isValid;
}

function validarEmail($email) {
    $isValid = false;
    if( preg_match(ValidValues::EMAIL, $email, $matches)){
        $isValid = true;
    }
    return $isValid;
}

function validarHora($hora) {
    $isValid = false;
    if( preg_match(ValidValues::HORA, $hora, $matches)){
        $isValid = true;
    }
    return $isValid;
}

function validarFecha($fecha) {
    return preg_match(ValidValues::FECHA, $fecha, $matches) ? true : false;
}

function comprobarFecha($fecha){
    $hoy = strtotime(date('Y-m-d'));
    $fecha_ts = strtotime($fecha);
    if ($fecha_ts === false) return false; // Fecha inválida
    return ($fecha_ts >= $hoy);
}

function validarFranja($hora_i, $hora_f){
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