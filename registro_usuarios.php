<?php
session_start();
include("php/conexion.php"); // ajustar ruta si hace falta
require_once("php/permisos.php");

$errors = [];
$success = false;

// lista permitida de roles (si querés cambiar más adelante, lo hacés acá)
$roles_permitidos = ['jefe', 'operador', 'productor'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // recibir y sanitizar
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';
    $rol = $_POST['rol'] ?? '';

    // validaciones básicas
    if ($username === '') $errors[] = "El nombre y apellido es obligatorio.";
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email inválido.";
    if (strlen($password) < 6) $errors[] = "La contraseña debe tener al menos 6 caracteres.";
    if ($password !== $password2) $errors[] = "Las contraseñas no coinciden.";
    if (!in_array($rol, $roles_permitidos)) $errors[] = "Rol inválido.";

    // si no hay errores, comprobar unicidad y crear usuario
    if (empty($errors)) {
        // comprobar username único
        $sql = "SELECT id_usuario FROM usuarios WHERE username = ? OR email = ? LIMIT 1";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $errors[] = "Ya existe un usuario con ese nombre o email.";
            }
            $stmt->close();
        } else {
            $errors[] = "Error en la base de datos (prepare).";
        }
    }

    if (empty($errors)) {
        // hashear contraseña
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // insertar en DB
        $insert = "INSERT INTO usuarios (username, email, contrasena, rol) VALUES (?, ?, ?, ?)";
        if ($stmt = $conn->prepare($insert)) {
            $stmt->bind_param("ssss", $username, $email, $password_hash, $rol);
            if ($stmt->execute()) {
                $success = true;
            } else {
                $errors[] = "Error al insertar el usuario: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errors[] = "Error en la base de datos (prepare insert).";
        }
    }
}


if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'jefe') {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Registrar usuario - VOXFX</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link rel="stylesheet" href="css/style.css"> <!-- usa tu CSS -->
  <style>
    /* pequeños ajustes para el form si querés */
    .form-card {
      max-width: 640px;
      margin: 40px auto;
      background: #1e1e1e;
      padding: 26px;
      border-radius: 16px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.6);
    }
    .field { margin-bottom: 14px; text-align:left; }
    label { display:block; margin-bottom:6px; font-weight:500; }
    input[type="text"], input[type="email"], input[type="password"], select {
      width:100%; padding:12px 16px; border-radius:40px; border:1px solid #444;
      background:#2b2b2b; color:#fff; box-sizing:border-box;
    }
    .actions { display:flex; gap:12px; justify-content:flex-end; margin-top:18px; }
    .btn { padding:12px 22px; border-radius:40px; border:none; cursor:pointer; color:#fff; font-weight:700; }
    .btn-crear { background:linear-gradient(90deg,#3498db,#e056fd); }
    .btn-cancel { background:linear-gradient(90deg,#555,#333); }
    .msg-error { background: #2b1b1b; color:#ffb3b3; padding:10px; border-radius:8px; margin-bottom:12px; border:1px solid #5b1b1b; }
    .msg-ok { background: #11221a; color:#9ff2bf; padding:10px; border-radius:8px; margin-bottom:12px; border:1px solid #1b5b3b; }
  </style>
</head>
<body>

<header style="background:linear-gradient(to right,#8e44ad,#3498db); padding:12px;">
  <div style="max-width:1100px;margin:0 auto;display:flex;justify-content:space-between;align-items:center;color:#fff;">
    <div class="logo">VOXFX</div>
    <div><?php if(isset($_SESSION['username'])) echo htmlspecialchars($_SESSION['username']); ?></div>
  </div>
</header>

<main>
  <div class="form-card">
    <h2>Registrar nuevo usuario</h2>

    <?php if (!empty($errors)): ?>
      <div class="msg-error">
        <?php foreach ($errors as $e) echo "<div>- " . htmlspecialchars($e) . "</div>"; ?>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="msg-ok">Usuario creado correctamente. <a href="dashboard.php" style="color:#bfefff;">Ir al dashboard</a></div>
    <?php endif; ?>

    <form method="post" action="registro_usuarios.php">
      <div class="field">
        <label for="username">Nombre y Apellido</label>
        <input id="username" name="username" type="text" maxlength="100" value="<?php echo isset($username)?htmlspecialchars($username):''; ?>" required>
      </div>

      <div class="field">
        <label for="email">Email</label>
        <input id="email" name="email" type="email" maxlength="150" value="<?php echo isset($email)?htmlspecialchars($email):''; ?>" required>
      </div>

      <div class="field" style="display:flex;gap:10px;">
        <div style="flex:1">
          <label for="password">Contraseña</label>
          <input id="password" name="password" type="password" required>
        </div>
        <div style="flex:1">
          <label for="password2">Confirmar contraseña</label>
          <input id="password2" name="password2" type="password" required>
        </div>
      </div>

      <div class="field">
        <label for="rol">Rol</label>
        <select id="rol" name="rol" required>
          <option value="">-- Seleccionar rol --</option>
          <option value="jefe" <?php if(isset($rol) && $rol==='jefe') echo 'selected'; ?>>Jefe de Operadores</option>
          <option value="operador" <?php if(isset($rol) && $rol==='operador') echo 'selected'; ?>>Operador</option>
          <option value="productor" <?php if(isset($rol) && $rol==='productor') echo 'selected'; ?>>Productor</option>
        </select>
      </div>

      <div class="actions">
        <a href="usuarios.php" class="btn btn-add" style="padding:10px 22px;">⬅ Volver a los Usuarios</a>
        <a href="dashboard.php"><button type="button" class="btn btn-cancel">Cancelar</button></a>
        <button type="submit" class="btn btn-crear">Crear cuenta</button>
      </div>
    </form>
  </div>
</main>

</body>
</html>
