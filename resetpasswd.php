<?php
require "conexion.php";
require 'vendor/autoload.php';  // Incluir el autoload de Composer para PHPMailer

$email = $_POST['email'];
$otp_received = $_POST['otp'];  // El OTP recibido del usuario
$new_passwd = $_POST['newPasswd'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Asegurarse de que los campos no estén vacíos
    if ($email && $otp_received && $new_passwd) {
        
        // Paso 1: Verificar si el OTP es válido
        $sql = "SELECT * FROM reset_otp WHERE email = ? AND otp = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            echo json_encode(["message" => "Error en la preparación de la consulta"]);
            exit();
        }

        $stmt->bind_param("ss", $email, $otp_received);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $otp_data = $result->fetch_assoc();
            $expiration_time = $otp_data['expiration_time'];

            // Verificar si el OTP ha expirado
            if (time() > $expiration_time) {
                echo json_encode(["message" => "El código OTP ha expirado"]);
            } else {
                // OTP válido, procedemos a actualizar la contraseña
                $hashed_password = password_hash($new_passwd, PASSWORD_BCRYPT);

                // Actualizar la contraseña en la base de datos
                $sql2 = "UPDATE users SET password = ? WHERE email = ?";
                $stmt = $conn->prepare($sql2);
                if ($stmt === false) {
                    echo json_encode(["message" => "Error en la preparación de la actualización de la contraseña"]);
                    exit();
                }

                $stmt->bind_param("ss", $hashed_password, $email);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    // Eliminar el OTP de la base de datos después de usarlo
                    $sql3 = "DELETE FROM reset_otp WHERE email = ?";
                    $stmt = $conn->prepare($sql3);
                    $stmt->bind_param("s", $email);
                    $stmt->execute();

                    echo json_encode(["message" => "Contraseña actualizada con éxito"]);
                } else {
                    echo json_encode(["message" => "Error al actualizar la contraseña"]);
                }
            }
        } else {
            echo json_encode(["message" => "OTP incorrecto"]);
        }
    } else {
        echo json_encode(["message" => "Debe rellenar todos los campos"]);
    }
}
?>
