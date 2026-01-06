<?php
include 'conexion.php';

// Data to seed
$grades = [
    ['id' => '1', 'name' => '1° Grado'],
    ['id' => '2', 'name' => '2° Grado'],
    ['id' => '3', 'name' => '3° Grado'],
    ['id' => '4', 'name' => '4° Grado'],
    ['id' => '5', 'name' => '5° Grado'],
    ['id' => '6', 'name' => '6° Grado'],
];

$sections = ['A', 'B'];
$capacity = 30; // Default capacity

// 1. Seed Grades
foreach ($grades as $grade) {
    // Check if grade exists
    $check = $conn->query("SELECT id_grado FROM grado WHERE id_grado = '{$grade['id']}'");
    if ($check->num_rows == 0) {
        // 2 sections * 30 capacity = 60 vacancies
        $total_vacancies = count($sections) * $capacity;
        $sql = "INSERT INTO grado (id_grado, nombre_grado, vacantes_disponibles) VALUES ('{$grade['id']}', '{$grade['name']}', $total_vacancies)";
        if ($conn->query($sql) === TRUE) {
            echo "Grado {$grade['name']} creado.<br>";
        } else {
            echo "Error creando grado {$grade['name']}: " . $conn->error . "<br>";
        }
    } else {
        echo "Grado {$grade['name']} ya existe.<br>";
    }

    // 2. Seed Sections for this Grade
    foreach ($sections as $sec) {
        $sec_id = $grade['id'] . $sec; // e.g., "1A"
        $sec_name = "Sección " . $sec;
        
        $check_sec = $conn->query("SELECT id_seccion FROM seccion WHERE id_seccion = '$sec_id'");
        if ($check_sec->num_rows == 0) {
            // Check if column 'vacantes' exists (it should from previous step)
            // We assume db_updates_v2.php ran. If not, this might fail or we should handle it.
            // But I will assume the column exists or ignore if the query fails on that specific column.
            
            // Safe insert assuming 'vacantes' column exists
            $sql_sec = "INSERT INTO seccion (id_seccion, nombre_seccion, capacidad, id_grado, vacantes) 
                        VALUES ('$sec_id', '$sec_name', $capacity, '{$grade['id']}', $capacity)";
            
            if ($conn->query($sql_sec) === TRUE) {
                echo "Sección $sec_id creada.<br>";
            } else {
                echo "Error creando sección $sec_id: " . $conn->error . "<br>";
            }
        } else {
            echo "Sección $sec_id ya existe.<br>";
        }
    }
}
$conn->close();
?>
