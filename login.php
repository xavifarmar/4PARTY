<?php
session_start(); 
require "config/conexion.php";


// Verifica si el formulario se ha enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Captura las variables del formulario
    $email = $_POST['email'] ?? null;
    $passwd = $_POST['password'] ?? null;

    // Verifica si los campos no están vacíos
    if ($email && $passwd) {

        // Preparamos la consulta SQL para buscar al usuario por correo electrónico
        $sql = "SELECT id, username, email, password FROM users WHERE email = ?";

        // Preparamos la consulta
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            echo json_encode(["message" => "Error en la preparación de la consulta"]);
        } else {
            // Vinculamos el parámetro (el email)
            $stmt->bind_param("s", $email);

            // Ejecutamos la consulta
            $stmt->execute();
            
            // Guardamos el resultado
            $result = $stmt->get_result();

            // Verificamos si se encontró el usuario
            if ($result->num_rows > 0) {
                // Obtenemos los datos del usuario
                $user = $result->fetch_assoc();

                // Comparamos la contraseña ingresada con el hash almacenado
                if (password_verify($passwd, $user['password'])) {
                    // Si la contraseña es correcta, iniciamos la sesión
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    
                    echo json_encode(["message" => "Inicio de sesión exitoso"]);
                    exit();
                } else {
                    echo json_encode(["message" => "Contraseña incorrecta"]);
                }
            } else {
                echo json_encode(["message" => "Usuario no encontrado"]);
            }

            $stmt->close(); // Cerramos la sentencia
        }
    } else {
        echo json_encode(["message" => "Por favor, ingresa tu correo electrónico y contraseña"]);
    }
}
?>
