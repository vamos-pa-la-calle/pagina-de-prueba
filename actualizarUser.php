<?php

session_start();

include "conexion.php";


$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;

// Obtener datos del usuario
$stmt = $conn->prepare("SELECT * FROM clientes WHERE email = ?");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener datos actuales
    $sql_select = "SELECT nombre,email,telefono FROM clientes WHERE email = ?";
    $stmt_select = $conn->prepare($sql_select);
    $stmt_select->bind_param("s", $usuario);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    $usuario_actual = $result->fetch_assoc();
    $stmt_select->close();
    
    // Nuevos datos (o mantener los anteriores si están vacíos)
    $nombre = !empty($_POST['nombre']) ? $_POST['nombre'] : $usuario_actual['nombre'];
    $email = !empty($_POST['email']) ? $_POST['email'] : $usuario_actual['email'];
    $telefono = !empty($_POST['telefono']) ? $_POST['telefono'] : $usuario_actual['telefono'];

    // Actualizar datos
    $sql = "UPDATE clientes SET nombre = ?, email = ?, telefono = ? WHERE email = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombre, $email, $telefono,$usuario);

    if ($stmt->execute()) {

        $_SESSION['usuario'] = $email;
        header("Location: perfil.php");
        exit();

    } else {
        echo "Error al actualizar: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
