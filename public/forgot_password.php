<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/session_config.php';
require_once '../src/core/functions.php';
require_once '../src/classes/PasswordReset.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    
    if (empty($email)) {
        $error = 'Please enter your email address.';
    } elseif (!validateEmail($email)) {
        $error = 'Please enter a valid email address.';
    } else {
        $passwordReset = new PasswordReset($pdo);
        $result = $passwordReset->requestReset($email);
        
        if ($result === true) {
            $success = 'If an account exists with this email, you will receive password reset instructions shortly.';
        } else {
            // Show same success message for security (don't reveal if email exists)
            $success = 'If an account exists with this email, you will receive password reset instructions shortly.';
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

    <!-- Forgot Password Section -->
    <section class="py-5">
        <div class="container-lg">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-5">
                            <!-- Header -->
                            <div class="text-center mb-4">
                                <div class="feature-icon mx-auto mb-3">
                                    <i data-lucide="key" style="width: 24px; height: 24px; color: white;"></i>
                                </div>
                                <h2 class="fw-bold mb-2">Reset Password</h2>
                                <p class="text-muted">Enter your email address and we'll send you instructions to reset your password</p>
                            </div>

                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger d-flex align-items-center" role="alert">
                                    <i data-lucide="alert-circle" class="me-2" style="width: 20px; height: 20px;"></i>
                                    <div><?php echo htmlspecialchars($error); ?></div>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($success)): ?>
                                <div class="alert alert-success d-flex align-items-center" role="alert">
                                    <i data-lucide="check-circle" class="me-2" style="width: 20px; height: 20px;"></i>
                                    <div><?php echo htmlspecialchars($success); ?></div>
                                </div>
                                <div class="text-center mt-4">
                                    <a href="login.php" class="btn btn-primary">
                                        <i data-lucide="arrow-left" class="me-2" style="width: 18px; height: 18px;"></i>
                                        Back to Login
                                    </a>
                                </div>
                            <?php else: ?>
                                <!-- Reset Form -->
                                <form method="POST" action="">
                                    <div class="mb-4">
                                        <label for="email" class="form-label fw-semibold">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i data-lucide="mail" style="width: 18px; height: 18px; color: #6B7280;"></i>
                                            </span>
                                            <input type="email" class="form-control border-start-0 ps-0" id="email"
                                                name="email" placeholder="Enter your email"
                                                value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold">
                                        <i data-lucide="send" class="me-2" style="width: 20px; height: 20px;"></i>
                                        Send Reset Instructions
                                    </button>
                                </form>

                                <div class="text-center mt-4">
                                    <a href="login.php" class="text-primary text-decoration-none">
                                        <i data-lucide="arrow-left" class="me-1" style="width: 16px; height: 16px;"></i>
                                        Back to Login
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p class="text-muted small">
                            <i data-lucide="info" class="me-1" style="width: 16px; height: 16px;"></i>
                            Password reset links expire after 1 hour
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../src/templates/footer.php'; ?>