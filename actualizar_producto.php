<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 'Admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['id'];
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $descripcion = $conn->real_escape_string($_POST['descripcion']);
    $precio = (float)$_POST['precio'];
    $imagen = $conn->real_escape_string($_POST['imagen']);

    $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, imagen = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsi", $nombre, $descripcion, $precio, $imagen, $id);

    if ($stmt->execute()) {
        $_SESSION['swal_icon'] = 'success';
        $_SESSION['swal_title'] = 'Éxito';
        $_SESSION['swal_message'] = 'Producto actualizado correctamente';
    } else {
        $_SESSION['swal_icon'] = 'error';
        $_SESSION['swal_title'] = 'Error';
        $_SESSION['swal_message'] = 'Error al actualizar el producto';
    }
}

header("Location: admin.php#productos");
exit();
?>