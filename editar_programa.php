<?php
session_start();
include("php/conexion.php");

// Solo el jefe puede entrar
require_once("php/permisos.php");


$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("ID de programa inválido.");
}

// obtener datos del programa
$stmt = $conn->prepare("SELECT * FROM programas WHERE id_programa = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$programa = $result->fetch_assoc();

if (!$programa) die("Programa no encontrado.");

// procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre_programa']);
    $descripcion = trim($_POST['descripcion']);
    $horario = trim($_POST['horario']);

    $stmt = $conn->prepare("UPDATE programas SET nombre_programa=?, descripcion=?, horario=? WHERE id_programa=?");
    $stmt->bind_param("sssi", $nombre, $descripcion, $horario, $id);
    if ($stmt->execute()) {
        header("Location: programas.php");
        exit();
    } else {
        $msg = "❌ Error al actualizar programa.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Programa - VOXFX</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<header>
  <div class="logo">VOXFX</div>
</header>

<div class="main-panel">
  <h2>Editar programa</h2>
  <?php if (isset($msg)) echo "<p class='msg'>$msg</p>"; ?>

  <form method="post">
    <label>Nombre del programa</label>
    <input type="text" name="nombre_programa" value="<?php echo htmlspecialchars($programa['nombre_programa']); ?>" required>

    <label>Descripción</label>
    <textarea name="descripcion" rows="3"><?php echo htmlspecialchars($programa['descripcion']); ?></textarea>

    <label>Horario</label>
    <input type="text" name="horario" value="<?php echo htmlspecialchars($programa['horario']); ?>">

    <button type="submit" class="btn btn-save">Guardar cambios</button>
    <a href="programas.php" class="btn btn-cancel">Cancelar</a>
  </form>
</div>

</body>
</html>
