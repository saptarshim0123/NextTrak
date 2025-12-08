<?php
// Add this to your session_config.php AFTER session_start()

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0);
    ini_set('session.cookie_lifetime', 0); // Session cookie
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    session_start();

    // COOKIE-BASED AUTO-LOGIN
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
        if (isset($pdo)) {
            require_once __DIR__ . '/../src/classes/Auth.php';
            $auth = new Auth($pdo);
            $auth->loginWithCookie();
        }
    }

    // **NEW: Session Token Validation**
    // This ensures that if remember_me is NOT active, we validate the session token
    if (isset($_SESSION['user_id']) && !isset($_SESSION['remember_me_active'])) {
        // User is logged in but remember_me is NOT active
        // Check if they have a valid session token
        
        if (!isset($_SESSION['session_token'])) {
            // No session token - this shouldn't happen, but create one
            $_SESSION['session_token'] = bin2hex(random_bytes(32));
        }
        
        // Check if session token cookie exists and matches
        if (!isset($_COOKIE['session_token']) || $_COOKIE['session_token'] !== $_SESSION['session_token']) {
            // Session token mismatch - user closed browser and came back
            // This is a new browser session, so log them out
            session_unset();
            session_destroy();
            header('Location: ' . APP_URL . '/public/login.php');
            exit;
        }
    }

    // SESSION REGENERATION
    if (!isset($_SESSION['last_regen'])) {
        $_SESSION['last_regen'] = time();
    } elseif (time() - $_SESSION['last_regen'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['last_regen'] = time();
    }

    // INACTIVITY TIMEOUT
    if (isset($_SESSION['user_id'])) {
        if (isset($_SESSION['last_activity'])) {
            $inactive_time = time() - $_SESSION['last_activity'];
            if ($inactive_time > SESSION_LIFETIME) {
                session_unset();
                session_destroy();
                header('Location: ' . APP_URL . '/public/login.php?timeout=1');
                exit;
            }
        }
        $_SESSION['last_activity'] = time();
    }
}

function setFlashMessage($message, $type = 'success')
{
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function getFlashMessage()
{
    if (isset($_SESSION['flash_message'])) {
        $message = [
            'message' => $_SESSION['flash_message'],
            'type' => $_SESSION['flash_type']
        ];
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
        return $message;
    }
    return null;
}

function hasFlashMessage()
{
    return isset($_SESSION['flash_message']);
}