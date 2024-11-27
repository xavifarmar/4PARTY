<?php
// mailHelper.php
require 'vendor/autoload.php';

class MailHelper {
    public static function sendOTP($email, $otp) {
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP(); 
        $mail->Host = 'smtp.gmail.com';  // Servidor SMTP de Gmail
        $mail->SMTPAuth = true; //Esta opción autentica el usuario antes de enviar un correo. 
        $mail->Username = '4partyapp@gmail.com'; // Mi correo de Gmail
        $mail->Password = 'bnhf cwen ezek ihub'; // Contraseña de la aplicación de Gmail (si usas 2FA)
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS; //Esto asegura que la conexión sea segura mediante cifrado SSL/TLS.
        $mail->Port = 587; // El puerto que utiliza GMAIL, para el servidor de STMP

        // Asunto y cuerpo del mensaje
        $mail->Subject = 'Codigo de Verificacion para restablecer contraseña';
        $mail->Body = 'Tu codigo de verificacion es: ' . $otp . '. Este codigo expirara en 5 minutos.';

        $mail->addAddress($email);

        if (!$mail->send()) {
            return ['error' => true, 'message' => $mail->ErrorInfo];
        }
        return ['error' => false];
    }
}
?>
