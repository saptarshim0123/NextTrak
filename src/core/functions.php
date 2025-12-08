<?php
function isLoggedIn()
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        setFlashMessage('Please login to access this page.', 'warning');
        redirect('/public/login.php');
    }
}

function getCurrentUser()
{
    global $pdo;

    if (!isLoggedIn()) {
        return null;
    }

    if (!isset($pdo) || !$pdo instanceof PDO) {
        error_log("getCurrentUser: PDO instance is not available");
        return null;
    }

    try {
        $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, created_at FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error fetching current user: " . $e->getMessage());
        return null;
    }
}

function redirect($path)
{
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        header("Location: $path");
    } else {
        // Relative path - prepend APP_URL
        $url = APP_URL . $path;
        header("Location: $url");
    }
    exit;
}

function sanitizeInput($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePassword($password)
{
    $errors = [];

    // Check length
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }

    // Check for uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }

    // Check for lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }

    // Check for number
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }

    if (empty($errors)) {
        return ['valid' => true, 'message' => 'Password is strong'];
    } else {
        return ['valid' => false, 'message' => implode('. ', $errors)];
    }
}

function formatDate($date, $format = 'M d, Y')
{
    if (empty($date))
        return 'N/A';
    return date($format, strtotime($date));
}

function generateCSRFToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token)
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function dd($var)
{
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    die();
}

function isActivePage($page)
{
    $currentPage = basename($_SERVER['PHP_SELF']);
    return ($currentPage === $page) ? 'active' : '';
}

function truncateText($text, $length = 100, $suffix = '...')
{
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . $suffix;
}

function generateRandomString($length = 32)
{
    return bin2hex(random_bytes($length / 2));
}

/**
 * Get company logo URL using proxy to bypass browser tracking prevention
 * Properly extracts domain from URL and constructs proxy URL
 */
function getCompanyLogo($website)
{
    if (empty($website)) {
        return null;
    }

    // Trim whitespace
    $website = trim($website);
    
    // 1. Ensure a scheme/protocol is present for parse_url to work correctly
    $url = $website;
    if (strpos($url, '://') === false) {
        $url = 'http://' . $url;
    }

    // 2. Extract the host/domain name
    $host = parse_url($url, PHP_URL_HOST);

    if (empty($host)) {
        // If parse_url fails, try to extract domain manually
        // Remove protocol if present
        $host = preg_replace('#^https?://#i', '', $website);
        // Remove path if present
        $host = preg_replace('#[/?#].*$#', '', $host);
        // Remove port if present
        $host = preg_replace('#:\d+$#', '', $host);
        
        if (empty($host)) {
            return null; // Could not extract a valid domain
        }
    }

    // 3. Remove optional 'www.' prefix and convert to lowercase
    $domain = strtolower(preg_replace('/^www\./i', '', $host));
    
    // 4. Basic domain validation - just check it's not empty and has at least one dot
    if (empty($domain) || strpos($domain, '.') === false) {
        return null; // Invalid domain format
    }

    // Use Google Favicon API - more reliable and doesn't have tracking prevention issues
    // Size 128 gives us a good quality logo/favicon
    return "https://www.google.com/s2/favicons?domain={$domain}&sz=128";
}