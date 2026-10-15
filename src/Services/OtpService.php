<?php
// src/Services/OtpService.php - Email OTP Verification Service

require_once dirname(__DIR__) . '/Database.php';

class OtpService {
    public static function generateOtp(string $email): string {
        $db = Database::getInstance();
        $email = strtolower(trim($email));

        // Invalidate older OTPs
        $db->execute("UPDATE email_otps SET is_used = 1 WHERE email = ?", [$email]);

        // Generate 6-digit cryptographically secure OTP
        $otp = (string)random_int(100000, 999999);
        $expiresAt = date('Y-m-d H:i:s', time() + 600); // 10 minutes

        $db->execute("INSERT INTO email_otps (email, otp_code, expires_at, is_used) VALUES (?, ?, ?, 0)", [
            $email,
            $otp,
            $expiresAt
        ]);

        // Send Email (in development mode, store in session and log for instant testing)
        self::sendOtpEmail($email, $otp);

        return $otp;
    }

    public static function verifyOtp(string $email, string $otp): bool {
        $db = Database::getInstance();
        $email = strtolower(trim($email));
        $otp = trim($otp);

        $record = $db->fetchOne("
            SELECT * FROM email_otps 
            WHERE email = ? AND otp_code = ? AND is_used = 0 AND expires_at > CURRENT_TIMESTAMP
            ORDER BY id DESC LIMIT 1
        ", [$email, $otp]);

        if ($record) {
            $db->execute("UPDATE email_otps SET is_used = 1 WHERE id = ?", [$record['id']]);
            return true;
        }

        return false;
    }

    public static function sendOtpEmail(string $email, string $otp): bool {
        $_SESSION['dev_last_otp'] = $otp;
        $_SESSION['dev_last_email'] = $email;
        // In production, invoke mail() or SMTP mailer (PHPMailer / SendGrid / Amazon SES)
        return true;
    }

    public static function getDevOtp(): ?string {
        return $_SESSION['dev_last_otp'] ?? null;
    }
}
