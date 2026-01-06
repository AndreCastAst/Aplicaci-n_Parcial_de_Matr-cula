<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "bdcolegio";

// Crear conexión
// new mysqli(SERVIDOR, USUARIO, CONTRASEÑA, NOMBRE_BD)
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error); // 'die' detiene el script si hay error
}

// Establecer conjunto de caracteres a utf8
$conn->set_charset("utf8");
?>