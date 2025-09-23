<?php
session_start();

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

// Obtiene los datos del formulario y los sanitiza
$username = $conn->real_escape_string($_POST['username']);
$email = $conn->real_escape_string($_POST['email']);
$contrasena_ingresada = $_POST['contrasena'];

// Consulta la base de datos para encontrar al usuario por username y email
$sql = "SELECT id_usuario, username, contrasena, rol FROM usuarios WHERE username = '$username' AND email = '$email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Si se encontró un usuario con ese username y email, verifica la contraseña
    $row = $result->fetch_assoc();
    $hash_almacenado = $row['contrasena'];

    if (password_verify($contrasena_ingresada, $hash_almacenado)) {
        // Contraseña correcta, inicia la sesión
        $_SESSION['id_usuario'] = $row['id_usuario'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['rol'] = $row['rol'];

        
        // Redirige al usuario a la página de bienvenida (o dashboard)
        header("Location: ../dashboard.php");
        exit();
    } else {
        // Contraseña incorrecta
        header("Location: ../index.php?error=1");
        exit();
    }
} else {
    // Usuario o email no encontrado
    header("Location: ../index.php?error=1");
    exit();
}

$conn->close();
?>