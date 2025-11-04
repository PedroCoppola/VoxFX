<?php
session_start();
require_once("php/permisos.php");


include("php/conexion.php");

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    die("ID inválido.");
}

// obtener el sonido para borrar el archivo también
$sql = "SELECT url FROM sonidos WHERE id_sonido = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$sonido = $result->fetch_assoc();

if ($sonido) {
    $url = $sonido['url'];
    if (file_exists($url)) {
        unlink($url);
    }

    $sql = "DELETE FROM sonidos WHERE id_sonido = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: dashboard.php?tab=institucionales");
exit();
?>
