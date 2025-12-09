<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/session_config.php';
require_once '../src/core/functions.php';
require_once '../src/classes/PasswordReset.php';

$error = '';
$success = '';
$token = $_GET['token'] ?? '';

if (empty($token)) {
    redirect('/public/forgot_password.php');
}

$passwordReset = new PasswordReset($pdo);

// Verify token is valid
if (!$passwordReset->verifyToken($token)) {
    $error = 'This password reset link is invalid or has expired. Please request a new one.';
    $token = ''; // Disable form
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($token)) {
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($new_password) || empty($confirm_password)) {
        $error = 'Please fill in all fields.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $passwordValidation = validatePassword($new_password);
        
        if (!$passwordValidation['valid']) {
            $error = $passwordValidation['message'];
        } else {
            $result = $passwordReset->resetPassword($token, $new_password);
            
            if ($result === true) {
                setFlashMessage('Your password has been reset successfully! You can now login.', 'success');
                redirect('/public/login.php');
            } else {
                $error = $result;
            }
        }
    }
}

include '../src/templates/header.php';
?>

<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-lg">
            <a class="navbar-brand" href="./index.php">
                <i data-lucide="target" class="me-2"></i>NextTrak
            </a>
        </div>
    </nav>

    <!-- Reset Password Section -->
    <section class="py-5">
        <div class="container-lg">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-5">
                            <!-- Header -->
                            <div class="text-center mb-4">
                                <div class="feature-icon mx-auto mb-3">
                                    <i data-lucide="lock" style="width: 24px; height: 24px; color: white;"></i>
                                </div>
                                <h2 class="fw-bold mb-2">Create New Password</h2>
                                <p class="text-muted">Enter your new password below</p>
                            </div>

                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger d-flex align-items-center" role="alert">
                                    <i data-lucide="alert-circle" class="me-2" style="width: 20px; height: 20px;"></i>
                                    <div><?php echo htmlspecialchars($error); ?></div>
                                </div>
                                
                                <?php if (empty($token)): ?>
                                    <div class="text-center mt-4">
                                        <a href="forgot_password.php" class="btn btn-primary">
                                            <i data-lucide="key" class="me-2" style="width: 18px; height: 18px;"></i>
                                            Request New Reset Link
                                        </a>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if (!empty($token) && empty($error)): ?>
                                <!-- Reset Password Form -->
                                <form method="POST" action="" id="resetPasswordForm">
                                    <div class="mb-3">
                                        <label for="new_password" class="form-label fw-semibold">New Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i data-lucide="lock" style="width: 18px; height: 18px; color: #6B7280;"></i>
                                            </span>
                                            <input type="password" class="form-control border-start-0 ps-0" 
                                                id="new_password" name="new_password"
                                                pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                                title="Must contain at least one number, one uppercase and lowercase letter, and at least 8 or more characters"
                                                placeholder="Enter new password" required>
                                        </div>
                                        <div class="form-text">
                                            Must be 8+ characters with one uppercase, one lowercase, and one number
                                        </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="confirm_password" class="form-label fw-semibold">Confirm New Password</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0" id="password-match-icon-span">
                                                <i data-lucide="lock" class="text-muted" style="width: 18px; height: 18px;"></i>
                                            </span>
                                            <input type="password" class="form-control border-start-0 ps-0"
                                                id="confirm_password" name="confirm_password"
                                                placeholder="Confirm new password" required>
                                        </div>
                                        <div id="password-match-message" class="form-text"></div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold">
                                        <i data-lucide="check" class="me-2" style="width: 20px; height: 20px;"></i>
                                        Reset Password
                                    </button>
                                </form>
                            <?php endif; ?>

                            <div class="text-center mt-4">
                                <a href="login.php" class="text-primary text-decoration-none">
                                    <i data-lucide="arrow-left" class="me-1" style="width: 16px; height: 16px;"></i>
                                    Back to Login
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted small">
                            <i data-lucide="shield-check" class="me-1" style="width: 16px; height: 16px;"></i>
                            Your password is encrypted and secure
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Password match validation
        const passwordInput = document.getElementById('new_password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        const passwordMatchMessage = document.getElementById('password-match-message');
        const passwordMatchIcon = document.querySelector('#password-match-icon-span i');

        function validatePassword() {
            const password = passwordInput.value;
            const confirmPassword = confirmPasswordInput.value;

            if (!passwordMatchIcon) return;

            if (confirmPassword.length > 0) {
                if (password === confirmPassword) {
                    passwordMatchMessage.textContent = 'Passwords match!';
                    passwordMatchMessage.classList.remove('text-danger');
                    passwordMatchMessage.classList.add('text-success');
                    
                    confirmPasswordInput.classList.remove('is-invalid');
                    confirmPasswordInput.classList.add('is-valid');
                    
                    passwordMatchIcon.classList.remove('text-muted', 'text-danger');
                    passwordMatchIcon.classList.add('text-success');
                } else {
                    passwordMatchMessage.textContent = 'Passwords do not match.';
                    passwordMatchMessage.classList.remove('text-success');
                    passwordMatchMessage.classList.add('text-danger');
                    
                    confirmPasswordInput.classList.add('is-invalid');
                    confirmPasswordInput.classList.remove('is-valid');
                    
                    passwordMatchIcon.classList.remove('text-muted', 'text-success');
                    passwordMatchIcon.classList.add('text-danger');
                }
            } else {
                passwordMatchMessage.textContent = '';
                confirmPasswordInput.classList.remove('is-invalid', 'is-valid');
                
                passwordMatchIcon.classList.remove('text-success', 'text-danger');
                passwordMatchIcon.classList.add('text-muted');
            }
        }

        if (passwordInput && confirmPasswordInput && passwordMatchIcon) {
            passwordInput.addEventListener('keyup', validatePassword);
            confirmPasswordInput.addEventListener('keyup', validatePassword);
        }
    </script>

    <?php include '../src/templates/footer.php'; ?>