<?php

include "conexion.php";

if ($conn->connect_error) {
    die('<div class="alert alert-danger" role="alert">Error de conexión: ' . $conn->connect_error . '</div>');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $email = $_POST["email"];
    $mensaje = $_POST["mensaje"];

    $stmt = $conn->prepare("INSERT INTO contacto (nombre, email, mensaje) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre, $email, $mensaje);

    if ($stmt->execute()) {
        echo '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <title>Mensaje Enviado - GastroBar</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <meta http-equiv="refresh" content="4;URL=index.php">
            <style>
                body {
                    background-color: #f8f9fa;
                }
                .brand {
                    font-weight: bold;
                    font-size: 1.5rem;
                    color: #0d6efd;
                }
                .brand span {
                    color: #000;
                }
            </style>
        </head>
        <body>
            <div class="container text-center mt-5">
                <div class="brand mb-4">Gastro<span>Bar</span></div>
                <div class="alert alert-success shadow p-4 rounded mx-auto" style="max-width: 500px;">
                    <h4 class="alert-heading">¡Mensaje enviado con éxito!</h4>
                    <p>Gracias por contactarnos. Te responderemos pronto.</p>
                    <hr>
                    <p class="mb-0">Serás redirigido en unos segundos. Si no, <a href="index.php" class="alert-link">haz clic aquí</a>.</p>
                </div>
            </div>
        </body>
        </html>';
    } else {
        echo '<div class="alert alert-danger mt-3" role="alert">
                Error al guardar el mensaje: ' . $stmt->error . '
              </div>';
    }

    $stmt->close();
    $conn->close();
}
?>
