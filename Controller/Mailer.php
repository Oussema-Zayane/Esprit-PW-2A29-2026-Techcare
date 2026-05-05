<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../libs/PHPMailer/Exception.php';
require_once __DIR__ . '/../libs/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/SMTP.php';

class Mailer {
    // CONFIGURATION SMTP (À remplir par l'utilisateur)
    private static $SMTP_HOST = 'smtp.gmail.com';
    private static $SMTP_USER = 'oussamazayane2005@gmail.com'; 
    private static $SMTP_PASS = 'llbv gtjq mfim qypl'; 
    private static $SMTP_PORT = 587; 
    private static $FROM_EMAIL = 'oussamazayane2005@gmail.com';
    private static $FROM_NAME = 'TechCare';

    public static function sendVerificationEmail($email, $token) {
        $subject = "✨ Activez votre compte TechCare";
        $verifyLink = "http://" . $_SERVER['HTTP_HOST'] . "/User/View/front/verify.php?token=" . $token;
        
        $body = "
        <div style='font-family: \"Inter\", -apple-system, sans-serif; background-color: #f8fafc; padding: 40px 20px; color: #1e293b;'>
            <div style='max-width: 550px; margin: 0 auto; background-color: #ffffff; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;'>
                <div style='text-align: center; margin-bottom: 30px;'>
                    <div style='display: inline-block; background-color: #00A86B; color: white; width: 60px; height: 60px; line-height: 60px; border-radius: 50%; font-size: 30px; margin-bottom: 20px;'>⚡</div>
                    <h1 style='margin: 0; color: #1e293b; font-size: 24px; font-weight: 800;'>Bienvenue chez TechCare</h1>
                </div>
                
                <p style='font-size: 16px; line-height: 1.6; color: #475569; text-align: center;'>
                    Nous sommes ravis de vous compter parmi nous ! Pour commencer votre expérience premium, veuillez confirmer votre adresse email en cliquant sur le bouton ci-dessous :
                </p>
                
                <div style='text-align: center; margin: 35px 0;'>
                    <a href='$verifyLink' style='background: linear-gradient(135deg, #00A86B, #008f5d); color: white; padding: 16px 32px; text-decoration: none; border-radius: 12px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 15px rgba(0,168,107,0.3); display: inline-block;'>Confirmer mon compte</a>
                </div>
                
                <p style='font-size: 14px; color: #94a3b8; text-align: center; margin-top: 30px;'>
                    Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>
                    <a href='$verifyLink' style='color: #00A86B; text-decoration: none;'>$verifyLink</a>
                </p>
                
                <hr style='border: 0; border-top: 1px solid #f1f5f9; margin: 30px 0;'>
                
                <p style='font-size: 12px; color: #cbd5e1; text-align: center; margin: 0;'>
                    &copy; 2026 TechCare. Tous droits réservés.<br>
                    Ceci est un message automatique, merci de ne pas y répondre.
                </p>
            </div>
        </div>";

        return self::send($email, $subject, $body);
    }

    public static function sendResetEmail($email, $token) {
        $subject = "Reset Password - TechCare";
        $resetLink = "http://" . $_SERVER['HTTP_HOST'] . "/User/View/front/reset_password.php?token=" . $token;
        
        $body = "
        <div style='font-family: Arial, sans-serif; background-color: #0f172a; color: #f8fafc; padding: 20px;'>
            <div style='max-width: 600px; margin: 0 auto; background-color: #1e293b; padding: 30px; border-radius: 12px; border: 1px solid #334155;'>
                <h1 style='color: #00A86B;'>Réinitialisation de mot de passe</h1>
                <p>Cliquez sur le bouton ci-dessous pour changer votre mot de passe (valable 1h) :</p>
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='$resetLink' style='background-color: #00A86B; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;'>Réinitialiser le mot de passe</a>
                </div>
            </div>
        </div>";

        return self::send($email, $subject, $body);
    }

    private static function send($to, $subject, $body) {
        $mail = new PHPMailer(true);

        try {
            // Paramètres du serveur
            $mail->isSMTP();
            $mail->Host       = self::$SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = self::$SMTP_USER;
            $mail->Password   = self::$SMTP_PASS;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = self::$SMTP_PORT;
            $mail->CharSet    = 'UTF-8';

            // Destinataires
            $mail->setFrom(self::$FROM_EMAIL, self::$FROM_NAME);
            $mail->addAddress($to);

            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $body;

            $mail->send();
            return true;
        } catch (Exception $e) {
            // En cas d'erreur, on logue dans mail_log.txt pour débugger
            $log = "[" . date('Y-m-d H:i:s') . "] Erreur Mailer: {$mail->ErrorInfo} | To: $to\n";
            file_put_contents(__DIR__ . "/../mail_log.txt", $log, FILE_APPEND);
            return false;
        }
    }
}
