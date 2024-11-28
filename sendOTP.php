<?php
require 'vendor/autoload.php';

// Configuración para el servidor de Gmail (PHPMailer)
class MailHelper {
    public static function sendOTP($email, $otp) {
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '4partyapp@gmail.com'; // Reemplaza con tu correo de Gmail
        $mail->Password = 'bnhf cwen ezek ihub'; // Contraseña de aplicación generada en Gmail
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Configuración del correo
        $mail->setFrom('4partyapp@gmail.com', '4Party App');
        $mail->addAddress($email);
        $mail->Subject = 'Código de Verificación para restablecer contraseña';
        $mail->isHTML(true);
        $mail->Body    = '<p>Tu código de verificación es: <b>' . $otp . '</b></p><p>Este código expirará en 5 minutos.</p>';

        // Intentar enviar el correo
        if (!$mail->send()) {
            return ['error' => true, 'message' => 'Error al enviar el correo: ' . $mail->ErrorInfo];
        }
        return ['error' => false, 'message' => 'Correo enviado correctamente.'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Correo no válido.";
        exit;
    }

    // Generar OTP (código de 6 dígitos aleatorio)
    $otp = rand(100000, 999999);

    // Enviar correo
    $result = MailHelper::sendOTP($email, $otp);
    
    if ($result['error']) {
        echo 'Error al enviar el mensaje: ' . $result['message'];
    } else {
        echo 'Mensaje enviado con éxito.';
    }
}
?>
