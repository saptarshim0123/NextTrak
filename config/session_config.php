<?php

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', 0);
    ini_set('session.gc_maxlifetime', SESSION_LIFETIME);
    session_start();

    // If user is NOT logged in, but HAS a cookie, try to log them in
    if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_me'])) {
        // We need a DB connection here. 
        // Since session_config is usually included early, ensure $pdo is available or include database.php here.
        if (isset($pdo)) {
            require_once __DIR__ . '/../src/classes/Auth.php';
            $auth = new Auth($pdo);
            $auth->loginWithCookie();
        }
    }

    if (!isset($_SESSION['last_regen'])) {
        $_SESSION['last_regen'] = time();
    } elseif (time() - $_SESSION['last_regen'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['last_regen'] = time();
    }

    if (isset($_SESSION['last_activity'])) {
        $inactive_time = time() - $_SESSION['last_activity'];

        if ($inactive_time > SESSION_LIFETIME) {
            // Capture user_id status before destroying the session
            $user_id_was_set = isset($_SESSION['user_id']);

            session_unset();
            session_destroy();

            // Redirect only if the user was logged in before the timeout
            if ($user_id_was_set) {
                header('Location: ' . APP_URL . '/public/login.php?timeout=1');
                exit;
            }
        }
    }

    $_SESSION['last_activity'] = time();
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