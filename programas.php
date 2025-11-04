<?php
session_start();
include("php/conexion.php");

// solo el jefe puede entrar
require_once("php/permisos.php");


$username = $_SESSION['username'];

// eliminar programa (si se pasó ?delete=)
if (isset($_GET['delete'])) {
    $id_borrar = intval($_GET['delete']);
    if ($id_borrar > 0) {
        // eliminar relaciones primero
        $conn->query("DELETE FROM usuarios_programas WHERE id_programa = $id_borrar");
        $conn->query("DELETE FROM programas_sonidos WHERE id_programa = $id_borrar");
        // eliminar programa
        $conn->query("DELETE FROM programas WHERE id_programa = $id_borrar");
        $msg = "🗑️ Programa eliminado correctamente.";
    }
}

// manejar formulario de alta
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_programa'])) {
    $nombre = trim($_POST['nombre_programa']);
    $descripcion = trim($_POST['descripcion']);
    $horario = trim($_POST['horario']);

    if ($nombre !== "") {
        $stmt = $conn->prepare("INSERT INTO programas (nombre_programa, descripcion, horario) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nombre, $descripcion, $horario);
        if ($stmt->execute()) {
            $msg = "✅ Programa agregado correctamente.";
        } else {
            $msg = "❌ Error al agregar programa.";
        }
    } else {
        $msg = "⚠️ El nombre del programa es obligatorio.";
    }
}

// obtener lista de programas existentes
$result = $conn->query("SELECT * FROM programas ORDER BY id_programa DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Programas - VOXFX</title>
<link rel="stylesheet" href="css/style.css">
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
  <h2>Gestionar Programas</h2>
  <?php if (isset($msg)) echo "<p class='msg'>$msg</p>"; ?>

  <form method="post" style="margin-bottom:30px;">
    <input type="text" name="nombre_programa" placeholder="Nombre del programa" required>
    <textarea name="descripcion" rows="3" placeholder="Descripción (opcional)"></textarea>
    <input type="text" name="horario" placeholder="Horario (ej: Lunes a Viernes 18:00 - 20:00)">
    <button type="submit" class="btn btn-add">Agregar programa</button>
  </form>

  <div style="margin-bottom:15px;">
    <a href="asignar_programas.php"><button class="btn btn-add">Asignar programas</button></a>
  </div>

  <h3>Programas existentes</h3>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th>Descripción</th>
        <th>Horario</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?php echo $row['id_programa']; ?></td>
          <td><?php echo htmlspecialchars($row['nombre_programa']); ?></td>
          <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
          <td><?php echo htmlspecialchars($row['horario']); ?></td>
          <td>
            <div class="actions">
              <form method="get" action="programas.php" onsubmit="return confirm('¿Seguro que querés eliminar este programa?');">
                <input type="hidden" name="delete" value="<?php echo $row['id_programa']; ?>">
                <button type="submit" class="btn btn-del">Eliminar</button>
                <a href="editar_programa.php?id=<?php echo $row['id_programa']; ?>" 
                   class="btn btn-edit" 
                   style="background:linear-gradient(to right,#5dade2,#3498db);text-decoration:none;">
                   Editar
                </a>
              </form>
            </div>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

  <div style="text-align:center; margin-top:25px;">
    <a href="dashboard.php" class="btn btn-add">⬅ Volver al Dashboard</a>
  </div>
</div>

</body>
</html>
