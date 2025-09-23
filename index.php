<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="logo"><h1 style="font-family:'Bicubik Regular';font-weight:normal;font-size:30px">VoxFX</h1>
</header>
                    <h2>Iniciar Sesión</h2>

    <form action="login.php" method="post">
        <div class="input-group">
            <input type="text" name="username" placeholder="Ingrese su nombre de usuario" required>
        </div>
        <div class="input-group">
            <input type="email" name="email" placeholder="Ingrese su email" required>
        </div>
        <div class="input-group">
            <input type="password" name="contrasena" placeholder="Ingrese su contraseña" required>
        </div>
        <button type="submit" class="login-btn">Iniciar Sesión</button>
    </form>
    <?php
    // Muestra el mensaje de error si existe
    if (isset($_GET['error'])) {
        echo '<p class="error-msg">Datos de inicio de sesión incorrectos.</p>';
    }
    ?>
</body>
</html>