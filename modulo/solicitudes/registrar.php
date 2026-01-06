<?php
session_start();

// Verificar si está logueado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../login.php");
    exit();
}

// Verificar si es secretario (quien puede registrar solicitudes)
if ($_SESSION['rol'] != 'secretario') {
    echo "<h1>Acceso Denegado</h1><p>No tienes permiso para realizar esta acción.</p>";
    echo "<a href='../../index.php'>Volver</a>";
    exit();
}

include '../../conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. RECIBIR Y VALIDAR DATOS DEL FORMULARIO
    // Coincidiendo con los 'name' del HTML actualizado por el usuario

    // Apoderado - Validar y sanitizar
    $apo_nombres = isset($_POST['apo_nombres']) ? trim($_POST['apo_nombres']) : '';
    $apo_ape_paterno = isset($_POST['apo_ape_paterno']) ? trim($_POST['apo_ape_paterno']) : '';
    $apo_ape_materno = isset($_POST['apo_ape_materno']) ? trim($_POST['apo_ape_materno']) : '';
    $apo_dni = isset($_POST['apo_dni']) ? trim($_POST['apo_dni']) : ''; 
    $apo_telefono = isset($_POST['apo_telefono']) ? trim($_POST['apo_telefono']) : '';
    $apo_email = isset($_POST['apo_email']) ? filter_var(trim($_POST['apo_email']), FILTER_VALIDATE_EMAIL) : '';

    // Estudiante - Validar y sanitizar
    $est_nombres = isset($_POST['est_nombres']) ? trim($_POST['est_nombres']) : '';
    $est_ape_paterno = isset($_POST['est_ape_paterno']) ? trim($_POST['est_ape_paterno']) : '';
    $est_ape_materno = isset($_POST['est_ape_materno']) ? trim($_POST['est_ape_materno']) : '';
    $est_dni = isset($_POST['est_dni']) ? trim($_POST['est_dni']) : ''; 
    $est_fecha_nac = isset($_POST['est_fecha_nac']) ? trim($_POST['est_fecha_nac']) : ''; 
    $est_grado = isset($_POST['est_grado']) ? intval($_POST['est_grado']) : 0;

    // Validar que todos los campos requeridos estén presentes
    if (empty($apo_nombres) || empty($apo_ape_paterno) || empty($apo_ape_materno) || 
        empty($apo_dni) || empty($apo_telefono) || !$apo_email ||
        empty($est_nombres) || empty($est_ape_paterno) || empty($est_ape_materno) || 
        empty($est_dni) || empty($est_fecha_nac) || $est_grado < 1 || $est_grado > 6) {
        die("Error: Faltan datos requeridos o datos inválidos.");
    }

    // Validar formato de DNI (8 dígitos)
    if (!preg_match('/^[0-9]{8}$/', $apo_dni) || !preg_match('/^[0-9]{8}$/', $est_dni)) {
        die("Error: El DNI debe tener 8 dígitos.");
    }


    // 2. DATOS AUTOMÁTICOS
    $id_solicitud = substr(uniqid(), 0, 10); 
    $fecha_solicitud = date("Y-m-d");
    $tipo_solicitud = "Matricula";
    $estado_solicitud = "Pendiente";
    $estado_alumno = "Postulante"; 

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // --- PASO 1: Insertar Apoderado ---
        $sql_apo = "INSERT INTO apoderado (id_apoderado, nombre, apellido_paterno, apellido_materno, telefono, email) 
                    VALUES (?, ?, ?, ?, ?, ?) 
                    ON DUPLICATE KEY UPDATE nombre=nombre";
        
        $stmt_apo = $conn->prepare($sql_apo);
        $stmt_apo->bind_param("ssssss", $apo_dni, $apo_nombres, $apo_ape_paterno, $apo_ape_materno, $apo_telefono, $apo_email);
        $stmt_apo->execute();


        // --- PASO 2: Insertar Alumno ---
        $sql_alu = "INSERT INTO alumno (id_alumno, nombre, apellido_paterno, apellido_materno, fecha_nacimiento, estado, id_apoderado, id_seccion) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, NULL)";
        
        $stmt_alu = $conn->prepare($sql_alu);
        $stmt_alu->bind_param("sssssss", $est_dni, $est_nombres, $est_ape_paterno, $est_ape_materno, $est_fecha_nac, $estado_alumno, $apo_dni);
        $stmt_alu->execute();


        // --- PASO 3: Insertar Solicitud ---
        $sql_sol = "INSERT INTO solicitud (id_solicitud, fecha_solicitud, tipo_solicitud, estado, id_apoderado, id_alumno, grado) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt_sol = $conn->prepare($sql_sol);
        $stmt_sol->bind_param("sssssss", $id_solicitud, $fecha_solicitud, $tipo_solicitud, $estado_solicitud, $apo_dni, $est_dni, $est_grado);
        $stmt_sol->execute();


        // Confirmar cambios
        $conn->commit();
        
        $id_solicitud_escaped = htmlspecialchars($id_solicitud, ENT_QUOTES, 'UTF-8');
        $est_grado_escaped = htmlspecialchars($est_grado, ENT_QUOTES, 'UTF-8');
        echo "<script>
                alert('¡Registro exitoso!\\nCódigo de solicitud: $id_solicitud_escaped\\nGrado solicitado: $est_grado_escaped');
                window.location.href = '../../index.php'; 
              </script>";

    } catch (Exception $e) {
        $conn->rollback(); 
        $error_msg = htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        echo "Error al registrar: " . $error_msg;
    }

    if(isset($stmt_apo)) $stmt_apo->close();
    if(isset($stmt_alu)) $stmt_alu->close();
    if(isset($stmt_sol)) $stmt_sol->close();
    $conn->close();
}
?>