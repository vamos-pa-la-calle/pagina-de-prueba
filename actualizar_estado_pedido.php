<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 'Admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['estado'])) {
    $id = (int)$_GET['id'];
    $estado = $conn->real_escape_string($_GET['estado']);
    
    $sql = "UPDATE pedidos SET estado = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $estado, $id);
    
    if ($stmt->execute()) {
        $_SESSION['swal_icon'] = 'success';
        $_SESSION['swal_title'] = 'Éxito';
        $_SESSION['swal_message'] = 'Estado del pedido actualizado correctamente';
    } else {
        $_SESSION['swal_icon'] = 'error';
        $_SESSION['swal_title'] = 'Error';
        $_SESSION['swal_message'] = 'Error al actualizar el estado del pedido';
    }
}

header("Location: admin.php#pedidos");
exit();
?>