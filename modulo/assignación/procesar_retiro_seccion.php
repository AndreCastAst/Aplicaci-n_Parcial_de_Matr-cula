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
    echo "<a href='../../login.php'>Volver</a>";
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

    $conn->begin_transaction();

    try {
        // 1. Quitar al estudiante de la sección (id_seccion = NULL)
        $sql_update_student = "UPDATE alumno SET id_seccion = NULL WHERE id_alumno = ?";
        $stmt_student = $conn->prepare($sql_update_student);
        $stmt_student->bind_param("s", $id_alumno);
        $stmt_student->execute();

        // 2. Aumentar vacante en la sección liberada
        $sql_inc_sec = "UPDATE seccion SET vacantes = vacantes + 1 WHERE id_seccion = ?";
        $stmt_inc = $conn->prepare($sql_inc_sec);
        $stmt_inc->bind_param("s", $id_seccion);
        $stmt_inc->execute();

        // 3. Aumentar vacante global en el grado
        // Primero obtenemos el grado de esa sección
        $sql_get_grado = "SELECT id_grado FROM seccion WHERE id_seccion = ?";
        $stmt_grado = $conn->prepare($sql_get_grado);
        $stmt_grado->bind_param("s", $id_seccion);
        $stmt_grado->execute();
        $res = $stmt_grado->get_result();
        $row = $res->fetch_assoc();
        
        if ($row) {
            $id_grado = $row['id_grado'];
            $sql_inc_grado = "UPDATE grado SET vacantes_disponibles = vacantes_disponibles + 1 WHERE id_grado = ?";
            $stmt_grado_inc = $conn->prepare($sql_inc_grado);
            $stmt_grado_inc->bind_param("s", $id_grado);
            $stmt_grado_inc->execute();
        }

        $conn->commit();
        $message = "Alumno retirado de la sección exitosamente. Vacantes restauradas.";
        $status = "success";

    } catch (Exception $e) {
        $conn->rollback();
        $message = "Error al retirar alumno: " . $e->getMessage();
        $status = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado Retiro</title>
    <link rel="stylesheet" href="../../assets/css/styles.css">
</head>
<body>
    <div class="result-box">
        <?php if ($status == 'success'): ?>
            <div class="success">✅ Eliminación Exitosa</div>
            <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
            <p style="font-size: 0.9em; color: gray;">El alumno ahora está disponible para una nueva asignación.</p>
        <?php else: ?>
            <div class="error">❌ Error</div>
            <p><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
        
        <br>
        <div style="display: flex; flex-direction: column; gap: 10px; align-items: center;">
            <a href="registrar_alumno_seccion.php" class="btn btn-blue" style="font-size: 0.9em; width: 100%;">Ir a Registrar Alumno</a>
            <a href="modificar_seccion.php" class="btn btn-gray" style="font-size: 0.9em; width: 100%;">Buscar otro alumno</a>
        </div>
    </div>
</body>
</html>
