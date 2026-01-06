<?php
include 'conexion.php';

// Add vacantes column to seccion table if it doesn't exist
$sql_add_column = "ALTER TABLE seccion ADD COLUMN vacantes INT DEFAULT 0 AFTER capacidad";
try {
    if ($conn->query($sql_add_column) === TRUE) {
        echo "Columna 'vacantes' agregada exitosamente.<br>";
        
        // Update vacantes to be equal to capacity initially
        $sql_update_vacantes = "UPDATE seccion SET vacantes = capacidad";
        if ($conn->query($sql_update_vacantes) === TRUE) {
            echo "Vacantes inicializadas correctamente (igual a capacidad).";
        } else {
            echo "Error actualizando vacantes iniciales: " . $conn->error;
        }
    } else {
        // Check if error is because column already exists
        if (strpos($conn->error, "Duplicate column") !== false) {
             echo "La columna 'vacantes' ya existe.";
        } else {
            echo "Error agregando columna: " . $conn->error;
        }
    }
} catch (Exception $e) {
    echo "Excepción: " . $e->getMessage();
}

$conn->close();
?>
