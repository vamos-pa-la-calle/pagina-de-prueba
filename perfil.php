<?php
session_start();

include 'conexion.php';

// Verificar si el usuario está logueado
$usuario = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : null;

$sql = "SELECT * FROM clientes WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $fila = $resultado->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tu perfil - Gastrobar</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <!-- BARRA DE NAVEGACIÓN -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container">
            <a href="#" class="navbar-brand">
                <span class="text-primary">Gastro</span>Bar
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-star"
                aria-controls="navbar-star" aria-expanded="false" aria-label="Toggler navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbar-star">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cerrar.php">Cerrar sesion</a>
                    </li>
                </ul>
                
            </div>
        </div>
    </nav>

    <!-- AGENDA TU CITA -->
    <section class="pt-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-8 offset-md-2">
                    <h1 class="text-center text-primary">Tu perfil</h1>
                    <p class="text-center mt-3">
                        Aqui puedes ver y editar tus datos.
                    </p>
                    <form class="mt-4" action="actualizarUser.php" method="POST">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre completo</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="<?php echo $fila["nombre"]; ?>" >
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="<?php echo $fila["email"]; ?>" >
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Número de teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="<?php echo $fila["telefono"]; ?>" >
                        </div>
                        <br><br><br>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary">Actualizar Informacion</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <br><br><br>

    <!-- PIE DE PÁGINA -->
    <footer class="bg-dark p-2 text-center">
        <div class="container">
            <p class="text-white">Todos los derechos reservados &copy; Gastrobar</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>

</body>

</html>
