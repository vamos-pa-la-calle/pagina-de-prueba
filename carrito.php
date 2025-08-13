<?php
session_start();
include 'conexion.php';

// Verificar sesión de usuario
$usuario_id = isset($_SESSION['usuario_id']) ? $_SESSION['usuario_id'] : null;
$usuario_nombre = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;

// Procesar compra si se envió el parámetro
if(isset($_GET['comprar']) && !empty($_SESSION['carrito']) && $usuario_id) {
    header("Location: procesar_pago.php");
    exit();
}

// Obtener historial de pedidos si el usuario está logueado
$historial_pedidos = array();
if ($usuario_id) {
    $sql = "SELECT p.* FROM pedidos p WHERE p.cliente_id = ? ORDER BY p.fecha DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $historial_pedidos = $result->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras - GastroBar</title>
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
        .quantity-control {
            width: 60px;
            text-align: center;
            display: inline-block;
        }
        .empty-cart {
            min-height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .sticky-summary {
            position: sticky;
            top: 20px;
        }
        .cart-table th {
            background-color: #f8f9fa;
        }
        .badge.bg-danger {
            font-size: 0.7rem;
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
                    <?php if (!$usuario_nombre): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Entrar</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="perfil.php"><?php echo htmlspecialchars($usuario_nombre); ?></a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle position-relative" href="#" id="navbarDropdownCart" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-cart3" style="font-size: 1.2rem;"></i>
                            <?php if(isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?php 
                                        $totalItems = array_sum(array_column($_SESSION['carrito'], 'cantidad'));
                                        echo $totalItems > 9 ? '9+' : $totalItems;
                                    ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-cart" aria-labelledby="navbarDropdownCart" style="min-width: 300px; padding: 0;">
                            <div class="p-3">
                                <h6 class="dropdown-header">Tu Carrito</h6>
                                <?php if(empty($_SESSION['carrito'])): ?>
                                    <div class="dropdown-item-text text-center py-3">Carrito vacío</div>
                                <?php else: ?>
                                    <div class="cart-items" style="max-height: 300px; overflow-y: auto;">
                                        <?php 
                                        $total = 0;
                                        foreach($_SESSION['carrito'] as $id => $item): 
                                            $subtotal = $item['precio'] * $item['cantidad'];
                                            $total += $subtotal;
                                        ?>
                                            <div class="dropdown-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= htmlspecialchars($item['imagen']) ?>" alt="<?= htmlspecialchars($item['nombre']) ?>" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                                    <div>
                                                        <h6 class="mb-0"><?= htmlspecialchars($item['nombre']) ?></h6>
                                                        <small><?= $item['cantidad'] ?> x $<?= number_format($item['precio'], 2) ?></small>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="fw-bold">$<?= number_format($subtotal, 2) ?></span>
                                                    <a href="eliminar_carrito.php?id=<?= $id ?>" class="text-danger ms-2">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-3 px-2">
                                        <h5 class="mb-0">Total:</h5>
                                        <h5 class="mb-0 fw-bold">$<?= number_format($total, 2) ?></h5>
                                    </div>
                                    <div class="d-grid gap-2 mt-3">
                                        <a href="carrito.php" class="btn btn-primary">Ver Carrito</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container" style="padding-top: 80px;">
        <div class="row">
            <div class="col-12">
                <h1 class="mb-4">Tu Carrito de Compras</h1>
                
                <?php if(isset($_SESSION['mensaje'])): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($_SESSION['mensaje']) ?></div>
                    <?php unset($_SESSION['mensaje']); ?>
                <?php endif; ?>
                
                <?php if(isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']) ?></div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-lg-8">
                        <?php if(empty($_SESSION['carrito'])): ?>
                            <div class="empty-cart bg-light rounded p-5 text-center">
                                <i class="bi bi-cart-x display-4 text-muted mb-3"></i>
                                <h3 class="text-muted">Tu carrito está vacío</h3>
                                <p class="text-muted">Agrega productos desde nuestro menú</p>
                                <a href="index.php" class="btn btn-primary mt-3">Ver Productos</a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table cart-table">
                                    <thead>
                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio</th>
                                            <th>Cantidad</th>
                                            <th>Subtotal</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $total = 0;
                                        foreach($_SESSION['carrito'] as $id => $item): 
                                            $subtotal = $item['precio'] * $item['cantidad'];
                                            $total += $subtotal;
                                        ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?= htmlspecialchars($item['imagen']) ?>" alt="<?= htmlspecialchars($item['nombre']) ?>" class="product-img me-3">
                                                        <div>
                                                            <h6 class="mb-0"><?= htmlspecialchars($item['nombre']) ?></h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>$<?= number_format($item['precio'], 2) ?></td>
                                                <td>
                                                    <form action="actualizar_cantidad.php" method="post" class="d-inline-flex align-items-center">
                                                        <input type="hidden" name="producto_id" value="<?= $id ?>">
                                                        <input type="number" name="cantidad" value="<?= $item['cantidad'] ?>" min="1" class="form-control quantity-control me-2">
                                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                                            <i class="bi bi-arrow-clockwise"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                                <td>$<?= number_format($subtotal, 2) ?></td>
                                                <td>
                                                    <a href="eliminar_carrito.php?id=<?= $id ?>" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="d-flex justify-content-between mt-3">
                                <a href="index.php" class="btn btn-outline-primary">
                                    <i class="bi bi-arrow-left"></i> Seguir comprando
                                </a>
                                <a href="eliminar_carrito.php?vaciar=1" class="btn btn-outline-danger">
                                    <i class="bi bi-trash"></i> Vaciar carrito
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Historial de pedidos -->
                        <?php if($usuario_id && !empty($historial_pedidos)): ?>
                            <div class="mt-5">
                                <h3><i class="bi bi-clock-history"></i> Tu historial de pedidos</h3>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>N° Pedido</th>
                                                <th>Fecha</th>
                                                <th>Estado</th>
                                                <th>Total</th>
                                                
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($historial_pedidos as $pedido): ?>
                                                <tr>
                                                    <td>#<?= $pedido['id'] ?></td>
                                                    <td><?= date('d/m/Y H:i', strtotime($pedido['fecha'])) ?></td>
                                                    <td>
                                                        <span class="badge bg-<?= 
                                                            $pedido['estado'] == 'completado' ? 'success' : 
                                                            ($pedido['estado'] == 'cancelado' ? 'danger' : 'warning') 
                                                        ?>">
                                                            <?= ucfirst($pedido['estado']) ?>
                                                        </span>
                                                    </td>
                                                    <td>$<?= number_format($pedido['total'], 2) ?></td>
                                                   
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if(!empty($_SESSION['carrito'])): ?>
                    <div class="col-lg-4">
                        <div class="card shadow-sm sticky-summary">
                            <div class="card-body">
                                <h5 class="card-title mb-4">Resumen de Compra</h5>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span>$<?= number_format($total, 2) ?></span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Envío:</span>
                                    <span>$0.00</span>
                                </div>
                                
                                <hr>
                                
                                <div class="d-flex justify-content-between fw-bold mb-4">
                                    <span>Total:</span>
                                    <span>$<?= number_format($total, 2) ?></span>
                                </div>
                                
                                <?php if($usuario_id): ?>
                                    <a href="procesar_pago.php" class="btn btn-success w-100 py-2 mb-2">
                                        <i class="bi bi-credit-card"></i> Finalizar Compra
                                    </a>
                                <?php else: ?>
                                    <div class="alert alert-warning mb-3">
                                        Debes iniciar sesión para completar la compra
                                    </div>
                                    <a href="login.php" class="btn btn-primary w-100 py-2">
                                        <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
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
    // Mostrar confirmación al eliminar productos
    document.querySelectorAll('a[href^="eliminar_carrito.php"]').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Eliminar producto?',
                text: "¿Estás seguro de que quieres eliminar este producto del carrito?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = this.href;
                }
            });
        });
    });
    
    // Confirmación para vaciar carrito
    const vaciarCarrito = document.querySelector('a[href="eliminar_carrito.php?vaciar=1"]');
    if(vaciarCarrito) {
        vaciarCarrito.addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: '¿Vaciar carrito?',
                text: "¿Estás seguro de que quieres eliminar todos los productos del carrito?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, vaciar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = this.href;
                }
            });
        });
    }
    </script>
</body>
</html>