<?php
session_start();
include 'conexion.php';

// Verificación de administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 'Admin') {
    header("Location: login.php");
    exit();
}

// Obtener la pestaña de redirección
$redirect_tab = isset($_GET['redirect']) ? $_GET['redirect'] : 'usuarios';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    try {
        // Desactivar temporalmente las restricciones de clave foránea
        $conn->query("SET FOREIGN_KEY_CHECKS = 0");
        
        // 1. Eliminar detalles de pedido asociados a los pedidos del usuario
        $conn->query("DELETE dp FROM detalles_pedido dp
                     INNER JOIN pedidos p ON dp.pedido_id = p.id
                     WHERE p.cliente_id = $id");
        
        // 2. Eliminar los pedidos del usuario
        $conn->query("DELETE FROM pedidos WHERE cliente_id = $id");
        
        // 3. Eliminar el usuario
        $sql = "DELETE FROM clientes WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            $_SESSION['swal_icon'] = 'success';
            $_SESSION['swal_title'] = 'Éxito';
            $_SESSION['swal_message'] = 'Usuario y todos sus registros relacionados eliminados correctamente';
        }
        $stmt->close();
        
        // Reactivar las restricciones de clave foránea
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        
    } catch (Exception $e) {
        // Asegurarse de reactivar las restricciones incluso si hay error
        $conn->query("SET FOREIGN_KEY_CHECKS = 1");
        
        $_SESSION['swal_icon'] = 'error';
        $_SESSION['swal_title'] = 'Error';
        $_SESSION['swal_message'] = 'Error al eliminar el usuario: ' . $e->getMessage();
    }
}

header("Location: admin.php#" . $redirect_tab);
exit();
?>