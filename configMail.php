<?php
// configmail.php
require_once 'mailHelper.php';

$email = $_POST['email']; // El email que has recibido de la petición
$otp = rand(100000, 999999); // Generar un OTP aleatorio de 6 dígitos

$sendResult = MailHelper::sendOTP($email, $otp);

if ($sendResult['error']) {
    echo 'Error al enviar el mensaje: ' . $sendResult['message'];
} else {
    echo 'Mensaje enviado con éxito';
    // Guardar el OTP y la expiración en la base de datos
    $expiration_time = time() + 300; // 5 minutos de expiración
    $query = "INSERT INTO reset_otp (email, otp, expiration_time) VALUES ('$email', '$otp', '$expiration_time')";
    mysqli_query($conn, $query);
}
?>