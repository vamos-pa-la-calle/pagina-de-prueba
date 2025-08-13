<?php
session_start();
include("conexion.php");

// Limpiar mensajes anteriores
unset($_SESSION['registro_error']);
unset($_SESSION['registro_exitoso']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['usuario'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $password_plana = $_POST['password'] ?? '';
    $tipo_usuario = "Usuario";

    // Validaciones básicas
    if(empty($nombre) || empty($email) || empty($telefono) || empty($password_plana)) {
        $_SESSION['registro_error'] = "Todos los campos son obligatorios";
        header("Location: registro.php");
        exit();
    }

    // Encriptar la contraseña
    $contraseña = password_hash($password_plana, PASSWORD_DEFAULT);

    // Verificar si el email ya existe
    $sql_check = "SELECT id FROM clientes WHERE email = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $email);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $_SESSION['registro_error'] = "El email ya está registrado";
        header("Location: registro.php");
        exit();
    }

    // Insertar nuevo usuario
    $sql = "INSERT INTO clientes (nombre, email, telefono, contraseña, tipo_usuario) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nombre, $email, $telefono, $contraseña, $tipo_usuario);

    if ($stmt->execute()) {
        $_SESSION['registro_exitoso'] = true;
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['registro_error'] = "Error al registrar: " . $stmt->error;
        header("Location: registro.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registro - Gastrobar</title>
  <!-- SweetAlert 2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Pacifico&family=Open+Sans&display=swap');

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Open Sans', sans-serif;
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
    }

    .login-container p {
      margin-bottom: 20px;
      color: #555;
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
    }

    button:hover {
      background-color: #3488d6;
    }

    .cocktail-icon {
      font-size: 40px;
      margin-bottom: 10px;
      color: #ff914d;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <div class="cocktail-icon">🍸</div>
    <h1>Gastrobar</h1>
    <p>Formulario de registro</p>
    <form action="registro.php" method="POST">
      <input type="text" name="usuario" placeholder="Usuario" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="text" name="telefono" placeholder="Teléfono" required>
      <input type="password" name="password" placeholder="Contraseña" required>
      <button type="submit">Registrarse</button>
    </form>
    <p>¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
  </div>

  <script>
  // Mostrar mensajes después de cargar la página
  document.addEventListener('DOMContentLoaded', function() {
    <?php if (isset($_SESSION['registro_error'])): ?>
      Swal.fire({
        title: 'Error',
        text: '<?php echo addslashes($_SESSION['registro_error']); ?>',
        icon: 'error',
        confirmButtonColor: '#d63447'
      });
      <?php unset($_SESSION['registro_error']); ?>
    <?php endif; ?>
  });
  </script>
</body>
</html>