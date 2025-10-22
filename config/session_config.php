<?php

if (session_status() === PHP_SESSION_NONE) {
    // Configuration of session security measures
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0);

    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);

    session_start();

    // Regenerate session ID periodically to prevent session fixation attacks
    if (!isset($_SESSION['last_regen'])) {
        $_SESSION['last_regen'] = time();
    } elseif (time() - $_SESSION['last_regen'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['last_regen'] = time();
    }

    // Session timeout handling
    if (!isset($_SESSION['last_activity'])) {
        $inactive_time = time() - $_SESSION['last_activity'];

        if ($inactive_time > SESSION_LIFETIME) {
            session_unset();
            session_destroy();

            //Redirect to login with timeout message
            if (isset($_SESSION['user_id'])) {
                header('Location: ' . APP_URL . '/public/login.php?timeout=1');
                exit;
            }
        }
    }

    $_SESSION['last_activity'] = time();
}

function setFlashMessage($message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = [
            'message' => $_SESSION['flash_message'],
            'type' => $_SESSION['flash_type']
        ];
        
        // Clear the message after retrieving it
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        
        return $message;
    }
    return null;
}

function hasFlashMessage() {
    return isset($_SESSION['flash_message']);
}