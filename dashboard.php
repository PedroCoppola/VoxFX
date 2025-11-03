<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

include("php/conexion.php"); // archivo donde conectás la BD

$id_usuario = $_SESSION['id_usuario'];
$username   = $_SESSION['username'];
$rol        = $_SESSION['rol'];

// obtener programas asignados si es operador o jefe
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
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - Jefe de Operadores</title>
  <link rel="stylesheet" href="css/style_dashboard.css">
</head>
<body>

<header>
  <div class="menu-icon">≡</div>
  <div class="logo">FM PIXEL</div>
  <div class="user-info">
    <span><?php echo htmlspecialchars($username); ?></span>
    <small><?php echo ucfirst($rol); ?></small>
    <div class="avatar"></div>
  </div>
</header>

<div class="container">
  <!-- Sidebar -->
  <div class="sidebar">
    <a href="agregar.php"><button class="add-btn">Agregar sonido</button></a>
    <button class="edit-btn">Editar sonido</button>
    <button class="delete-btn">Eliminar sonido</button>
    <?php if ($rol == 'jefe'): ?>
      <a href="usuarios.php"><button class="manage-btn">Gestionar usuarios</button></a>
    <?php endif; ?>
  </div>

  <!-- Panel principal -->
  <div class="main-panel">
    <!-- Tabs -->
    <div class="tabs">
      <div class="tab" data-tab="institucionales">Sonidos Institucionales</div>
      <div class="tab active" data-tab="programa">Sonidos del Programa</div>
      <div class="tab" data-tab="personales">Mis Sonidos</div>
    </div>

    <!-- Barra superior -->
    <div class="top-bar" id="programa-bar" style="display:none;">
<form method="get" action="dashboard.php">
  <input type="hidden" name="tab" value="programa">
  <select name="programa" class="dropdown" onchange="this.form.submit()">
    <option value="">Seleccionar programa</option>

    <?php foreach ($programas as $p): ?>
      <option value="<?php echo $p['id_programa']; ?>"
        <?php if (isset($_GET['programa']) && $_GET['programa']==$p['id_programa']) echo 'selected'; ?>>
        <?php echo htmlspecialchars($p['nombre_programa']); ?>
      </option>
    <?php endforeach; ?>
  </select>
</form>
      <br> <hr>

    </div>
           


    <!-- Grid sonidos -->
  <div class="sound-grid" id="sonidos">
      <?php
      $tab = $_GET['tab'] ?? 'institucionales';

      if ($tab == 'institucionales') {
          $sql = "SELECT * FROM sonidos WHERE tipo='institucional'";
      } elseif ($tab == 'programa' && isset($_GET['programa'])) {
          $id_programa = intval($_GET['programa']);
          $sql = "SELECT s.* FROM sonidos s
                  INNER JOIN programas_sonidos ps ON s.id_sonido = ps.id_sonido
                  WHERE ps.id_programa = $id_programa";
      } else {
          $sql = "SELECT * FROM sonidos WHERE tipo='personal' AND propietario=$id_usuario";
      }

      $result = $conn->query($sql);

      if ($result && $result->num_rows > 0) {
          while ($s = $result->fetch_assoc()) {
              $nombre = htmlspecialchars($s['nombre']);
              $url = htmlspecialchars($s['url']);
              echo "<div class='sound-btn' data-sound='$url'>
                      <span>🎵</span>$nombre
                    </div>";
          }
      } else {
          echo "<p>No hay sonidos disponibles en esta categoría.</p>";
      }
      ?>
    </div>
  </div>
</div>

<script>
  // manejar tabs con redirección
  const tabs = document.querySelectorAll('.tab');
  tabs.forEach(t => {
    t.addEventListener('click', () => {
      const tab = t.getAttribute('data-tab');
      window.location.href = 'dashboard.php?tab=' + tab;
    });
  });

  // obtener pestaña activa desde PHP
  const activeTab = "<?php echo $tab; ?>";

  // asignar clase 'active'
  tabs.forEach(t => {
    if (t.getAttribute('data-tab') === activeTab) {
      t.classList.add('active');
    } else {
      t.classList.remove('active');
    }
  });

  // mostrar dropdown solo si la pestaña es 'programa'
  if (activeTab === 'programa') {
    document.getElementById('programa-bar').style.display = 'flex';
  }

  // ========================
  // 🎧 REPRODUCCIÓN DE SONIDOS
  // ========================
  let currentAudio = null;

  document.querySelectorAll('.sound-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const soundPath = btn.dataset.sound;

      if (!soundPath) return;

      // si hay un audio sonando, lo detiene
      if (currentAudio && !currentAudio.paused) {
        currentAudio.pause();
        currentAudio.currentTime = 0;
        // si el mismo botón se clickea de nuevo, no arranca otro
        if (currentAudio.src.includes(soundPath)) {
          currentAudio = null;
          return;
        }
      }

      // crear nuevo audio
      currentAudio = new Audio(soundPath);
      currentAudio.play().catch(err => console.error("Error al reproducir:", err));
    });
  });
</script>

</body>
</html>
