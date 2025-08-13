<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $producto_id = $_POST['producto_id'];
    
    // Validación básica
    if(empty($producto_id)) {
        $_SESSION['error'] = "ID de producto inválido";
        header("Location: index.php");
        exit();
    }

    // Obtener detalles del producto desde la base de datos
    $sql = "SELECT * FROM productos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows == 0) {
        $_SESSION['error'] = "Producto no encontrado";
        header("Location: index.php");
        exit();
    }

    $producto = $result->fetch_assoc();

    // Inicializar carrito si no existe
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = array();
    }

    // Si el producto ya está en el carrito, aumentar cantidad
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id]['cantidad'] += 1;
    } else {
        // Agregar nuevo producto al carrito
        $_SESSION['carrito'][$producto_id] = array(
            'nombre' => $producto['nombre'],
            'precio' => $producto['precio'],
            'cantidad' => 1,
            'imagen' => $producto['imagen']
        );
    }

    // Forzar escritura de la sesión
    session_write_close();

    // Redirigir con éxito
    $_SESSION['mensaje'] = "Producto agregado al carrito";
    header("Location: index.php");
    exit();
} else {
    // Si no es POST, redirigir
    header("Location: index.php");
    exit();
}
?>