<?php
require "conexion.php";
$email = $_POST['email'];
$new_passwd = $_POST['newPasswd'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Asegurarse de que los campos no estén vacíos
    if ($email && $new_passwd) {

        // Paso 1: Verificar si el usuario existe
        $sql = "SELECT email, password FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            echo json_encode(["message" => "Error en la preparación de la consulta"]);
            exit();
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // El usuario existe, procedemos a actualizar la contraseña

            // Paso 2: Cifrar la nueva contraseña de manera segura
            $hashed_password = password_hash($new_passwd, PASSWORD_BCRYPT);

            // Consulta para actualizar la contraseña
            $sql2 = "UPDATE users SET password = ? WHERE email = ?";
            $stmt = $conn->prepare($sql2);

            if ($stmt === false) {
                echo json_encode(["message" => "Error en la preparación de la actualización de la contraseña"]);
                exit();
            }

            $stmt->bind_param("ss", $hashed_password, $email);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                echo json_encode(["message" => "Contraseña actualizada con éxito"]);
            } else {
                echo json_encode(["message" => "Error al actualizar la contraseña"]);
            }

        } else {
            echo json_encode(["message" => "No se ha encontrado el usuario"]);
        }

    } else {
        echo json_encode(["message" => "Debe rellenar todos los campos"]);
    }
}
?>
