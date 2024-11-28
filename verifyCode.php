<?php
require 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $otp = $_POST['otp'];

    if (empty($email) || empty($otp)) {
        echo json_encode(["message" => "Debe proporcionar el correo y el código OTP"]);
        exit();
    }

    // Consultar la base de datos para verificar el OTP
    $query = "SELECT * FROM reset_otp WHERE email = ? AND otp = ?";
    $stmt = $conn->prepare($query);

    if ($stmt === false) {
        echo json_encode(["message" => "Error al preparar la consulta"]);
        exit();
    }

    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $otp_data = $result->fetch_assoc();
        $expiration_time = $otp_data['expiration_time'];

        if (time() > $expiration_time) {
            echo json_encode(["message" => "El código OTP ha expirado"]);
        } else {
            echo json_encode(["message" => "OTP válido"]);
        }
    } else {
        echo json_encode(["message" => "OTP incorrecto"]);
    }
}
?>
