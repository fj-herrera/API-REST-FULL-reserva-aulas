<?php 

function getDbConnection() {
    static $db = null;

    if ($db === null) {
        $host = 'localhost';
        $dbname = 'reservas';
        $user = 'root';
        $pass = 'root'; // Cambia esto según tu entorno

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";

        try {
            $db = new PDO($dsn, $user, $pass);
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error de conexión a la base de datos']);
            exit;
        }
    }

    return $db;
}

?>