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

        $userId = $this->pdo->create($first_name, $last_name, $email, $password_hash);

        if ($userId) {
            return true;
        } else {
            return "An error occured. Please retry!";
        }
    }

    public function login($email, $password)
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

            return true;
        } else {
            return "Invalid email or password.";
        }
    }

    public function logout() {
        session_unset();
        session_destroy();
    }
}