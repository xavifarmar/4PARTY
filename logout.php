<?php
session_start(); // Iniciar sesión
/*echo '<pre>';
var_dump($_SESSION);  // Imprime el contenido de la sesión
echo '</pre>';*/

require "config/conexion.php";

// Verificar si el usuario está logueado
if (isset($_SESSION['user_id'])) {
    // Destruir la sesión
    session_unset(); // Eliminar todas las variables de sesión
    session_destroy(); // Destruir la sesión

    // Responder con un mensaje de éxito
    echo json_encode(["message" => "Logout exitoso"]);
} else {

    // Si no hay sesión activa, responder con un error
    echo json_encode(["message" => "No se encontró sesión activa"]);
}
?>
