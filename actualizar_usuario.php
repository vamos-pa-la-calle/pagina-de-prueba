<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 'Admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = (int)$_POST['id'];
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $email = $conn->real_escape_string($_POST['email']);
    $telefono = $conn->real_escape_string($_POST['telefono'] ?? '');
    $tipo_usuario = $conn->real_escape_string($_POST['tipo_usuario']);

    // Verificar si el email ya existe (excluyendo el usuario actual)
    $sql = "SELECT id FROM clientes WHERE email = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $email, $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['swal_icon'] = 'error';
        $_SESSION['swal_title'] = 'Error';
        $_SESSION['swal_message'] = 'El email ya está registrado por otro usuario';
    } else {
        // Actualizar usuario
        $sql = "UPDATE clientes SET nombre = ?, email = ?, telefono = ?, tipo_usuario = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nombre, $email, $telefono, $tipo_usuario, $id);

        if ($stmt->execute()) {
            $_SESSION['swal_icon'] = 'success';
            $_SESSION['swal_title'] = 'Éxito';
            $_SESSION['swal_message'] = 'Usuario actualizado correctamente';
        } else {
            $_SESSION['swal_icon'] = 'error';
            $_SESSION['swal_title'] = 'Error';
            $_SESSION['swal_message'] = 'Error al actualizar el usuario';
        }
    }
}

header("Location: admin.php#usuarios");
exit();
?>