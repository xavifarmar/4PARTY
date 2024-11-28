<?php
require "conexion.php"; // Asegúrate de que el archivo de conexión esté correctamente configurado

// Captura las variables del formulario
$username = $_POST['username'];
$passwd = $_POST['password'];
$confirm_passwd = $_POST['confirm_passwd'];
$email = $_POST['email'];

// Asegúrate de que los campos no estén vacíos
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if ($username && $passwd && $email && $confirm_passwd) {

        // Verificamos si las contraseñas coinciden
        if ($passwd !== $confirm_passwd) {
            echo "Las contraseñas no coinciden.";
        } else {
            // Preparamos la consulta SQL
            $query = "INSERT INTO users (username, email, password, created_at)
                    VALUES (?, ?, ?, CURRENT_TIMESTAMP)"; 

            // Preparamos la consulta
            $stmt = $conn->prepare($query);

            if ($stmt === false) {
                echo "Error en la preparación de la consulta";
            } else {
                // Creamos un hash seguro para la contraseña antes de almacenarla
                $hashed_password = password_hash($passwd, PASSWORD_BCRYPT);

                // Vinculamos los parámetros de la consulta
                $stmt->bind_param("sss", $username, $email, $hashed_password);

                // Ejecutamos la consulta
                if ($stmt->execute()) {
                    echo "Usuario registrado correctamente";
                } else {
                    echo "Error al registrarse";
                }

                $stmt->close(); // Cerramos la sentencia
            }
        }
    } else {
        echo "Datos incompletos, tiene que rellenar todos los campos";
    }
} else {
    echo "Método de solicitud no válido";
}
?>
