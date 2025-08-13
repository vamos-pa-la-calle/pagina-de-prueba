<?php
session_start();

if (isset($_GET['vaciar']) && $_GET['vaciar'] == 1) {
    unset($_SESSION['carrito']);
    $_SESSION['mensaje'] = "Carrito vaciado correctamente";
} elseif (isset($_GET['id'])) {
    $producto_id = $_GET['id'];
    
    if (isset($_SESSION['carrito'][$producto_id])) {
        unset($_SESSION['carrito'][$producto_id]);
        $_SESSION['mensaje'] = "Producto eliminado del carrito";
        
        // Eliminar variable si está vacía
        if (empty($_SESSION['carrito'])) {
            unset($_SESSION['carrito']);
        }
    } else {
        $_SESSION['error'] = "El producto no existe en el carrito";
    }
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'carrito.php'));
exit();
?>