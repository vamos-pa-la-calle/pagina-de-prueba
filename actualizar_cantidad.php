<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $producto_id = $_POST['producto_id'];
    $cantidad = (int)$_POST['cantidad'];
    
    if (isset($_SESSION['carrito'][$producto_id]) && $cantidad > 0) {
        $_SESSION['carrito'][$producto_id]['cantidad'] = $cantidad;
        $_SESSION['mensaje'] = "Cantidad actualizada correctamente";
    } else {
        $_SESSION['error'] = "Error al actualizar la cantidad";
    }
}

header('Location: carrito.php');
exit();
?>