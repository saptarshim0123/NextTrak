<?php 
require_once '../config/database.php';
require_once '../config/session_config.php';
require_once '../src/core/functions.php';
require_once '../src/classes/Auth.php';

$error = '';

// Check for flash messages (from registration success)
$flash = getFlashMessage();

// Check for timeout message
if (isset($_GET['timeout']) && $_GET['timeout'] == '1') {
    $error = 'Your session has expired. Please login again.';
}

// Process login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Create Auth instance
    $auth = new Auth($pdo);
    
    // Attempt login
    $result = $auth->login($email, $password);
    
    if ($result === true) {
        // Success! Redirect to dashboard
        redirect('/public/dashboard/index.php');
    } else {
        $error = $result;
    }
}

include '../src/templates/header.php';?>

<body class="bg-light">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-lg">
            <a class="navbar-brand" href="../index.php">
                <i data-lucide="target" class="me-2"></i>NextTrak
            </a>
        </div>
    </nav>

    <!-- Login Section -->
    <section class="py-5">
        <div class="container-lg">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7">
                    <div class="card shadow-lg border-0">
                        <div class="card-body p-5">
                            <!-- Header -->
                            <div class="text-center mb-4">
                                <div class="feature-icon mx-auto mb-3">
                                    <i data-lucide="log-in" style="width: 24px; height: 24px; color: white;"></i>
                                </div>
                                <h2 class="fw-bold mb-2">Welcome Back</h2>
                                <p class="text-muted">Sign in to continue tracking your job applications</p>
                            </div>

                            <?php if (!empty($error)): ?>
                                <div class="alert alert-danger d-flex align-items-center" role="alert">
                                    <i data-lucide="alert-circle" class="me-2" style="width: 20px; height: 20px;"></i>
                                    <div><?php echo htmlspecialchars($error); ?></div>
                                </div>
                            <?php endif; ?>

                            <!-- Login Form -->
                            <form method="POST" id="loginForm" action="">
                                <div class="mb-3">
                                    <label for="email" class="form-label fw-semibold">Email Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i data-lucide="mail"
                                                style="width: 18px; height: 18px; color: #6B7280;"></i>
                                        </span>
                                        <input type="email" class="form-control border-start-0 ps-0" id="email"
                                            name="email" placeholder="Enter your email"
                                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label fw-semibold">Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i data-lucide="lock"
                                                style="width: 18px; height: 18px; color: #6B7280;"></i>
                                        </span>
                                        <input type="password" class="form-control border-start-0 ps-0" id="password"
                                            name="password" placeholder="Enter your password" required>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                        <label class="form-check-label text-muted" for="remember">
                                            Remember me
                                        </label>
                                    </div>
                                    <a href="forgot_password.php" class="text-primary text-decoration-none">
                                        Forgot password?
                                    </a>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold">
                                    <i data-lucide="log-in" class="me-2" style="width: 20px; height: 20px;"></i>
                                    Sign In
                                </button>
                            </form>

                            <div class="text-center my-4">
                                <span class="text-muted">Don't have an account? Create now!</span>
                            </div>

                            <div class="text-center">
                                <a href="register.php" class="btn btn-outline-primary w-100 py-3 fw-semibold">
                                    <i data-lucide="user-plus" class="me-2" style="width: 20px; height: 20px;"></i>
                                    Create Free Account
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Info -->
                    <div class="text-center mt-4">
                        <p class="text-muted small">
                            <i data-lucide="shield-check" class="me-1" style="width: 16px; height: 16px;"></i>
                            Your data is secure and encrypted
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include '../src/templates/footer.php'; ?>