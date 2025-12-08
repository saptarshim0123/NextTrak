<?php

require_once __DIR__ . '/User.php';

class Auth
{
    private $pdo;
    private $user;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->user = new User($this->pdo);
    }

    public function register($first_name, $last_name, $email, $password, $confirm_password)
    {
        if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
            return "All fields are required!";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format.";
        }

        if ($password !== $confirm_password) {
            return "Passwords do not match.";
        }

        $passwordValidation = validatePassword($password);

        if (!$passwordValidation['valid']) {
            return $passwordValidation['message'];
        }

        if ($this->user->emailExists($email)) {
            return "An account with this email already exists.";
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $userId = $this->user->create($first_name, $last_name, $email, $password_hash);

        if ($userId) {
            return true;
        } else {
            return "An error occured. Please retry!";
        }
    }

    public function login($email, $password, $remember)
    {
        if (empty($email) || empty($password)) {
            return "Email and password are required!";
        }

        $userData = $this->user->getUserByEmail($email);

        if (!$userData) {
            return "Invalid email or password.";
        }

        if (password_verify($password, $userData['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['user_first_name'] = $userData['first_name'];
            $_SESSION['last_activity'] = time();

            if ($remember) {
                $this->rememberUser($userData['id']);
            }

            return true;
        } else {
            return "Invalid email or password.";
        }
    }

    public function loginWithCookie() {
        // 1. Check if cookie exists
        if (!isset($_COOKIE['remember_me'])) {
            return false;
        }

        // 2. Extract Selector and Validator
        $parts = explode(':', $_COOKIE['remember_me']);
        if (count($parts) !== 2) {
            return false;
        }
        
        $selector = $parts[0];
        $validator = $parts[1];

        // 3. Find token in database
        $sql = "SELECT * FROM auth_tokens WHERE selector = :selector AND expires_at > NOW()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':selector' => $selector]);
        $tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tokenData) {
            return false;
        }

        // 4. Verify the Validator (Hash comparison)
        if (password_verify($validator, $tokenData['token_hash'])) {
            // Token is valid! Log the user in.
            $userData = $this->user->getUserById($tokenData['user_id']); // You might need to add this method to User.php
            
            if ($userData) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $userData['id'];
                $_SESSION['user_first_name'] = $userData['first_name'];
                $_SESSION['last_activity'] = time();
                return true;
            }
        }

        return false;
    }

    private function rememberUser($userId)
    {
        // Generate two random tokens:
        // Selector: Used to find the token in DB (public-facing)
        // Validator: Used to verify the token (hashed in DB, like a password)
        $selector = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32));

        // Hash the validator before storing it (Security Best Practice)
        $token_hash = password_hash($validator, PASSWORD_DEFAULT); // or hash('sha256', $validator) if you prefer

        // Set expiration (e.g., 30 days from now)
        $expires_at = date('Y-m-d H:i:s', time() + 86400 * 30);

        // Store in auth_tokens table
        $sql = "INSERT INTO auth_tokens (user_id, selector, token_hash, expires_at) VALUES (:user_id, :selector, :token_hash, :expires_at)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':user_id' => $userId,
            ':selector' => $selector,
            ':token_hash' => $token_hash,
            ':expires_at' => $expires_at
        ]);
        // Set the cookie: "selector:validator"
        // We send the raw validator to the user, but keep the hash in DB
        setcookie(
            'remember_me',
            $selector . ':' . $validator,
            time() + 86400 * 30,
            '/',
            '',
            false, // Secure (true if using HTTPS)
            true   // HttpOnly (prevent JS access)
        );
    }

    public function logout()
    {
        // 1. Check if the 'remember_me' cookie exists
        if (isset($_COOKIE['remember_me'])) {
            // Parse the cookie to find the selector
            $parts = explode(':', $_COOKIE['remember_me']);

            // Basic validation to avoid errors if cookie is malformed
            if (count($parts) === 2) {
                $selector = $parts[0];

                // 2. Remove the token from the database
                // This is crucial: it invalidates the token server-side immediately.
                $sql = "DELETE FROM auth_tokens WHERE selector = :selector";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([':selector' => $selector]);
            }

            // 3. Delete the cookie from the browser
            // We do this by setting the expiration date to the past (time() - 3600)
            setcookie(
                'remember_me',
                '',
                time() - 3600,
                '/',
                '',
                false, // Secure (Change to true in production/HTTPS)
                true   // HttpOnly
            );

            // Ensure PHP also knows it's gone for the rest of this request
            unset($_COOKIE['remember_me']);
        }

        // 4. Standard Session Destruction
        session_unset();
        session_destroy();
    }
}