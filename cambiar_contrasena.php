<?php
session_start();
require_once("php/permisos.php");

include("php/conexion.php");

$id_usuario = $_SESSION['id_usuario'];
$username   = $_SESSION['username'];
$rol        = $_SESSION['rol'];

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $actual = $_POST['actual'];
    $nueva  = $_POST['nueva'];
    $confirm = $_POST['confirm'];

    if ($nueva !== $confirm) {
        $msg = "⚠️ Las contraseñas nuevas no coinciden.";
    } else {
        // verificar contraseña actual
        $stmt = $conn->prepare("SELECT contrasena FROM usuarios WHERE id_usuario = ?");
        $stmt->bind_param("i", $id_usuario);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if ($result && password_verify($actual, $result['contrasena'])) {
            // actualizar
            $hash = password_hash($nueva, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE usuarios SET contrasena = ? WHERE id_usuario = ?");
            $stmt->bind_param("si", $hash, $id_usuario);
            if ($stmt->execute()) {
                $msg = "✅ Contraseña cambiada correctamente.";
            } else {
                $msg = "❌ Error al actualizar la contraseña.";
            }
        } else {
            $msg = "⚠️ La contraseña actual es incorrecta.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Cambiar contraseña - VoxFX</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
  <div class="logo">VoxFX</div>
  <div class="user-info">
    <span><?php echo htmlspecialchars($username); ?></span>
    <small><?php echo ucfirst($rol); ?></small>
    <div class="avatar"></div>
  </div>
</header>

<div class="main-panel">
  <h2>Cambiar contraseña</h2>
  <?php if ($msg) echo "<p class='msg'>$msg</p>"; ?>

  <form method="post">
    <input type="password" name="actual" placeholder="Contraseña actual" required>
    <input type="password" name="nueva" placeholder="Nueva contraseña" required>
    <input type="password" name="confirm" placeholder="Confirmar nueva contraseña" required>
    <button type="submit" class="btn btn-save">Guardar cambios</button>
    <a href="dashboard.php" class="btn btn-cancel">Cancelar</a>
  </form>
</div>

</body>
</html>
