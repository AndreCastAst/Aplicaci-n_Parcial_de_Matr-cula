<?php
session_start();

// 1. Verificar si está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login.php");
    exit();
}

// 2. VERIFICAR SI ES DIRECTOR
if ($_SESSION['rol'] != 'director') {
    // Si NO es director, lo mandamos al inicio y le prohibimos entrar
    echo "<h1>Acceso Denegado</h1><p>No tienes permiso para ver esta página.</p>";
    echo "<a href='../../index.php'>Volver</a>";
    exit(); // Detenemos la carga de la página
}
include '../../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validar y sanitizar datos de entrada
    $id_alumno = isset($_POST['id_alumno']) ? trim($_POST['id_alumno']) : '';
    $id_seccion = isset($_POST['id_seccion']) ? trim($_POST['id_seccion']) : '';

    if (empty($id_alumno) || empty($id_seccion)) {
        die("Error: Faltan datos.");
    }

    // Iniciar transacción para asegurar consistencia
    // TRANSACCIÓN (ACID):
    // Esto es crucial. Si falla la inscripción, NO queremos que se resten las vacantes.
    // O se hace TODO (commit), o no se hace NADA (rollback).
    $conn->begin_transaction();

    try {
        // 1. Asignar sección al alumno
        $sql_update_alumno = "UPDATE alumno SET id_seccion = ? WHERE id_alumno = ?";
        $stmt_alumno = $conn->prepare($sql_update_alumno);
        $stmt_alumno->bind_param("ss", $id_seccion, $id_alumno);
        $stmt_alumno->execute();

        // 2. Obtener el grado de la sección para descontar vacante
        $sql_get_grado = "SELECT id_grado FROM seccion WHERE id_seccion = ?";
        $stmt_get_grado = $conn->prepare($sql_get_grado);
        $stmt_get_grado->bind_param("s", $id_seccion);
        $stmt_get_grado->execute();
        $result_grado = $stmt_get_grado->get_result();
        $row_grado = $result_grado->fetch_assoc();
        $id_grado = $row_grado['id_grado'];

        // 3. Descontar vacante en el grado (Global)
        $sql_update_vacantes_grado = "UPDATE grado SET vacantes_disponibles = vacantes_disponibles - 1 WHERE id_grado = ?";
        $stmt_vacantes_grado = $conn->prepare($sql_update_vacantes_grado);
        $stmt_vacantes_grado->bind_param("s", $id_grado);
        $stmt_vacantes_grado->execute();

        // 4. Descontar vacante en la sección (Específico)
        $sql_update_vacantes_seccion = "UPDATE seccion SET vacantes = vacantes - 1 WHERE id_seccion = ?";
        $stmt_vacantes_seccion = $conn->prepare($sql_update_vacantes_seccion);
        $stmt_vacantes_seccion->bind_param("s", $id_seccion);
        $stmt_vacantes_seccion->execute();

        // Confirmar transacción
        $conn->commit(); // GUARDA LOS CAMBIOS PERMANENTEMENTE

        $message = "Alumno inscrito correctamente en la sección. Vacantes actualizadas.";
        $status = "success";

    } catch (Exception $e) {
        $conn->rollback(); // SI HUBO ERROR, DESHACER TODO (Volver al estado anterior)
        $message = "Error al inscribir: " . $e->getMessage();
        $status = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado Inscripción</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
    <script>
        // Redirigir después de unos segundos si es exitoso
        <?php if ($status == 'success'): ?>
        setTimeout(function() {
            window.location.href = 'registrar_alumno_seccion.php';
        }, 3000);
        <?php endif; ?>
    </script>
</head>
<body>
    <div class="result-box">
        <?php if ($status == 'success'): ?>
            <div class="success">✅ <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
            <p>Redirigiendo en 3 segundos...</p>
        <?php else: ?>
            <div class="error">❌ <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <br>
        <a href="../../index.php" class="btn btn-back-blue">Volver al Inicio</a>
    </div>
</body>
</html>
