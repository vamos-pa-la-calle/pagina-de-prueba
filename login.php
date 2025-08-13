<?php
session_start();
include("conexion.php");

// Mostrar mensaje de registro exitoso
if (isset($_SESSION['registro_exitoso']) && $_SESSION['registro_exitoso']) {
    echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
              Swal.fire({
                title: "¡Registro exitoso!",
                text: "Ahora puedes iniciar sesión",
                icon: "success",
                confirmButtonColor: "#28a745"
              });
            });
          </script>';
    unset($_SESSION['registro_exitoso']);
}

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST['email'] ?? '';
    $password_ingresada = $_POST['password'] ?? '';

    // Buscar el usuario en la base de datos
    $sql = "SELECT * FROM clientes WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $fila = $resultado->fetch_assoc();
        $password_guardada = $fila['contraseña'];

        if (password_verify($password_ingresada, $password_guardada)) {
            $_SESSION['usuario'] = $usuario;
            $_SESSION['usuario_id'] = $fila['id'];
            $_SESSION['tipo_usuario'] = $fila['tipo_usuario']; 
            
            if ($fila['tipo_usuario'] == "Admin") {
                header("Location: admin.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            $error_message = "Contraseña incorrecta";
        }
    } else {
        $error_message = "Usuario no encontrado";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Login - Gastrobar</title>
  <!-- SweetAlert 2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
          * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
          }

          body {
            background:url("https://wallpapers.com/images/hd/bar-alcoholic-drinks-6fj3udabe3l4smcp.jpg");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
          }

          .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            position: relative;
          }

          .login-container p {
            margin-bottom: 20px;
            color: #555;
            font-weight: 500;
          }

          input[type="text"],
          input[type="password"],
          input[type="email"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
          }

          button {
            background-color: #3488d6;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
            width: 100%;
          }

          button:hover {
            background-color: #3488d6;
          }

          .cocktail-icon {
            font-size: 40px;
            margin-bottom: 10px;
            color: #ff914d;
          }

          a {
            color: #3488d6;
            text-decoration: none;
            font-weight: 600;
          }

          a:hover {
            text-decoration: underline;
          }

          /* Estilos para el mensaje de error */
          .error-message {
            position: absolute;
            top: -50px;
            left: 0;
            right: 0;
            background-color: #ff6b6b;
            color: white;
            padding: 10px;
            border-radius: 8px;
            margin: 0 auto;
            width: 90%;
            animation: slideDown 0.5s forwards;
            display: <?php echo $error_message ? 'block' : 'none'; ?>;
          }

          @keyframes slideDown {
            from { top: -50px; opacity: 0; }
            to { top: -40px; opacity: 1; }
          }

          .close-error {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
            font-weight: bold;
          }
    </style>


</head>
<body>
  <div class="login-container">
    <div class="cocktail-icon">🍸</div>
    <h1>Gastrobar</h1>
    <p>Iniciar sesión</p>
    <form action="login.php" method="POST">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Contraseña" required>
      <p>¿No tienes cuenta? <a href="registro.php">Regístrate</a></p>
      <button type="submit">Entrar</button>
    </form>
  </div>

  <script>
  // Mostrar mensajes de error
  document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($error_message)): ?>
      Swal.fire({
        title: 'Error',
        text: '<?php echo addslashes($error_message); ?>',
        icon: 'error',
        confirmButtonColor: '#d63447'
      });
    <?php endif; ?>
  });
  </script>
</body>
</html>