<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 'Admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $precio = (float)$_POST['precio'];
    $imagen = $conn->real_escape_string($_POST['imagen']);

    $sql = "INSERT INTO productos (nombre, descripcion, precio, imagen) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssds", $nombre, $descripcion, $precio, $imagen);

    if ($stmt->execute()) {
        $_SESSION['swal_icon'] = 'success';
        $_SESSION['swal_title'] = 'Éxito';
        $_SESSION['swal_message'] = 'Producto creado correctamente';
    } else {
        $_SESSION['swal_icon'] = 'error';
        $_SESSION['swal_title'] = 'Error';
        $_SESSION['swal_message'] = 'Error al crear el producto';
    }
}

header("Location: admin.php#productos");
exit();
?>