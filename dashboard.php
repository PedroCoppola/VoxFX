<?php
session_start();
include("php/conexion.php");

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$username   = $_SESSION['username'];
$rol        = $_SESSION['rol'];

// ===========================
// 🎚️ OBTENER PROGRAMAS ASIGNADOS (solo jefe y operador)
// ===========================
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

// ===========================
// 🧭 PESTAÑA ACTIVA
// ===========================
$tab = $_GET['tab'] ?? 'institucionales';
$selectedPrograma = $_GET['programa'] ?? null;

if ($tab === 'programa') {
    if (!$selectedPrograma) {
        if (!empty($programas)) {
            $selectedPrograma = $programas[0]['id_programa']; // toma el primero asignado
        } else {
            $selectedPrograma = null;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - VoxFX</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/style_dashboard.css">
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

<div class="container">
  <!-- ===========================
       📋 SIDEBAR
  ============================ -->
  <div class="sidebar">
    <?php if ($rol !== 'productor'): ?>
      <a href="agregar.php"><button class="add-btn">Agregar sonido</button></a>
    <?php endif; ?>

    <?php if ($rol == 'jefe'): ?>
      <a href="usuarios.php"><button class="manage-btn">Gestionar usuarios</button></a>
      <a href="programas.php"><button class="add-btn">Gestionar programas</button></a>
      <a href="asignar_programas.php"><button class="edit-btn">Asignar programas</button></a>
    <?php endif; ?>

    <hr>
    <a href="cambiar_contrasena.php"><button class="edit-btn">Cambiar contraseña</button></a>
    <a href="logout.php"><button class="delete-btn">Cerrar sesión</button></a>
  </div>

  <!-- ===========================
       🎚️ PANEL PRINCIPAL
  ============================ -->
  <div class="main-panel">
    <div class="tabs">
      <div class="tab <?php if ($tab=='institucionales') echo 'active'; ?>" data-tab="institucionales">Sonidos Institucionales</div>

      <?php if ($rol !== 'productor'): ?>
        <div class="tab <?php if ($tab=='programa') echo 'active'; ?>" data-tab="programa">Sonidos del Programa</div>
        <div class="tab <?php if ($tab=='personales') echo 'active'; ?>" data-tab="personales">Mis Sonidos</div>
      <?php endif; ?>
    </div>

    <!-- ===========================
         🎧 TOP BAR
    ============================ -->
    <div class="top-bar" id="programa-bar" style="display:none;">
      <?php if ($rol !== 'productor' && !empty($programas)): ?>
      <form method="get" action="dashboard.php">
        <input type="hidden" name="tab" value="programa">
        <select name="programa" class="dropdown" onchange="this.form.submit()">
          <?php foreach ($programas as $p): ?>
            <option value="<?php echo $p['id_programa']; ?>"
              <?php if ($selectedPrograma == $p['id_programa']) echo 'selected'; ?>>
              <?php echo htmlspecialchars($p['nombre_programa']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </form>
      <?php endif; ?>
      <br><hr>
    </div>

    <!-- ===========================
         🔊 GRID DE SONIDOS
    ============================ -->
    <div class="sound-grid" id="sonidos">
      <?php
      if ($tab == 'institucionales') {
          $sql = "SELECT * FROM sonidos WHERE tipo='institucional'";
      } elseif ($tab == 'programa' && $rol !== 'productor') {
          if ($selectedPrograma) {
              $sql = "SELECT s.* FROM sonidos s
                      INNER JOIN programas_sonidos ps ON s.id_sonido = ps.id_sonido
                      WHERE ps.id_programa = $selectedPrograma";
          } else {
              echo "<p>No tenés programas asignados.</p>";
              $sql = null;
          }
      } elseif ($tab == 'personales' && $rol !== 'productor') {
          $sql = "SELECT * FROM sonidos WHERE tipo='personal' AND propietario=$id_usuario";
      } else {
          // El productor solo ve institucionales
          $sql = null;
          if ($tab !== 'institucionales') {
              echo "<p>No tenés permisos para acceder a esta sección.</p>";
          }
      }

      if ($sql) {
          $result = $conn->query($sql);
          if ($result && $result->num_rows > 0) {
              while ($s = $result->fetch_assoc()) {
                  $id_sonido = (int)$s['id_sonido'];
                  $nombre = htmlspecialchars($s['nombre']);
                  $url = htmlspecialchars($s['url']);

                  echo "<div class='sound-btn' data-sound='$url'>
                          <span>🎵</span>
                          <div class='sound-name'>$nombre</div>";

                  // Mostrar editar/eliminar según rol
                  if ($rol == 'jefe' || ($rol == 'operador' && $tab !== 'institucionales')) {
                      echo "<div class='sound-actions'>
                              <a href='editar_sonido.php?id=$id_sonido' class='btn-icon edit' title='Editar'><i>✏️</i></a>
                              <a href='eliminar_sonido.php?id=$id_sonido' class='btn-icon delete' title='Eliminar' onclick='return confirm(\"¿Seguro que querés eliminar este sonido?\")'><i>🗑️</i></a>
                            </div>";
                  }

                  echo "</div>";
              }
          } else {
              echo "<p>No hay sonidos disponibles en esta categoría.</p>";
          }
      }
      ?>
    </div>
  </div>
</div>

<script>
  // manejar tabs
  const tabs = document.querySelectorAll('.tab');
  tabs.forEach(t => {
    t.addEventListener('click', () => {
      const tab = t.getAttribute('data-tab');
      window.location.href = 'dashboard.php?tab=' + tab;
    });
  });

  const activeTab = "<?php echo $tab; ?>";
  if (activeTab === 'programa') document.getElementById('programa-bar').style.display = 'flex';

  // Reproducción de sonidos 🎧
  let currentAudio = null;
  document.querySelectorAll('.sound-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const soundPath = btn.dataset.sound;
      if (!soundPath) return;
      if (currentAudio && !currentAudio.paused) {
        currentAudio.pause();
        currentAudio.currentTime = 0;
        if (currentAudio.src.includes(soundPath)) {
          currentAudio = null;
          return;
        }
      }
      currentAudio = new Audio(soundPath);
      currentAudio.play().catch(err => console.error("Error al reproducir:", err));
    });
  });
</script>

</body>
</html>
