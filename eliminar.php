<?php

session_start();

include "conexion.php";


$Id = $_POST['id'];

// Obtener datos del usuario
$stmt = $conn->prepare("SELECT * FROM clientes WHERE id = ?");
$stmt->bind_param("i", $Id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $sql = "DELETE FROM clientes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $Id);
    $stmt->execute();

    if ($stmt->execute()) {

        $_SESSION['usuario'] = $email;
        header("Location: dashboardAdmin.php");
        exit();

    } else {
        echo "Error al actualizar: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
