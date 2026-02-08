<?php

include_once __DIR__ . '/utilities.php';
$envPath = __DIR__ . '/../.env';

function loadEnvFromFile($envPath) {
    if (file_exists($envPath)) {
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#') || !str_contains($line, '=')) {
                continue;
            }
            [$name, $value] = explode('=', $line, 2);
            putenv(trim($name) . '=' . trim($value));
        }
    }
}

loadEnvFromFile($envPath);
define('API_SECRET_KEY', 'claveSuperSecreta123');
date_default_timezone_set('Europe/Madrid');