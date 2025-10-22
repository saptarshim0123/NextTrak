<?php include '../src/templates/header.php'; ?>
<section class="py-5">
    <div class="container-lg">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7">
                <div class="card shadow-lg border-0">
                    <div class="card-body p-5">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div class="feature-icon mx-auto mb-3">
                                <i data-lucide="party-popper" style="width: 24px; height: 24px; color: white;"></i>
                            </div>
                            <h2 class="fw-bold mb-2">Create Your Account</h2>
                            <p class="text-muted">Register to start tracking your job applications today!</p>
                        </div>

                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                <i data-lucide="alert-circle" class="me-2" style="width: 20px; height: 20px;"></i>
                                <div><?php echo htmlspecialchars($error); ?></div>
                            </div>
                        <?php endif; ?>

                        <!-- Register Form -->
                        <form method="POST" action="" id="registerForm">
                            <div class="mb-3">
                                <label for="first_name" class="form-label fw-semibold">First Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i data-lucide="user" style="width: 18px; height: 18px; color: #6B7280;"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="first_name"
                                        name="first_name" placeholder="John"
                                        value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="last_name" class="form-label fw-semibold">Last Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i data-lucide="user" style="width: 18px; height: 18px; color: #6B7280;"></i>
                                    </span>
                                    <input type="text" class="form-control border-start-0 ps-0" id="last_name"
                                        name="last_name" placeholder="Doe"
                                        value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                                </div>
                            </div>


                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email Address</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i data-lucide="mail" style="width: 18px; height: 18px; color: #6B7280;"></i>
                                    </span>
                                    <input type="email" class="form-control border-start-0 ps-0" id="email" name="email"
                                        placeholder="Enter your email"
                                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0">
                                        <i data-lucide="lock" style="width: 18px; height: 18px; color: #6B7280;"></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0 ps-0" id="password"
                                        name="password" minlength="8" maxlength="12"
                                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                        title="Must contain at least one number, one uppercase and lowercase letter, and at least 8 or more characters"
                                        placeholder="Enter your password" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="confirm-password" class="form-label fw-semibold">Confirm Password</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0" id="password-match-icon">
                                        <i data-lucide="check-check" class=""></i>
                                    </span>
                                    <input type="password" class="form-control border-start-0 ps-0"
                                        id="confirm-password" name="confirm-password"
                                        placeholder="Enter your password again" required>
                                </div>
                                <div id="password-match-message" class="form-text"></div>
                            </div>


                            <button type="submit" class="btn btn-primary w-100 py-3 fw-semibold">
                                <i data-lucide="user-plus" class="me-2" style="width: 20px; height: 20px;"></i>
                                Register
                            </button>
                        </form>

                        <div class="text-center my-4">
                            <span class="text-muted">Already have an account? Login instead!</span>
                        </div>

                        <div class="text-center">
                            <a href="login.php" class="btn btn-outline-primary w-100 py-3 fw-semibold">
                                <i data-lucide="log-in" class="me-2" style="width: 20px; height: 20px;"></i>
                                Login
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

<?php include "../src/templates/footer.php"; ?>