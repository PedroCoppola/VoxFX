<?php
session_start();
require_once("php/permisos.php");


include("php/conexion.php");

$id_usuario = $_SESSION['id_usuario'];
$username   = $_SESSION['username'];
$rol        = $_SESSION['rol'];

// obtener programas (solo si es jefe u operador)
$programas = [];
if ($rol == 'operador' || $rol == 'jefe') {
    $sql = "SELECT p.id_programa, p.nombre_programa 
            FROM programas p
            INNER JOIN usuarios_programas up ON p.id_programa = up.id_programa
            WHERE up.id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $programas[] = $row;
    }
}

// manejar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo_sonido'];
    $nombre = $_POST['nombre_sonido'];
    $emoji = $_POST['icono_sonido'];
    $programa = $_POST['programa'] ?? null;

    // manejar subida de archivo
    if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = $_FILES['archivo']['name'];
        $rutaTemporal = $_FILES['archivo']['tmp_name'];
        $rutaDestino = "uploads/" . basename($nombreArchivo);

        if (!is_dir("uploads")) mkdir("uploads", 0777, true);
        move_uploaded_file($rutaTemporal, $rutaDestino);

        // insertar sonido
        $sql = "INSERT INTO sonidos (nombre, url, tipo, propietario) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nombre, $rutaDestino, $tipo, $id_usuario);
        $stmt->execute();

        $id_sonido = $conn->insert_id;

        // si es de programa, vincularlo
        if ($tipo === 'programa' && $programa) {
            $sqlLink = "INSERT INTO programas_sonidos (id_programa, id_sonido) VALUES (?, ?)";
            $stmtLink = $conn->prepare($sqlLink);
            $stmtLink->bind_param("ii", $programa, $id_sonido);
            $stmtLink->execute();
        }

        header("Location: dashboard.php?tab=$tipo");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar sonido - VOXFX</title>
  <link rel="stylesheet" href="css/style_agregar.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <div class="logo">VOXFX</div>
  <div class="user-info">
    <span><?php echo htmlspecialchars($username); ?></span>
    <small><?php echo ucfirst($rol); ?></small>
    <div class="avatar"></div>
  </div>
</header>

<div class="container" style="justify-content:center;">
  <div class="main-panel" style="max-width:800px; margin-top:40px;">

    <h2 style="text-align:center; margin-bottom:20px;">Agregar un sonido</h2>
    <hr style="border:1px solid #8e44ad; margin-bottom:30px;">

    <form method="post" enctype="multipart/form-data">

<!-- Tipo de sonido -->
<div style="display:flex; justify-content:space-between; gap:20px; margin-bottom:20px;">
  <select name="tipo_sonido" id="tipo_sonido" class="dropdown" onchange="mostrarProgramas()">
    <?php if ($rol == 'jefe'): ?>
      <option value="institucional">Institucional</option>
      <option value="personal">Personal</option>
      <option value="programa">De programa</option>
    <?php elseif ($rol == 'operador'): ?>
      <option value="personal">Personal</option>
      <option value="programa">De programa</option>
    <?php else: ?>
      <option value="visualizar" disabled>Sin permisos para agregar sonidos</option>
    <?php endif; ?>
  </select>

  <?php if ($rol == 'jefe' || $rol == 'operador'): ?>
  <select name="programa" id="select_programa" class="dropdown" style="display:none;">
    <option value="">Seleccionar programa</option>
    <?php foreach ($programas as $p): ?>
      <option value="<?php echo $p['id_programa']; ?>"><?php echo htmlspecialchars($p['nombre_programa']); ?></option>
    <?php endforeach; ?>
  </select>
  <?php endif; ?>
</div>

      <!-- Nombre e icono -->
      <div style="display:flex; justify-content:space-between; gap:20px; margin-bottom:20px;">
        <input type="text" name="nombre_sonido" placeholder="Nombre del sonido" class="dropdown" required>
      </div>

      <!-- Archivo -->
      <div style="margin-bottom:30px;">
        <label class="dropdown" style="display:block; padding:15px; cursor:pointer;">
          <input type="file" name="archivo" accept="audio/*" style="display:none;" onchange="mostrarArchivo(this)">
          Subir archivo
        </label>
        <p id="fileInfo" style="color:#aaa; margin-top:10px;"></p>
      </div>

      <!-- Botones -->
      <div style="display:flex; justify-content:space-between; margin-top:30px;">
        <a href="dashboard.php"><button type="button" class="delete-btn">Cancelar</button></a>
        <button type="submit" class="add-btn" style="width:40%; background:linear-gradient(to right, #3498db, #e056fd);">Crear</button>
      </div>
    </form>

  </div>
</div>

<script>
  function mostrarProgramas() {
    const tipo = document.getElementById("tipo_sonido").value;
    const selectPrograma = document.getElementById("select_programa");
    selectPrograma.style.display = (tipo === "programa") ? "block" : "none";
  }

  function mostrarArchivo(input) {
    const file = input.files[0];
    if (file) {
      const sizeKB = (file.size / 1024).toFixed(2);
      const fileInfo = document.getElementById("fileInfo");
      fileInfo.textContent = `${file.name} — Tamaño: ${sizeKB} KB`;
    }
  }
</script>

</body>
</html>
