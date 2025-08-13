<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 'Admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $email = $conn->real_escape_string($_POST['email']);
    $telefono = $conn->real_escape_string($_POST['telefono'] ?? '');
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $tipo_usuario = $conn->real_escape_string($_POST['tipo_usuario']);

    // Verificar si el email ya existe
    $sql = "SELECT id FROM clientes WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['swal_icon'] = 'error';
        $_SESSION['swal_title'] = 'Error';
        $_SESSION['swal_message'] = 'El email ya está registrado';
    } else {
        // Insertar nuevo usuario
        $sql = "INSERT INTO clientes (nombre, email, telefono, contraseña, tipo_usuario) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $nombre, $email, $telefono, $password, $tipo_usuario);

        if ($stmt->execute()) {
            $_SESSION['swal_icon'] = 'success';
            $_SESSION['swal_title'] = 'Éxito';
            $_SESSION['swal_message'] = 'Usuario creado correctamente';
        } else {
            $_SESSION['swal_icon'] = 'error';
            $_SESSION['swal_title'] = 'Error';
            $_SESSION['swal_message'] = 'Error al crear el usuario';
        }
    }
}

header("Location: admin.php#usuarios");
exit();
?>