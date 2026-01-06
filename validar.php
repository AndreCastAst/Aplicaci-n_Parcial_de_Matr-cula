<?php
// Archivo: validar.php
session_start();
include 'conexion.php'; // Incluimos tu conexión

// Recibimos los datos del formulario
$usuario_input = $_POST['user'];
$password_input = $_POST['pass'];

// Consulta SQL segura (Sin la ñ)
$sql = "SELECT * FROM usuario WHERE usuario = ? AND contrasena = ?";

// Preparamos y ejecutamos
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $usuario_input, $password_input);
$stmt->execute();
$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    // --- LOGIN CORRECTO ---
    $_SESSION['id_usuario'] = $fila['id_usuario'];
    $_SESSION['usuario'] = $fila['usuario'];
    $_SESSION['rol'] = $fila['rol']; 

    // Redirigimos a tu página principal (ahora será .php)
    header("Location: index.php"); 
    exit();
} else {
    // --- LOGIN FALLIDO ---
    header("Location: login.php?error=1");
    exit();
}

$stmt->close();
$conn->close();
?>