<?php 
require 'vendor/autoload.php';

//Configuración del correo (Servidor SMTP)
$mail = new PHPMailer\PHPMailer\PHPMailer();
$mail->isSMTP(); 
$mail->Host = 'smtp.gmail.com';  // Servidor SMTP de Gmail
$mail->SMTPAuth = true; //Esta opción autentica el usuario antes de enviar un correo. 
$mail->Username = 'tu_correo@gmail.com'; // Mi correo de Gmail
$mail->Password = 'tu_contraseña_de_aplicacion'; // Contraseña de la aplicación de Gmail (si usas 2FA)
$mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; //Esto asegura que la conexión sea segura mediante cifrado SSL/TLS.
$mail->Port = 587 //El puerto que utiliza GMAIL, para el servidor de STMP



// Asunto y cuerpo del mensaje
$otp = rand(100000, 999999); // Generar un OTP aleatorio de 6 dígitos
$mail->Subject = 'Código de Verificación para Restablecer Contraseña';
$mail->Body    = 'Tu código de verificación es: ' . $otp . '. Este código expirará en 5 minutos.';

// Enviar el correo
if ($mail->send()) {
    echo 'Mensaje enviado con éxito';
    // Guardar el OTP y la expiración en la base de datos
    $expiration_time = time() + 300; // 5 minutos de expiración
    // Aquí deberías guardar el OTP y la expiración en tu base de datos
    // Utiliza la conexión a la base de datos para insertar el OTP, por ejemplo:
    $query = "INSERT INTO reset_otp (email, otp, expiration_time) VALUES ('$email', '$otp', '$expiration_time')";
    mysqli_query($conn, $query);
} else {
    echo 'Error al enviar el mensaje: ' . $mail->ErrorInfo;
}

?>