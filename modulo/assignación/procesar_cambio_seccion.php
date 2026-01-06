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
    $old_section_id = isset($_POST['old_section_id']) ? trim($_POST['old_section_id']) : '';
    $new_section_id = isset($_POST['new_section_id']) ? trim($_POST['new_section_id']) : '';

    if (empty($id_alumno) || empty($old_section_id) || empty($new_section_id)) {
        die("Error: Faltan datos.");
    }

    $conn->begin_transaction();

    try {
        // 1. Update Student
        $sql_update_student = "UPDATE alumno SET id_seccion = ? WHERE id_alumno = ?";
        $stmt_student = $conn->prepare($sql_update_student);
        $stmt_student->bind_param("ss", $new_section_id, $id_alumno);
        $stmt_student->execute();

        // 2. Increase vacancies in OLD section (Freed up spot)
        // Verificar si la sección tiene un conteo válido de vacantes (para evitar errores si es nulo, aunque el semilla lo garantiza)
        $sql_inc_old = "UPDATE seccion SET vacantes = vacantes + 1 WHERE id_seccion = ?";
        $stmt_inc = $conn->prepare($sql_inc_old);
        $stmt_inc->bind_param("s", $old_section_id);
        $stmt_inc->execute();

        // 3. Disminuir las vacantes en la nueva sección (tomado el lugar)
        $sql_dec_new = "UPDATE seccion SET vacantes = vacantes - 1 WHERE id_seccion = ?";
        $stmt_dec = $conn->prepare($sql_dec_new);
        $stmt_dec->bind_param("s", $new_section_id);
        $stmt_dec->execute();

        $conn->commit();
        $message = "Sección modificada exitosamente.";
        $status = "success";

    } catch (Exception $e) {
        $conn->rollback();
        $message = "Error al modificar sección: " . $e->getMessage();
        $status = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado Modificación</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <div class="result-box">
        <?php if ($status == 'success'): ?>
            <div class="success">✅ <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
            <p>El cambio se ha registrado correctamente.</p>
        <?php else: ?>
            <div class="error">❌ <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>
        <br>
        <a href="modificar_seccion.php" class="btn btn-blue" style="font-size: 0.9em;">Volver a Modificar</a>
        <a href="registrar_alumno_seccion.php" class="btn btn-gray" style="font-size: 0.9em;">Volver a Registro</a>
    </div>
</body>
</html>
