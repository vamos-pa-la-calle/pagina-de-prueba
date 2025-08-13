<?php
$host = "srv1077.hstgr.io";
$usuario = "u992749838_cocteleria";
$contrasena = "Unitecnar25DWAA#";
$base_datos = "u992749838_cocteleria";

$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


?>
