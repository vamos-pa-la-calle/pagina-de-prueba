<?php
session_start(); // Inicia la sesión
session_unset(); // Limpia todas las variables de sesión
session_destroy(); // Destruye la sesión

// Redirige al usuario al login o a la página principal
header("Location: index.php");
exit();
