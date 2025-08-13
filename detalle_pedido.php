<?php
session_start();
include 'conexion.php';

// Verificar si es administrador
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] != 'Admin') {
    $_SESSION['error'] = "Acceso denegado";
    header("Location: login.php");
    exit();
}

// Obtener ID del pedido
if (!isset($_GET['id'])) {
    $_SESSION['error'] = "No se especificó un pedido";
    header("Location: admin.php#pedidos");
    exit();
}

$pedido_id = (int)$_GET['id'];

// Consulta modificada para administradores
$sql_pedido = "SELECT p.*, c.nombre as cliente_nombre, c.email as cliente_email 
               FROM pedidos p 
               JOIN clientes c ON p.cliente_id = c.id 
               WHERE p.id = ?";
$stmt_pedido = $conn->prepare($sql_pedido);
$stmt_pedido->bind_param("i", $pedido_id);
$stmt_pedido->execute();
$result_pedido = $stmt_pedido->get_result();

if ($result_pedido->num_rows == 0) {
    $_SESSION['error'] = "Pedido no encontrado";
    header("Location: admin.php#pedidos");
    exit();
}

$pedido = $result_pedido->fetch_assoc();

// Resto del código para mostrar detalles...
// Obtener detalles del pedido 
$sql_detalles = "SELECT dp.*, pr.nombre as producto_nombre, pr.precio as precio_unitario, pr.imagen as producto_imagen
                 FROM detalles_pedido dp 
                 JOIN productos pr ON dp.producto_id = pr.id 
                 WHERE dp.pedido_id = ?";
$stmt_detalles = $conn->prepare($sql_detalles);

if (!$stmt_detalles) {
    die("Error al preparar la consulta: " . $conn->error);
}

$stmt_detalles->bind_param("i", $pedido_id);
$stmt_detalles->execute();
$result_detalles = $stmt_detalles->get_result();

// Inicializar $detalles como array vacío por defecto
$detalles = [];

if ($result_detalles) {
    $detalles = $result_detalles->fetch_all(MYSQLI_ASSOC);
}

// Calcular total seguro
$total_pedido = 0;
foreach($detalles as $detalle) {
    $total_pedido += $detalle['precio_unitario'] * $detalle['cantidad'];
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Pedido #<?= $pedido_id ?> - GastroBar</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
        }
        .order-header {
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .badge-estado {
            font-size: 0.9rem;
            padding: 0.5em 0.75em;
        }
        .info-card {
            border-left: 4px solid #0d6efd;
        }
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <!-- Barra de Navegación -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container">
            <a href="index.php" class="navbar-brand">
                <span class="text-primary">Gastro</span>Bar
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbar-star" aria-controls="navbar-star" aria-expanded="false"
                    aria-label="Toggler navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbar-star">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#services">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="agendacita.html">Agenda cita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="perfil.php"><?= htmlspecialchars($_SESSION['usuario'] ?? 'Usuario') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="carrito.php">
                            <i class="bi bi-cart3"></i> Carrito
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container" style="padding-top: 80px;">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h1 class="mb-0">Detalle del Pedido #<?= $pedido_id ?></h1>
                    <a href="admin.php" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Volver al panel de administrador
                    </a>
                </div>
                <hr>
            </div>
        </div>

        <?php if(isset($_SESSION['mensaje'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['mensaje']) ?></div>
            <?php unset($_SESSION['mensaje']); ?>
        <?php endif; ?>
        
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6 mb-4">
                <div class="card order-header">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-info-circle"></i> Información del Pedido</h5>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Fecha:</strong><br><?= date('d/m/Y H:i', strtotime($pedido['fecha'])) ?></p>
                                <p><strong>Estado:</strong><br>
                                    <span class="badge bg-<?= 
                                        $pedido['estado'] == 'completado' ? 'success' : 
                                        ($pedido['estado'] == 'cancelado' ? 'danger' : 'warning') 
                                    ?> badge-estado">
                                        <?= ucfirst($pedido['estado']) ?>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Total:</strong><br>$<?= number_format($pedido['total'], 2) ?></p>
                                <p><strong>N° de Productos:</strong><br><?= count($detalles) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card info-card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-person"></i> Información del Cliente</h5>
                        <hr>
                        <p><strong>Nombre:</strong><br><?= htmlspecialchars($pedido['cliente_nombre']) ?></p>
                        <p><strong>Email:</strong><br><?= htmlspecialchars($pedido['cliente_email']) ?></p>
                        <?php if($pedido['estado'] == 'pendiente'): ?>
                            <div class="mt-3">
                                <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelarModal">
                                    <i class="bi bi-x-circle"></i> Cancelar Pedido
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-list-check"></i> Productos del Pedido</h5>
                        <hr>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Precio Unitario</th>
                                        <th>Cantidad</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($detalles as $detallelle): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= htmlspecialchars($detalle['producto_imagen']) ?>" 
                                                         alt="<?= htmlspecialchars($detalle['producto_nombre']) ?>" 
                                                         class="product-img me-3">
                                                    <div>
                                                        <h6 class="mb-0"><?= htmlspecialchars($detalle['producto_nombre']) ?></h6>
                                                        
                                                    </div>
                                                </div>
                                            </td>
                                            <td>$<?= number_format($detalle['precio_unitario'], 2) ?></td>
                                            <td><?= $detalle['cantidad'] ?></td>
                                            <td>$<?= number_format($detalle['precio_unitario'] * $detalle['cantidad'], 2) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr class="total-row">
                                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                        <td>$<?= number_format($pedido['total'], 2) ?></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if($pedido['estado'] == 'pendiente'): ?>
        <!-- Modal para cancelar pedido -->
        <div class="modal fade" id="cancelarModal" tabindex="-1" aria-labelledby="cancelarModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelarModalLabel">Cancelar Pedido</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro que deseas cancelar este pedido?</p>
                        <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <form action="cancelar_pedido.php" method="post">
                            <input type="hidden" name="pedido_id" value="<?= $pedido_id ?>">
                            <button type="submit" class="btn btn-danger">Confirmar Cancelación</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Pie de Página -->
    <footer class="bg-dark p-2 text-center mt-5">
        <div class="container">
            <p class="text-white">Todos los derechos reservados &copy; <?= date('Y') ?></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    // Mostrar mensajes con SweetAlert
    <?php if(isset($_SESSION['swal_message'])): ?>
        Swal.fire({
            icon: '<?= $_SESSION['swal_icon'] ?>',
            title: '<?= $_SESSION['swal_title'] ?>',
            text: '<?= $_SESSION['swal_message'] ?>',
            confirmButtonColor: '#0d6efd'
        });
        <?php 
        unset($_SESSION['swal_message']);
        unset($_SESSION['swal_icon']);
        unset($_SESSION['swal_title']);
        ?>
    <?php endif; ?>
    </script>
</body>
</html>