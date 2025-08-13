<?php
session_start();
include 'conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['error'] = "Debes iniciar sesión para realizar esta acción";
    header("Location: login.php");
    exit();
}

// Verificar que se envió el ID del pedido
if (!isset($_POST['pedido_id'])) {
    $_SESSION['error'] = "No se especificó un pedido";
    header("Location: carrito.php");
    exit();
}

$pedido_id = (int)$_POST['pedido_id'];

// Verificar que el pedido pertenece al usuario y está pendiente
$sql = "SELECT id FROM pedidos WHERE id = ? AND cliente_id = ? AND estado = 'pendiente'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $pedido_id, $_SESSION['usuario_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "No se puede cancelar el pedido";
    header("Location: carrito.php");
    exit();
}

// Actualizar estado del pedido
$sql_update = "UPDATE pedidos SET estado = 'cancelado' WHERE id = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("i", $pedido_id);

if ($stmt_update->execute()) {
    $_SESSION['swal_icon'] = 'success';
    $_SESSION['swal_title'] = 'Pedido cancelado';
    $_SESSION['swal_message'] = 'El pedido #' . $pedido_id . ' ha sido cancelado correctamente';
} else {
    $_SESSION['swal_icon'] = 'error';
    $_SESSION['swal_title'] = 'Error';
    $_SESSION['swal_message'] = 'No se pudo cancelar el pedido';
}

header("Location: detalle_pedido.php?id=" . $pedido_id);
exit();
?>