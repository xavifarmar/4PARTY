<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $new_passwd = $_POST['newPasswd'];

    if (empty($email) || empty($new_passwd)) {
        echo json_encode(["message" => "Debe proporcionar el correo y la nueva contraseña"]);
        exit();
    }

    // Hash de la nueva contraseña
    $hashed_password = password_hash($new_passwd, PASSWORD_BCRYPT);

    // Actualizar la contraseña en la base de datos
    $query = "UPDATE users SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        echo json_encode(["message" => "Error al preparar la actualización de contraseña"]);
        exit();
    }

    $stmt->bind_param("ss", $hashed_password, $email);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Eliminar OTP después de actualizar la contraseña
        $delete_query = "DELETE FROM reset_otp WHERE email = ?";
        $stmt = $conn->prepare($delete_query);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        echo json_encode(["message" => "Contraseña actualizada con éxito"]);
    } else {
        echo json_encode(["message" => "Error al actualizar la contraseña"]);
    }
}
?>
