<?php

// Configuración de la base de datos
$servername = "localhost";
$username_db = "root"; // Reemplaza con tu usuario de BD
$password_db = ""; // Reemplaza con tu contraseña de BD
$dbname = "voxfx"; // Reemplaza con el nombre de tu BD

// Crea la conexión
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>