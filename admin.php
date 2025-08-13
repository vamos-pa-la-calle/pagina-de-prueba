<?php
session_start();
include 'conexion.php';

// Verificación de administrador obligatoria
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] != 'Admin') {
    header("Location: login.php");
    exit();
}

// Definir la variable para uso en el HTML
$usuario = $_SESSION['usuario']; // Esto evitará el warning
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - GastroBar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        body {
            padding-top: 70px;
            background-color: #f8f9fa;
        }
        .sidebar {
            position: fixed;
            top: 60px;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 20px 0;
            background-color: #343a40;
            color: white;
        }
        .sidebar-sticky {
            position: relative;
            height: calc(100vh - 60px);
            overflow-x: hidden;
            overflow-y: auto;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
            padding: 10px 20px;
        }
        .sidebar .nav-link:hover {
            color: rgba(255, 255, 255, 1);
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.2);
        }
        .main-content {
            margin-left: 220px;
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .table-responsive {
            overflow-x: auto;
        }
        .badge-admin {
            background-color: #dc3545;
        }
        .badge-user {
            background-color: #28a745;
        }
        .action-btns .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <!-- Barra de Navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <span class="text-primary">Gastro</span>Bar - Panel Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarAdmin">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarAdmin">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($usuario) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="admin.php"><i class="bi bi-person"></i> Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="cerrar.php"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3 text-white" style="width: 220px;">
        <div class="sidebar-sticky">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="#" id="tab-dashboard">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="tab-usuarios">
                        <i class="bi bi-people me-2"></i> Usuarios
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="tab-pedidos">
                        <i class="bi bi-cart3 me-2"></i> Pedidos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="tab-citas">
                        <i class="bi bi-calendar-check me-2"></i> Citas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="tab-productos">
                        <i class="bi bi-cup-straw me-2"></i> Productos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" id="tab-comentarios">
                        <i class="bi bi-chat-left-text me-2"></i> Comentarios
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="main-content">
        <!-- Dashboard -->
        <div id="dashboard-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>

            <div class="row">
                <!-- Resumen de Usuarios -->
                <div class="col-md-3">
                    <div class="card text-white bg-primary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Usuarios</h6>
                                    <?php
                                    $sql = "SELECT COUNT(*) as total FROM clientes";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h2 class="mb-0"><?= $row['total'] ?></h2>
                                </div>
                                <i class="bi bi-people fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen de Pedidos -->
                <div class="col-md-3">
                    <div class="card text-white bg-success">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Pedidos</h6>
                                    <?php
                                    $sql = "SELECT COUNT(*) as total FROM pedidos";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h2 class="mb-0"><?= $row['total'] ?></h2>
                                </div>
                                <i class="bi bi-cart3 fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen de Citas -->
                <div class="col-md-3">
                    <div class="card text-white bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Citas</h6>
                                    <?php
                                    $sql = "SELECT COUNT(*) as total FROM citas";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h2 class="mb-0"><?= $row['total'] ?></h2>
                                </div>
                                <i class="bi bi-calendar-check fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resumen de Productos -->
                <div class="col-md-3">
                    <div class="card text-white bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Productos</h6>
                                    <?php
                                    $sql = "SELECT COUNT(*) as total FROM productos";
                                    $result = $conn->query($sql);
                                    $row = $result->fetch_assoc();
                                    ?>
                                    <h2 class="mb-0"><?= $row['total'] ?></h2>
                                </div>
                                <i class="bi bi-cup-straw fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Últimos Pedidos -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5>Últimos Pedidos</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Cliente</th>
                                            <th>Fecha</th>
                                            <th>Total</th>
                                            <th>Estado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT p.id, p.fecha, p.total, p.estado, c.nombre as cliente 
                                                FROM pedidos p 
                                                JOIN clientes c ON p.cliente_id = c.id 
                                                ORDER BY p.fecha DESC LIMIT 5";
                                        $result = $conn->query($sql);
                                        while ($row = $result->fetch_assoc()):
                                        ?>
                                        <tr>
                                            <td>#<?= $row['id'] ?></td>
                                            <td><?= htmlspecialchars($row['cliente']) ?></td>
                                            <td><?= date('d/m/Y H:i', strtotime($row['fecha'])) ?></td>
                                            <td>$<?= number_format($row['total'], 2) ?></td>
                                            <td>
                                                <span class="badge bg-<?= 
                                                    $row['estado'] == 'completado' ? 'success' : 
                                                    ($row['estado'] == 'cancelado' ? 'danger' : 'warning') 
                                                ?>">
                                                    <?= ucfirst($row['estado']) ?>
                                                </span>
                                            </td>
                                            <td class="action-btns">
                                                <a href="detalle_pedido.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <?php if($row['estado'] == 'pendiente'): ?>
                                                    <a href="actualizar_estado_pedido.php?id=<?= $row['id'] ?>&estado=completado" class="btn btn-sm btn-success">
                                                        <i class="bi bi-check-circle"></i>
                                                    </a>
                                                    <a href="actualizar_estado_pedido.php?id=<?= $row['id'] ?>&estado=cancelado" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-x-circle"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido de Usuarios (oculto inicialmente) -->
        <div id="usuarios-content" style="display: none;">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestión de Usuarios</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoUsuarioModal">
                    <i class="bi bi-plus-circle"></i> Nuevo Usuario
                </button>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT id, nombre, email, telefono, tipo_usuario FROM clientes ORDER BY id DESC";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                                    <td><?= htmlspecialchars($row['email']) ?></td>
                                    <td><?= htmlspecialchars($row['telefono']) ?></td>
                                    <td>
                                        <span class="badge <?= $row['tipo_usuario'] == 'Admin' ? 'badge-admin' : 'badge-user' ?>">
                                            <?= $row['tipo_usuario'] ?>
                                        </span>
                                    </td>
                                    <td class="action-btns">
                                        <button class="btn btn-sm btn-warning editar-usuario" 
                                                data-id="<?= $row['id'] ?>"
                                                data-nombre="<?= htmlspecialchars($row['nombre']) ?>"
                                                data-email="<?= htmlspecialchars($row['email']) ?>"
                                                data-telefono="<?= htmlspecialchars($row['telefono']) ?>"
                                                data-tipo="<?= $row['tipo_usuario'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <?php if($row['id'] != $_SESSION['usuario_id']): ?>
                                            <a href="eliminar_usuario.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"  data-confirm="¿Estás seguro de eliminar este usuario?">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido de Pedidos (oculto inicialmente) -->
        <div id="pedidos-content" style="display: none;">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestión de Pedidos</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT p.id, p.fecha, p.total, p.estado, c.nombre as cliente 
                                        FROM pedidos p 
                                        JOIN clientes c ON p.cliente_id = c.id 
                                        ORDER BY p.fecha DESC";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>#<?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['cliente']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['fecha'])) ?></td>
                                    <td>$<?= number_format($row['total'], 2) ?></td>
                                    <td>
                                        <span class="badge bg-<?= 
                                            $row['estado'] == 'completado' ? 'success' : 
                                            ($row['estado'] == 'cancelado' ? 'danger' : 'warning') 
                                        ?>">
                                            <?= ucfirst($row['estado']) ?>
                                        </span>
                                    </td>
                                    <td class="action-btns">
                                        <a href="detalle_pedido.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <?php if($row['estado'] == 'pendiente'): ?>
                                            <a href="actualizar_estado_pedido.php?id=<?= $row['id'] ?>&estado=completado" class="btn btn-sm btn-success">
                                                <i class="bi bi-check-circle"></i>
                                            </a>
                                            <a href="actualizar_estado_pedido.php?id=<?= $row['id'] ?>&estado=cancelado" class="btn btn-sm btn-danger">
                                                <i class="bi bi-x-circle"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido de Citas (oculto inicialmente) -->
        <div id="citas-content" style="display: none;">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestión de Citas</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Fecha</th>
                                    <th>Hora</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM citas ORDER BY fecha, hora DESC";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                                    <td><?= htmlspecialchars($row['email'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars($row['telefono']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['fecha'])) ?></td>
                                    <td><?= date('H:i', strtotime($row['hora'])) ?></td>
                                    <td class="action-btns">
                                        <a href="eliminar_cita.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"  data-confirm="¿Estás seguro de eliminar esta cita?">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido de Productos (oculto inicialmente) -->
        <div id="productos-content" style="display: none;">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestión de Productos</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nuevoProductoModal">
                    <i class="bi bi-plus-circle"></i> Nuevo Producto
                </button>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Imagen</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Precio</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM productos ORDER BY id DESC";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td>
                                        <img src="<?= htmlspecialchars($row['imagen']) ?>" alt="<?= htmlspecialchars($row['nombre']) ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                                    <td><?= htmlspecialchars(substr($row['descripcion'], 0, 50)) ?>...</td>
                                    <td>$<?= number_format($row['precio'], 2) ?></td>
                                    <td class="action-btns">
                                        <button class="btn btn-sm btn-warning editar-producto" 
                                                data-id="<?= $row['id'] ?>"
                                                data-nombre="<?= htmlspecialchars($row['nombre']) ?>"
                                                data-descripcion="<?= htmlspecialchars($row['descripcion']) ?>"
                                                data-precio="<?= $row['precio'] ?>"
                                                data-imagen="<?= htmlspecialchars($row['imagen']) ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="eliminar_producto.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"  data-confirm="¿Estás seguro de eliminar este producto?">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenido de Comentarios (oculto inicialmente) -->
        <div id="comentarios-content" style="display: none;">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Gestión de Comentarios</h1>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Comentario</th>
                                    <th>Fecha</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM comentarios ORDER BY fecha_registro DESC";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                                    <td><?= htmlspecialchars($row['email'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars(substr($row['comentario'], 0, 50)) ?>...</td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['fecha_registro'])) ?></td>
                                    <td class="action-btns">
                                        <a href="eliminar_comentario.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"  data-confirm="¿Estás seguro de eliminar este comentario?">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para nuevo usuario -->
    <div class="modal fade" id="nuevoUsuarioModal" tabindex="-1" aria-labelledby="nuevoUsuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nuevoUsuarioModalLabel">Nuevo Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="crear_usuario.php" method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="tipo_usuario" class="form-label">Rol</label>
                            <select class="form-select" id="tipo_usuario" name="tipo_usuario" required>
                                <option value="User">Usuario</option>
                                <option value="Admin">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para editar usuario -->
    <div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-labelledby="editarUsuarioModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarUsuarioModalLabel">Editar Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="actualizar_usuario.php" method="POST">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="edit_nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="edit_telefono" name="telefono">
                        </div>
                        <div class="mb-3">
                            <label for="edit_tipo_usuario" class="form-label">Rol</label>
                            <select class="form-select" id="edit_tipo_usuario" name="tipo_usuario" required>
                                <option value="User">Usuario</option>
                                <option value="Admin">Administrador</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para nuevo producto -->
    <div class="modal fade" id="nuevoProductoModal" tabindex="-1" aria-labelledby="nuevoProductoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="nuevoProductoModalLabel">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="crear_producto.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="prod_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="prod_nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="prod_descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="prod_descripcion" name="descripcion" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="prod_precio" class="form-label">Precio</label>
                            <input type="number" step="0.01" class="form-control" id="prod_precio" name="precio" required>
                        </div>
                        <div class="mb-3">
                            <label for="prod_imagen" class="form-label">Imagen (URL)</label>
                            <input type="text" class="form-control" id="prod_imagen" name="imagen" required>
                            <small class="text-muted">Ejemplo: ./img/producto.jpg</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para editar producto -->
    <div class="modal fade" id="editarProductoModal" tabindex="-1" aria-labelledby="editarProductoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarProductoModalLabel">Editar Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="actualizar_producto.php" method="POST">
                    <input type="hidden" id="edit_prod_id" name="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_prod_nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="edit_prod_nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_prod_descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="edit_prod_descripcion" name="descripcion" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_prod_precio" class="form-label">Precio</label>
                            <input type="number" step="0.01" class="form-control" id="edit_prod_precio" name="precio" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_prod_imagen" class="form-label">Imagen (URL)</label>
                            <input type="text" class="form-control" id="edit_prod_imagen" name="imagen" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <script>
        // Manejo de pestañas
        document.querySelectorAll('.sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remover clase active de todos los links
                document.querySelectorAll('.sidebar .nav-link').forEach(el => {
                    el.classList.remove('active');
                });
                
                // Agregar clase active al link clickeado
                this.classList.add('active');
                
                // Ocultar todos los contenidos
                document.querySelectorAll('.main-content > div').forEach(content => {
                    content.style.display = 'none';
                });
                
                // Mostrar el contenido correspondiente
                const target = this.id.replace('tab-', '') + '-content';
                document.getElementById(target).style.display = 'block';
                
                // Actualizar el hash en la URL para mantener el estado
                window.location.hash = this.id.replace('tab-', '');
            });
        });
        
        // Mostrar contenido basado en el hash de la URL al cargar
        window.addEventListener('DOMContentLoaded', () => {
            const hash = window.location.hash.substring(1);
            const defaultTab = hash || 'dashboard';
            
            // Ocultar todos los contenidos
            document.querySelectorAll('.main-content > div').forEach(content => {
                content.style.display = 'none';
            });
            
            // Mostrar el contenido correspondiente al hash
            document.getElementById(`${defaultTab}-content`).style.display = 'block';
            
            // Activar el tab correspondiente
            document.querySelectorAll('.sidebar .nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.id === `tab-${defaultTab}`) {
                    link.classList.add('active');
                }
            });
        });
        
        // Manejo del modal de edición de usuario
        document.querySelectorAll('.editar-usuario').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');
                const email = this.getAttribute('data-email');
                const telefono = this.getAttribute('data-telefono');
                const tipo = this.getAttribute('data-tipo');
                
                document.getElementById('edit_id').value = id;
                document.getElementById('edit_nombre').value = nombre;
                document.getElementById('edit_email').value = email;
                document.getElementById('edit_telefono').value = telefono;
                document.getElementById('edit_tipo_usuario').value = tipo;
                
                // Mostrar modal
                const modal = new bootstrap.Modal(document.getElementById('editarUsuarioModal'));
                modal.show();
            });
        });
        
        // Manejo del modal de edición de producto
        document.querySelectorAll('.editar-producto').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const nombre = this.getAttribute('data-nombre');
                const descripcion = this.getAttribute('data-descripcion');
                const precio = this.getAttribute('data-precio');
                const imagen = this.getAttribute('data-imagen');
                
                document.getElementById('edit_prod_id').value = id;
                document.getElementById('edit_prod_nombre').value = nombre;
                document.getElementById('edit_prod_descripcion').value = descripcion;
                document.getElementById('edit_prod_precio').value = precio;
                document.getElementById('edit_prod_imagen').value = imagen;
                
                // Mostrar modal
                const modal = new bootstrap.Modal(document.getElementById('editarProductoModal'));
                modal.show();
            });
        });
        
        // Manejo de confirmaciones de eliminación
        document.addEventListener('DOMContentLoaded', function() {
            // Manejo de todas las eliminaciones
            document.querySelectorAll('a.btn-danger').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    const message = this.getAttribute('data-confirm') || '¿Estás seguro de realizar esta acción?';
                    const activeTab = document.querySelector('.sidebar .nav-link.active').id.replace('tab-', '');
                    
                    Swal.fire({
                        title: 'Confirmar',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirigir incluyendo la pestaña activa
                            window.location.href = url + (url.includes('?') ? '&' : '?') + 'redirect=' + activeTab;
                        }
                    });
                });
            });
        }); 
        
        // Mostrar mensajes de SweetAlert si existen
        <?php if(isset($_SESSION['swal_icon'])): ?>
            Swal.fire({
                icon: '<?= $_SESSION['swal_icon'] ?>',
                title: '<?= $_SESSION['swal_title'] ?>',
                text: '<?= $_SESSION['swal_message'] ?>',
                confirmButtonColor: '#0d6efd'
            });
            <?php 
            unset($_SESSION['swal_icon']);
            unset($_SESSION['swal_title']);
            unset($_SESSION['swal_message']);
            ?>
        <?php endif; ?>
   </script>
        
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  </body>
</html>