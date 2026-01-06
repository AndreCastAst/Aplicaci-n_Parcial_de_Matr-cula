<?php
// Archivo: logout.php
session_start();
session_destroy(); // Destruye la sesión
header("Location: login.php"); // Manda al usuario al login
exit();
?>