<?php

/**
 * Email Configuration for PHPMailer
 * 
 * IMPORTANT: Update these settings with your actual SMTP credentials
 * For production, use environment variables or a secure configuration system
 */

// SMTP Server Settings
define('SMTP_HOST', 'smtp.gmail.com');  // Gmail SMTP server (or your SMTP provider)
define('SMTP_PORT', 587);                // TLS port (use 465 for SSL)
define('SMTP_USERNAME', 'saptarshim0123@gmail.com');  // Your email address
define('SMTP_PASSWORD', 'ojnzvcasezbgonra');      // Your app password (NOT your regular password)

// Sender Information
define('SMTP_FROM_EMAIL', 'noreply@nexttrak.com'); // From email address
define('SMTP_FROM_NAME', 'NextTrak');               // From name

/**
 * HOW TO SET UP GMAIL SMTP:
 * 
 * 1. Enable 2-Factor Authentication on your Google account
 * 2. Go to: https://myaccount.google.com/apppasswords
 * 3. Generate an "App Password" for "Mail"
 * 4. Use that 16-character password as SMTP_PASSWORD
 * 5. Use your regular Gmail address as SMTP_USERNAME
 * 
 * ALTERNATIVE SMTP PROVIDERS:
 * 
 * - SendGrid: smtp.sendgrid.net (Port 587)
 * - Mailgun: smtp.mailgun.org (Port 587)
 * - Amazon SES: email-smtp.us-east-1.amazonaws.com (Port 587)
 * - Outlook: smtp-mail.outlook.com (Port 587)
 * 
 * For development/testing, you can use Mailtrap:
 * - Host: smtp.mailtrap.io
 * - Port: 2525
 * - Username & Password: Get from mailtrap.io
 */