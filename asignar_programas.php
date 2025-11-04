<?php
session_start();
include("php/conexion.php");
require_once("php/permisos.php");

// solo jefe
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'jefe') {
    header("Location: dashboard.php");
    exit();
}

$username = $_SESSION['username'];

// obtener programas y usuarios
$programas = $conn->query("SELECT * FROM programas ORDER BY nombre_programa");
$usuarios = $conn->query("SELECT id_usuario, username, rol FROM usuarios ORDER BY rol, username");

// manejar asignaciones
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['programa'])) {
    $id_programa = intval($_POST['programa']);
    $asignados = $_POST['usuarios'] ?? [];

    // limpiar asignaciones actuales
    $conn->query("DELETE FROM usuarios_programas WHERE id_programa = $id_programa");

    // agregar nuevas
    $stmt = $conn->prepare("INSERT INTO usuarios_programas (id_usuario, id_programa) VALUES (?, ?)");
    foreach ($asignados as $id_usuario) {
        $stmt->bind_param("ii", $id_usuario, $id_programa);
        $stmt->execute();
    }

    $msg = "✅ Asignaciones actualizadas correctamente.";
}

// si hay programa seleccionado, obtener asignados
$asignados_actual = [];
if (isset($_POST['programa'])) {
    $id_programa = intval($_POST['programa']);
    $res = $conn->query("SELECT id_usuario FROM usuarios_programas WHERE id_programa=$id_programa");
    while ($r = $res->fetch_assoc()) {
        $asignados_actual[] = $r['id_usuario'];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Asignar programas - VOXFX</title>
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
  <h2>Asignar usuarios a programas</h2>
  <?php if (isset($msg)) echo "<p>$msg</p>"; ?>

  <form method="post">
    <label>Seleccionar programa</label>
    <select name="programa" class="dropdown" onchange="this.form.submit()">
      <option value="">-- Selecciona un programa --</option>
      <?php while ($p = $programas->fetch_assoc()): ?>
        <option value="<?php echo $p['id_programa']; ?>" 
          <?php if (isset($id_programa) && $id_programa == $p['id_programa']) echo 'selected'; ?>>
          <?php echo htmlspecialchars($p['nombre_programa']); ?>
        </option>
      <?php endwhile; ?>
    </select>

    <?php if (isset($id_programa)): ?>
    <h3>Seleccionar usuarios para este programa</h3>
    <div class="user-list">
      <?php while ($u = $usuarios->fetch_assoc()): ?>
        <div class="user-item">
          <input type="checkbox" name="usuarios[]" value="<?php echo $u['id_usuario']; ?>"
            <?php if (in_array($u['id_usuario'], $asignados_actual)) echo 'checked'; ?>>
          <?php echo htmlspecialchars($u['username'])." (".ucfirst($u['rol']).")"; ?>
      </div>
      <?php endwhile; ?>
    </div>
    <br>
    <button type="submit" class="btn btn-save">💾 Guardar Asignaciones</button>
    <?php endif; ?>
  </form>

  <div style="text-align:center; margin-top:25px;">
    <a href="dashboard.php" class="btn">⬅ Volver al Dashboard</a>
  </div>
</div>

</body>
</html>
