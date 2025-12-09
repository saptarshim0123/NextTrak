<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

class Mailer
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->configure();
    }

    /**
     * Configure PHPMailer with SMTP settings
     */
    private function configure()
    {
        try {
            // Server settings
            $this->mail->isSMTP();
            $this->mail->Host = SMTP_HOST;
            $this->mail->SMTPAuth = true;
            $this->mail->Username = SMTP_USERNAME;
            $this->mail->Password = SMTP_PASSWORD;
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = SMTP_PORT;
            $this->mail->CharSet = 'UTF-8';

            // Sender info
            $this->mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);

            // Optional: Enable verbose debug output (disable in production)
            // $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
        } catch (Exception $e) {
            error_log("Mailer configuration error: {$this->mail->ErrorInfo}");
            throw $e;
        }
    }

    /**
     * Send an email
     * 
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $message HTML message body
     * @param string|null $altBody Plain text alternative body
     * @return bool
     */
    public function send($to, $subject, $message, $altBody = null)
    {
        try {
            // Recipients
            $this->mail->addAddress($to);

            // Content
            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $message;
            
            // Set alternative plain text body
            if ($altBody) {
                $this->mail->AltBody = $altBody;
            } else {
                // Auto-generate plain text from HTML
                $this->mail->AltBody = strip_tags($message);
            }

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    /**
     * Send email to multiple recipients
     * 
     * @param array $recipients Array of email addresses
     * @param string $subject Email subject
     * @param string $message HTML message body
     * @return bool
     */
    public function sendBulk($recipients, $subject, $message)
    {
        try {
            foreach ($recipients as $email) {
                $this->mail->addAddress($email);
            }

            $this->mail->isHTML(true);
            $this->mail->Subject = $subject;
            $this->mail->Body = $message;
            $this->mail->AltBody = strip_tags($message);

            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Bulk email sending failed: {$this->mail->ErrorInfo}");
            return false;
        }
    }

    /**
     * Add an attachment to the email
     * 
     * @param string $path Path to the file
     * @param string|null $name Optional custom filename
     */
    public function addAttachment($path, $name = null)
    {
        try {
            if ($name) {
                $this->mail->addAttachment($path, $name);
            } else {
                $this->mail->addAttachment($path);
            }
        } catch (Exception $e) {
            error_log("Failed to add attachment: {$e->getMessage()}");
        }
    }

    /**
     * Clear all recipients
     */
    public function clearRecipients()
    {
        $this->mail->clearAddresses();
    }

    /**
     * Clear all attachments
     */
    public function clearAttachments()
    {
        $this->mail->clearAttachments();
    }
}