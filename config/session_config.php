<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS
    ini_set('session.cookie_lifetime', 0); // Session cookie (destroyed when browser closes)
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    session_start();

    // **COOKIE-BASED AUTO-LOGIN** - Check BEFORE session validation
    // This must run first so we can restore the session if remember_me is active
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
        if (isset($pdo)) {
            require_once __DIR__ . '/../src/classes/Auth.php';
            $auth = new Auth($pdo);
            $loginSuccess = $auth->loginWithCookie();
            
            if ($loginSuccess) {
                // Mark that this session was restored via remember_me
                $_SESSION['remember_me_active'] = true;
            }
        }
    }

    // **SESSION TOKEN VALIDATION** - Only for non-remember-me sessions
    // If user is logged in but remember_me is NOT active, validate session token
    if (isset($_SESSION['user_id']) && !isset($_SESSION['remember_me_active'])) {
        // Create session token if it doesn't exist
        if (!isset($_SESSION['session_token'])) {
            $_SESSION['session_token'] = bin2hex(random_bytes(32));
            // Set a session token cookie that expires when browser closes
            setcookie('session_token', $_SESSION['session_token'], 0, '/');
        } else {
            // Validate session token
            if (!isset($_COOKIE['session_token']) || $_COOKIE['session_token'] !== $_SESSION['session_token']) {
                // Session token mismatch - user closed browser and came back
                // This is a new browser session, so log them out
                session_unset();
                session_destroy();
                
                // Start a new session for the redirect
                session_start();
                
                header('Location: ' . APP_URL . '/public/login.php?timeout=1');
                exit;
            }
        }
    }

    // **SESSION REGENERATION** - Prevent session fixation
    if (!isset($_SESSION['last_regen'])) {
        $_SESSION['last_regen'] = time();
    } elseif (time() - $_SESSION['last_regen'] > 1800) { // Regenerate every 30 minutes
        session_regenerate_id(true);
        $_SESSION['last_regen'] = time();
    }

    // **INACTIVITY TIMEOUT** - Only for non-remember-me sessions
    if (isset($_SESSION['user_id']) && !isset($_SESSION['remember_me_active'])) {
        if (isset($_SESSION['last_activity'])) {
            $inactive_time = time() - $_SESSION['last_activity'];
            if ($inactive_time > SESSION_LIFETIME) {
                session_unset();
                session_destroy();
                
                // Start new session for redirect
                session_start();
                
                header('Location: ' . APP_URL . '/public/login.php?timeout=1');
                exit;
            }
        }
        $_SESSION['last_activity'] = time();
    }
}

/**
 * Set a flash message in the session
 */
function setFlashMessage($message, $type = 'success')
{
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

/**
 * Get and clear flash message from session
 */
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

/**
 * Check if flash message exists
 */
function hasFlashMessage()
{
    return isset($_SESSION['flash_message']);
}