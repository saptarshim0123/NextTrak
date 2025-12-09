<?php

require_once __DIR__ . '/User.php';
require_once __DIR__ . '/../core/Mailer.php';

class PasswordReset
{
    private $pdo;
    private $user;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->user = new User($pdo);
    }

    /**
     * Request a password reset - generates token and sends email
     */
    public function requestReset($email)
    {
        // Check if user exists
        $userData = $this->user->getUserByEmail($email);
        
        if (!$userData) {
            // Don't reveal if email exists (security best practice)
            return true;
        }

        // Generate secure token
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', time() + 3600); // 1 hour expiry

        try {
            // Delete any existing reset tokens for this user
            $deleteSql = "DELETE FROM password_reset_tokens WHERE user_id = ?";
            $deleteStmt = $this->pdo->prepare($deleteSql);
            $deleteStmt->execute([$userData['id']]);

            // Insert new reset token
            $insertSql = "INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (?, ?, ?)";
            $insertStmt = $this->pdo->prepare($insertSql);
            $insertStmt->execute([$userData['id'], $token, $expires_at]);

            // Send reset email
            $resetLink = APP_URL . "/public/reset_password.php?token=" . $token;
            $this->sendResetEmail($userData['email'], $userData['first_name'], $resetLink);

            return true;
        } catch (PDOException $e) {
            error_log("PasswordReset::requestReset Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify if a reset token is valid and not expired
     */
    public function verifyToken($token)
    {
        try {
            $currentTime = date('Y-m-d H:i:s');
            $sql = "SELECT user_id, expires_at FROM password_reset_tokens 
                    WHERE token = ? AND expires_at > ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$token, $currentTime]);
            
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("PasswordReset::verifyToken Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Reset password using valid token
     */
    public function resetPassword($token, $new_password)
    {
        try {
            // Get user_id from token
            $currentTime = date('Y-m-d H:i:s');
            $sql = "SELECT user_id FROM password_reset_tokens 
                    WHERE token = ? AND expires_at > ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$token, $currentTime]);
            $tokenData = $stmt->fetch();

            if (!$tokenData) {
                return "Invalid or expired reset token.";
            }

            // Update user password
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $updateSql = "UPDATE users SET password_hash = ? WHERE id = ?";
            $updateStmt = $this->pdo->prepare($updateSql);
            $updateStmt->execute([$password_hash, $tokenData['user_id']]);

            // Delete used token
            $deleteSql = "DELETE FROM password_reset_tokens WHERE token = ?";
            $deleteStmt = $this->pdo->prepare($deleteSql);
            $deleteStmt->execute([$token]);

            return true;
        } catch (PDOException $e) {
            error_log("PasswordReset::resetPassword Error: " . $e->getMessage());
            return "An error occurred while resetting your password.";
        }
    }

    /**
     * Send password reset email
     */
    private function sendResetEmail($to, $firstName, $resetLink)
    {
        $subject = "Reset Your NextTrak Password";
        
        $message = "
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #4F46E5, #6366F1); color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0; }
                .content { background: #f9fafb; padding: 30px; border-radius: 0 0 8px 8px; }
                .button { display: inline-block; background: #4F46E5; color: white; padding: 15px 30px; text-decoration: none; border-radius: 50px; margin: 20px 0; font-weight: bold; }
                .footer { text-align: center; margin-top: 20px; color: #6B7280; font-size: 14px; }
                .warning { background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 15px; margin: 20px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 Password Reset Request</h1>
                </div>
                <div class='content'>
                    <p>Hi <strong>" . htmlspecialchars($firstName) . "</strong>,</p>
                    
                    <p>We received a request to reset your password for your NextTrak account. If you didn't make this request, you can safely ignore this email.</p>
                    
                    <p>To reset your password, click the button below:</p>
                    
                    <div style='text-align: center;'>
                        <a href='" . htmlspecialchars($resetLink) . "' class='button'>Reset My Password</a>
                    </div>
                    
                    <p>Or copy and paste this link into your browser:</p>
                    <p style='word-break: break-all; background: white; padding: 10px; border-radius: 4px; font-size: 12px;'>" . htmlspecialchars($resetLink) . "</p>
                    
                    <div class='warning'>
                        <strong>⚠️ Security Notice:</strong> This link will expire in 1 hour for security reasons. If you didn't request this reset, please secure your account immediately.
                    </div>
                    
                    <p>Best regards,<br><strong>The NextTrak Team</strong></p>
                </div>
                <div class='footer'>
                    <p>This is an automated email. Please do not reply to this message.</p>
                    <p>&copy; 2024 NextTrak. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        ";

        try {
            $mailer = new Mailer();
            return $mailer->send($to, $subject, $message);
        } catch (Exception $e) {
            error_log("Failed to send reset email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean up expired tokens (should be run periodically via cron)
     */
    public function cleanupExpiredTokens()
    {
        try {
            $currentTime = date('Y-m-d H:i:s');
            $sql = "DELETE FROM password_reset_tokens WHERE expires_at < ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$currentTime]);
        } catch (PDOException $e) {
            error_log("PasswordReset::cleanupExpiredTokens Error: " . $e->getMessage());
            return false;
        }
    }
}