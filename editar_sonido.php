<?php
session_start();
require_once("php/permisos.php");

include("php/conexion.php");

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("ID inválido.");
}

$sql = "SELECT * FROM sonidos WHERE id_sonido = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$sonido = $result->fetch_assoc();

if (!$sonido) die("Sonido no encontrado.");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $tipo = $_POST['tipo'];

    $url = $sonido['url'];
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = basename($_FILES['archivo']['name']);
        $rutaDestino = "uploads/" . $nombreArchivo;
        move_uploaded_file($_FILES['archivo']['tmp_name'], $rutaDestino);
        $url = $rutaDestino;
    }

    $sql = "UPDATE sonidos SET nombre = ?, tipo = ?, url = ? WHERE id_sonido = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nombre, $tipo, $url, $id);
    $stmt->execute();

    header("Location: dashboard.php?tab=" . urlencode($tipo));
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar sonido</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<header>
  <div class="logo">VoxFX</div>
</header>

<div class="main-panel">
  <h2>Editar sonido</h2>
  <hr>

  <form method="post" enctype="multipart/form-data">
    <label>Nombre</label>
    <input type="text" name="nombre" value="<?php echo htmlspecialchars($sonido['nombre']); ?>" required>

    <label>Tipo</label>
    <select name="tipo" required>
      <option value="institucional" <?php if ($sonido['tipo']=="institucional") echo "selected"; ?>>Institucional</option>
      <option value="personal" <?php if ($sonido['tipo']=="personal") echo "selected"; ?>>Personal</option>
      <option value="programa" <?php if ($sonido['tipo']=="programa") echo "selected"; ?>>De programa</option>
    </select>

    <label>Reemplazar archivo (opcional)</label>
    <input type="file" name="archivo" accept="audio/*">

    <button type="submit" class="btn btn-save">Guardar cambios</button>
    <a href="dashboard.php" class="btn btn-cancel">Cancelar</a>
  </form>
</div>
</body>
</html>
