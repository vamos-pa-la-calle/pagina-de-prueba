<?php
session_start();
include 'conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['error'] = "Debes iniciar sesión para completar la compra";
    header("Location: login.php");
    exit();
}

// Verificar que el carrito no esté vacío
if (empty($_SESSION['carrito'])) {
    $_SESSION['error'] = "El carrito está vacío";
    header("Location: carrito.php");
    exit();
}

// Calcular total
$total = 0;
foreach($_SESSION['carrito'] as $item) {
    $total += $item['precio'] * $item['cantidad'];
}

// Iniciar transacción
$conn->begin_transaction();

try {
    // 1. Crear el pedido
    $sql_pedido = "INSERT INTO pedidos (cliente_id, estado, total) VALUES (?, 'pendiente', ?)";
    $stmt_pedido = $conn->prepare($sql_pedido);
    $stmt_pedido->bind_param("id", $_SESSION['usuario_id'], $total);
    $stmt_pedido->execute();
    $pedido_id = $conn->insert_id;
    
    // 2. Agregar los detalles del pedido
    $sql_detalle = "INSERT INTO detalles_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
    $stmt_detalle = $conn->prepare($sql_detalle);
    
    foreach($_SESSION['carrito'] as $producto_id => $item) {
        $stmt_detalle->bind_param("iiid", $pedido_id, $producto_id, $item['cantidad'], $item['precio']);
        $stmt_detalle->execute();
    }
    
    // Confirmar transacción
    $conn->commit();
    
    // Limpiar carrito
    unset($_SESSION['carrito']);
    
    $_SESSION['mensaje'] = "¡Compra realizada con éxito! Número de pedido: #$pedido_id";
    header("Location: carrito.php");
    exit();
    
} catch (Exception $e) {
    // Revertir en caso de error
    $conn->rollback();
    
    $_SESSION['error'] = "Error al procesar el pago: " . $e->getMessage();
    header("Location: carrito.php");
    exit();
}
?>