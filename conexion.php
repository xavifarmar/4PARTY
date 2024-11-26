<?php 
require 'vendor/autoload.php'; // Incluye el autoload generado por Composer

$servidor = "localhost";
$usuario = "root";
$password = "";
$db = "4party";

//Crear conexion

$conn = new mysqli($servidor, $usuario, $password, $db);

if ($conn -> connect_error){
    die("Conexion fallida - Error de conexión: " . $conn->connect_error);
}else{
    
}
?>