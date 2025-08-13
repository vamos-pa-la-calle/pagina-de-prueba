<?php
session_start();

include 'conexion.php';

// Verificar si el usuario está logueado
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="GMT-5">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gastrobar</title>
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" 
        rel="stylesheet" 
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" 
        crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">

</head>


<body>

    <!-- BARRA DE NAVEGACION -->

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
                        <a class="nav-link" href="#services">Servicios</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="agendacita.html">Agenda cita</a>
                    </li>
                    <?php if (!$usuario): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Entrar</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="perfil.php"><?php echo htmlspecialchars($usuario); ?></a>
                    </li>
                    <!-- Carrito Dropdown -->
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
                                                    <img src="<?= $item['imagen'] ?>" alt="<?= $item['nombre'] ?>" style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                                                    <div>
                                                        <h6 class="mb-0"><?= $item['nombre'] ?></h6>
                                                        <small><?= $item['cantidad'] ?> x $<?= number_format($item['precio'], 2) ?></small>
                                                    </div>
                                                </div>
                                                <div>
                                                    <span class="fw-bold">$<?= number_format($subtotal, 2) ?></span>
                                                    <a href="#" 
                                                                class="text-danger ms-2 btn-eliminar" 
                                                                data-id="<?= $id ?>" 
                                                                data-nombre="<?= htmlspecialchars($item['nombre']) ?>">
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
                                        <?php if(isset($_SESSION['usuario'])): ?>
                                            <a href="carrito.php?comprar=1" class="btn btn-success">Finalizar Compra</a>
                                        <?php else: ?>
                                            <a href="login.php" class="btn btn-warning">Iniciar Sesión para Comprar</a>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

<style>
    .dropdown-cart {
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }
    .cart-items::-webkit-scrollbar {
        width: 5px;
    }
    .cart-items::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .cart-items::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
</style>

    <!-- CARRUSEL -->
    <style>
        
        .carousel-inner img {
            height: 600px; 
            object-fit: cover; 
            width: 100%; 
        }
    
       
        .carousel-caption {
            background: linear-gradient(to bottom, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.7)); 
            color: #fff;
            padding: 20px;
            border-radius: 10px;
        }
    
        
        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            filter: invert(1); 
        }
    
       
        .carousel-caption h5 {
            font-size: 2rem;
            font-weight: bold;
            text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.7);
        }
    
        .carousel-caption p {
            font-size: 1.2rem;
            margin-top: 10px;
        }
    
        .btn-primary {
            font-size: 1rem;
            padding: 10px 20px;
            border-radius: 5px;
        }
    </style>
    
    
    <div id="carouselE1" class="carousel slide" data-bs-ride="carousel">
    
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselE1" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselE1" data-bs-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-bs-target="#carouselE1" data-bs-slide-to="2" aria-label="Slide 3"></button>
            <button type="button" data-bs-target="#carouselE1" data-bs-slide-to="3" aria-label="Slide 4"></button>
        </div>
    
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="./img/bloodymary.webp" class="d-block w-100" alt="Car 1">
                <div class="carousel-caption">
                    <h5>Los mejores cócteles</h5>
                    <p><a href="mejoresCT.html" class="btn btn-primary mt-3">CLICK AQUI</a></p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="./img/whiskeysour-.jpg" class="d-block w-100" alt="Car 2">
                <div class="carousel-caption">
                    <h5>Los mejores cócteles</h5>
                    <p><a href="mejoresCT.html" class="btn btn-primary mt-3">CLICK AQUI</a></p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="./img/caipirinha.webp" class="d-block w-100" alt="Car 3">
                <div class="carousel-caption">
                    <h5>Los mejores cócteles</h5>
                    <p><a href="mejoresCT.html" class="btn btn-primary mt-3">CLICK AQUI</a></p>
                </div>
            </div>
            <div class="carousel-item">
                <img src="./img/cosmopolitan1.jpg" class="d-block w-100" alt="Car 4">
                <div class="carousel-caption">
                    <h5>Los mejores cócteles</h5>
                    <p><a href="mejoresCT.html" class="btn btn-primary mt-3">CLICK AQUI</a></p>
                </div>
            </div>
        </div>
    
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselE1" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselE1" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
    

    <!-- SERVICIOS -->
   
    <section id="services" class="services">
    <div class="container">
        <div class="row">
            <!-- Sobre nosotros -->
            <div class="col-12 col-md-6 col-lg-6 mb-3">
                <div class="card text-white text-center bg-car1 pb-2">
                    <div class="card-body">
                        <h3>Sobre nosotros</h3>
                        <p></p>
                        <a href="nosotros.html">
                            <button class="btn bg-primary text-white">Leer más</button>
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Comentarios -->
            <div class="col-12 col-md-6 col-lg-6 mb-3">
                <div class="card text-white text-center bg-car3 pb-2">
                    <div class="card-body">
                        <h3>Comentarios</h3>
                        <p></p>
                        <a href="seccion_comentarios.html">
                            <button class="btn bg-primary text-white">Leer más</button>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>  

    <!-- CSS DE OS PRODUCTOS -->
    <style>

        .product .card {
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .product .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .product .card-img-top {
            height: 180px;
            object-fit: cover;
            width: 100%;
        }
        
        .product .card-body {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            padding: 1.25rem;
        }
        
        .product .card-title {
            margin-bottom: 0.75rem;
        }
        
        .product .card-text {
            flex-grow: 1;
            margin-bottom: 1rem;
        }
        
        .price-container {
            min-height: 40px; 
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .product .price {
            color: #007bff;
            font-size: 1.2rem;
            font-weight: bold;
            margin: 0;
            width: 100%;
        }
        
        .product .btn {
            margin-top: auto;
            width: 100%;
        }
    </style>
    
    <!-- PRODUCTOS -->
    <section class="product py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="section-header text-center pb-5">
                        <h2>Productos</h2>
                        <p>No te pierdas nuestra gran variedad de CÓCTELES.</p>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php
                // Consulta para obtener los productos de la base de datos
                $sql = "SELECT * FROM productos";
                $result = $conn->query($sql);
                
                if ($result->num_rows > 0) {
                    while($producto = $result->fetch_assoc()) {
                ?>
                    <div class="col-12 col-md-6 col-lg-3 mb-4">
                        <div class="card h-100 shadow-sm">
                            <div class="text-center pt-3">
                                <img src="<?php echo $producto['imagen']; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($producto['nombre']); ?>" style="width: auto; max-height: 150px;">
                            </div>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($producto['nombre']); ?></h5>
                                <p class="card-text">
                                    <?php echo htmlspecialchars($producto['descripcion']); ?>
                                </p>
                                <div class="price-container">
                                    <p class="price">$<?php echo number_format($producto['precio'], 2); ?> COP</p>
                                </div>
                                <form action="agregar_carrito.php" method="post">
                                    <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                    <input type="hidden" name="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                    <input type="hidden" name="precio" value="<?php echo $producto['precio']; ?>">
                                    <input type="hidden" name="imagen" value="<?php echo $producto['imagen']; ?>">
                                    <button type="submit" class="btn btn-primary w-100">Agregar al carrito</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php
                    }
                } else {
                    echo '<div class="col-12"><div class="alert alert-info">No hay productos disponibles</div></div>';
                }
                ?>
            </div>
        </div>
    </section>
        
    <!-- CONTACTO-->
   <section class="py-5 bg-white" id="contacto">
    <div class="container">
        <div class="text-center mb-4">
        <h2 class="fw-bold">Contacta con nosotros</h2>
        <p class="text-muted">No dudes en escribirnos si tienes alguna duda o inquietud, estamos para ti.</p>
        </div>
        <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <form action="guardar_contacto.php" method="POST" class="bg-light p-4 rounded shadow-sm">
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" name="nombre" id="nombre" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo electrónico</label>
                <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="mensaje" class="form-label">Mensaje</label>
                <textarea name="mensaje" id="mensaje" rows="4" class="form-control" required></textarea>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">Enviar</button>
            </div>
            </form>
        </div>
        </div>
    </div>
   </section>

    <!-- PIE DE PAGINA   -->
    <footer class="bg-dark p-2 text-center">
        <div class="container">
            <p class="text-white">todos los derechos reservados</p>
        </div>

    </footer>

    <script 
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" 
        crossorigin="anonymous"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar clic en botones de eliminar
            document.querySelectorAll('.btn-eliminar').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.getAttribute('data-id');
                    const nombre = this.getAttribute('data-nombre');
                    
                    Swal.fire({
                        title: '¿Eliminar producto?',
                        html: `¿Estás seguro que deseas eliminar <b>${nombre}</b> del carrito?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d63447',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                       
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `eliminar_carrito.php?id=${id}`;
                        }
                    });
                });
            });
        });
        </script>
    </body>
</html>
