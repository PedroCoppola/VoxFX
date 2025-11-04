<?php
session_start();
include("php/conexion.php");

// permitir solo jefe de operadores
require_once("php/permisos.php");


$username = $_SESSION['username'];

// eliminar usuario si se pidió
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id_borrar = intval($_GET['delete']);
    if ($id_borrar !== $_SESSION['id_usuario']) { // no se puede borrar a sí mismo
        $del = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
        $del->bind_param("i", $id_borrar);
        $del->execute();
        $del->close();
    }
    header("Location: usuarios.php");
    exit();
}

// obtener lista de usuarios
$sql = "SELECT id_usuario, username, email, rol FROM usuarios ORDER BY rol, username";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Usuarios - VOXFX</title>
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
  <div class="header-bar">
    <h2>Gestión de Usuarios</h2>
    <a href="registro_usuarios.php" class="btn btn-add">+ Crear nuevo usuario</a>
  </div>

  <?php if ($result && $result->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>ID</th>
          <th>Nombre y Apellido</th>
          <th>Email</th>
          <th>Rol</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($u = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo $u['id_usuario']; ?></td>
            <td><?php echo htmlspecialchars($u['username']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td style="text-transform:capitalize;"><?php echo htmlspecialchars($u['rol']); ?></td>
            <td>
              <div class="actions">
                <form method="get" action="usuarios.php" onsubmit="return confirm('¿Seguro que querés eliminar este usuario?');">
                  <input type="hidden" name="delete" value="<?php echo $u['id_usuario']; ?>">
                  <button type="submit" class="btn btn-del">Eliminar</button>
                  <a href="editar_usuario.php?id=<?php echo $u['id_usuario']; ?>" class="btn btn-edit" 
   style="background:linear-gradient(to right,#5dade2,#3498db);text-decoration:none;">Editar</a>

                </form>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <div class="msg-empty">No hay usuarios registrados.</div>
  <?php endif; ?>

  <div style="margin-top:20px; text-align:center;">
    <a href="dashboard.php" class="btn btn-add" style="padding:10px 22px;">⬅ Volver al Dashboard</a>
  </div>
</div>

</body>
</html>
