<?php
$conn = new mysqli("localhost", "root", "", "bdcolegio");
if ($conn->connect_error) {
    echo "Connection failed: " . $conn->connect_error;
    exit(1);
} else {
    echo "Connected successfully to bdcolegio";
}
?>
