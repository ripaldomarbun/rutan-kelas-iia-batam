<?php
// ============================================================
// FILE: backend/includes/mailer.php
// Helper function untuk kirim email via PHPMailer + SMTP
// ============================================================

// Include PHPMailer classes
require_once __DIR__ . '/../../vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/../../vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../vendor/phpmailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Kirim email reset password
 * 
 * @param string $to Email penerima
 * @param string $nama Nama penerima
 * @param string $token Token reset password
 * @return array ['success' => bool, 'message' => string]
 */
function sendResetPasswordEmail($to, $nama, $token) {
    // Load konfigurasi
    $config = require __DIR__ . '/../config/mail.php';
    
    // URL reset password
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $baseUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['PHP_SELF']));
    $resetUrl = $baseUrl . '/admin/reset-password.php?token=' . $token;
    
    // HTML Email body
    $body = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: linear-gradient(135deg, #0a1628, #1a3a5c); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { background: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
        .button { display: inline-block; background: linear-gradient(135deg, #c9a84c, #e8c97a); color: #0a1628; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; margin: 20px 0; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #666; }
        .warning { background: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin-top: 20px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>🔐 Reset Password</h2>
            <p>Rutan Kelas IIA Batam</p>
        </div>
        <div class="content">
            <p>Halo <strong>' . htmlspecialchars($nama) . '</strong>,</p>
            <p>Anda telah meminta reset password untuk akun admin panel Rutan Batam.</p>
            <p>Klik tombol di bawah ini untuk mengatur password baru:</p>
            <p style="text-align: center;">
                <a href="' . $resetUrl . '" class="button">Reset Password Sekarang</a>
            </p>
            <p style="font-size: 13px; color: #666;">Atau salin link ini ke browser:<br>
            <a href="' . $resetUrl . '">' . $resetUrl . '</a></p>
            <div class="warning">
                ⚠️ Link ini akan kadaluarsa dalam <strong>1 jam</strong>.<br>
                Jika Anda tidak meminta reset password, abaikan email ini.
            </div>
        </div>
        <div class="footer">
            <p>&copy; ' . date('Y') . ' Rutan Kelas IIA Batam. Email otomatis, jangan balas.</p>
        </div>
    </div>
</body>
</html>';
    
    try {
        $mail = new PHPMailer(true);
        
        // SMTP Settings dari config
        $mail->isSMTP();
        $mail->Host       = $config['host'];
        $mail->SMTPAuth   = true;
        $mail->Username   = $config['username'];
        $mail->Password   = $config['password'];
        $mail->SMTPSecure = $config['encryption'];
        $mail->Port       = $config['port'];
        $mail->CharSet    = 'UTF-8';
        
        // Pengirim & Penerima
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($to, $nama);
        
        // Konten Email
        $mail->isHTML(true);
        $mail->Subject = 'Reset Password Admin - Rutan Kelas IIA Batam';
        $mail->Body    = $body;
        $mail->AltBody = "Reset password klik link: " . $resetUrl;
        
        $mail->send();
        
        return [
            'success' => true,
            'message' => 'Email reset password telah dikirim ke ' . $to
        ];
        
    } catch (Exception $e) {
        // Jika gagal kirim email, tampilkan link (fallback)
        return [
            'success' => false,
            'message' => 'Gagal kirim email. Link reset: ' . $resetUrl
        ];
    }
}
