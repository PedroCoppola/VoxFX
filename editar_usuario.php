<?php
session_start();
include("php/conexion.php");

// solo el jefe puede editar usuarios
require_once("php/permisos.php");


// si no se pasa ID, volver
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: usuarios.php");
    exit();
}

$id = intval($_GET['id']);
$username = $_SESSION['username'];

// obtener usuario actual
$stmt = $conn->prepare("SELECT id_usuario, username, email, rol FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    header("Location: usuarios.php");
    exit();
}
$usuario = $result->fetch_assoc();
$stmt->close();

// actualizar usuario si se envió el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['username'];
    $email = $_POST['email'];
    $rol = $_POST['rol'];
    $contrasena = $_POST['contrasena'];

    if (!empty($contrasena)) {
        // actualizar con nueva contraseña
        $hash = password_hash($contrasena, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET username=?, email=?, rol=?, contrasena=? WHERE id_usuario=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nombre, $email, $rol, $hash, $id);
    } else {
        // actualizar sin cambiar contraseña
        $sql = "UPDATE usuarios SET username=?, email=?, rol=? WHERE id_usuario=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nombre, $email, $rol, $id);
    }

    if ($stmt->execute()) {
        header("Location: usuarios.php");
        exit();
    } else {
        $error = "Error al actualizar usuario.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Usuario - VOXFX</title>
  <link rel="stylesheet" href="css/style.css">
  <style>

  </style>
</head>
<body>

<header>
  <div class="logo">VOXFX</div>
  <div class="user-info">
    <span><?php echo htmlspecialchars($username); ?></span>
    <small>Jefe de Operadores</small>
    <div class="avatar"></div>
  </div>
</header>

<div class="main-panel">
  <h2 style="margin-bottom:20px;">Editar usuario</h2>
  <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

  <form method="post">
    <label>Nombre y Apellido</label>
    <input type="text" name="username" value="<?php echo htmlspecialchars($usuario['username']); ?>" required>

    <label>Email</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>

    <label>Rol</label>
    <select name="rol" required>
      <option value="jefe" <?php if ($usuario['rol']=='jefe') echo 'selected'; ?>>Jefe de Operadores</option>
      <option value="operador" <?php if ($usuario['rol']=='operador') echo 'selected'; ?>>Operador</option>
      <option value="productor" <?php if ($usuario['rol']=='productor') echo 'selected'; ?>>Productor</option>
    </select>

    <label>Nueva contraseña (opcional)</label>
    <input type="password" name="contrasena" placeholder="Dejar vacío para no cambiarla">

    <div style="display:flex; justify-content:center; margin-top:25px;">
      <button type="submit" class="btn btn-save">💾 Guardar cambios</button>
      <a href="usuarios.php" class="btn btn-cancel">Cancelar</a>
    </div>
  </form>
</div>

</body>
</html>
