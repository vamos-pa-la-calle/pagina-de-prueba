<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include("conexion.php");

    // Validar campos requeridos
    if (
        empty($_POST['nombre']) ||
        empty($_POST['email']) ||
        empty($_POST['telefono']) ||
        empty($_POST['fecha']) ||
        empty($_POST['hora'])
    ) {
        mostrarMensaje("Faltan datos obligatorios.", "danger");
        exit;
    }

    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $comentarios = isset($_POST['comentarios']) ? trim($_POST['comentarios']) : "";
    $servicio = "Desde web";

    $sql = "INSERT INTO citas (nombre, email, telefono, fecha, hora, servicio, mensaje)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        mostrarMensaje("Error en la preparación de la consulta: " . $conn->error, "danger");
        exit;
    }

    $stmt->bind_param("sssssss", $nombre, $email, $telefono, $fecha, $hora, $servicio, $comentarios);

    if ($stmt->execute()) {
        mostrarMensaje("¡Cita agendada con éxito! En breve nos pondremos en contacto para confirmar.", "success", true);
    } else {
        mostrarMensaje("Error al agendar cita: " . $conn->error, "danger");
    }

    $stmt->close();
    $conn->close();
} else {
    mostrarMensaje("Método no permitido.", "warning");
}

function mostrarMensaje($mensaje, $tipo = "info", $redirigir = false) {
    echo '
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>GastroBar - Confirmación</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        ' . ($redirigir ? '<meta http-equiv="refresh" content="4;URL=agendacita.html">' : '') . '
        <style>
            body { background-color: #f8f9fa; }
            .brand { font-weight: bold; font-size: 1.5rem; color: #0d6efd; }
            .brand span { color: #000; }
        </style>
    </head>
    <body>
        <div class="container text-center mt-5">
            <div class="brand mb-4">Gastro<span>Bar</span></div>
            <div class="alert alert-' . $tipo . ' shadow p-4 rounded mx-auto" style="max-width: 500px;">
                <h4 class="alert-heading">' . ($tipo == "success" ? "¡Éxito!" : "Atención") . '</h4>
                <p>' . htmlspecialchars($mensaje) . '</p>
                ' . ($redirigir ? '<hr><p class="mb-0">Serás redirigido automáticamente. Si no, <a href="agendacita.html" class="alert-link">haz clic aquí</a>.</p>' : '') . '
            </div>
        </div>
    </body>
    </html>';
}
?>
