<?php
session_start();
include 'conexion.php';

// Verificación de administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 'Admin') {
    header("Location: login.php");
    exit();
}

// Obtener la pestaña de redirección
$redirect_tab = isset($_GET['redirect']) ? $_GET['redirect'] : 'productos';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        // 1. Primero eliminar los detalles de pedido relacionados
        $conn->query("DELETE FROM detalles_pedido WHERE producto_id = $id");
        
        // 2. Luego eliminar el producto
        $sql = "DELETE FROM productos WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['swal_icon'] = 'success';
            $_SESSION['swal_title'] = 'Éxito';
            $_SESSION['swal_message'] = 'Producto eliminado correctamente';
        }
        $stmt->close();
        
    } catch (Exception $e) {
        $_SESSION['swal_icon'] = 'error';
        $_SESSION['swal_title'] = 'Error';
        $_SESSION['swal_message'] = 'Error al eliminar: ' . $e->getMessage();
    }
}

// Redirigir manteniendo la pestaña
header("Location: admin.php#" . $redirect_tab);
exit();
?>