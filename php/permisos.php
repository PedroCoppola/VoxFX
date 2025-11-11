<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_usuario'])) {
    header("Location: index.php");
    exit();
}

include_once("conexion.php");

$rol = $_SESSION['rol'];
$pagina = basename($_SERVER['PHP_SELF']); // ej: 'usuarios.php'

// ===========================
// MAPA DE PERMISOS POR ROL
// ===========================
$permisos = [
    'jefe' => [
        'permitidas' => [
            'dashboard.php', 'usuarios.php', 'editar_usuario.php', 'programas.php',
            'asignar_programas.php', 'editar_programa.php', 'agregar.php', 'editar_sonido.php',
            'eliminar_sonido.php', 'cambiar_contrasena.php', 'logout.php', 'registro_usuarios.php'
        ]
    ],
    'operador' => [
        'permitidas' => [
            'dashboard.php', 'agregar.php', 'editar_sonido.php',
            'eliminar_sonido.php', 'cambiar_contrasena.php', 'logout.php'
        ]
    ],
    'productor' => [
        'permitidas' => [
            'dashboard.php', 'cambiar_contrasena.php', 'logout.php'
        ]
    ]
];

// ===========================
// VERIFICAR ACCESO
// ===========================
if (!isset($permisos[$rol])) {
    // Rol desconocido o no autorizado
    session_destroy();
    header("Location: index.php");
    exit();
}

// si la página actual no está en las permitidas para ese rol
if (!in_array($pagina, $permisos[$rol]['permitidas'])) {
    header("Location: dashboard.php");
    exit();
}
?>
