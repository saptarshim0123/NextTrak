<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../config/session_config.php';
require_once '../../src/core/functions.php';
require_once '../../src/classes/User.php'; // We'll need to update this file

requireLogin();
$user = getCurrentUser();
$userObj = new User($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action']) && $_POST['action'] === 'update_details') {
        $first_name = sanitizeInput($_POST['first_name'] ?? '');
        $last_name = sanitizeInput($_POST['last_name'] ?? '');
        $email = sanitizeInput($_POST['email'] ?? '');
        $errors = [];

        if (empty($first_name) || empty($last_name) || empty($email)) {
            $errors[] = "All fields are required.";
        }
        if (!validateEmail($email)) {
            $errors[] = "Invalid email format.";
        }

        if ($email !== $user['email'] && $userObj->emailExists($email)) {
            $errors[] = "An account with this email already exists.";
        }

        if (empty($errors)) {
            $success = $userObj->updateDetails($user['id'], $first_name, $last_name, $email);
            if ($success) {
                setFlashMessage('Profile details updated successfully!', 'success');
                $_SESSION['user_first_name'] = $first_name;
            } else {
                setFlashMessage('An error occurred while updating details.', 'danger');
            }
        } else {
            setFlashMessage(implode(' ', $errors), 'danger');
        }
        redirect('/public/dashboard/profile.php');
    }

    if (isset($_POST['action']) && $_POST['action'] === 'update_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_new_password = $_POST['confirm_new_password'] ?? '';
        $errors = [];

        $userData = $userObj->getUserByEmail($user['email']);

        if (empty($current_password) || !password_verify($current_password, $userData['password_hash'])) {
            $errors[] = "Your current password is not correct.";
        }

        if (empty($new_password) || $new_password !== $confirm_new_password) {
            $errors[] = "The new passwords do not match.";
        }

        $passwordValidation = validatePassword($new_password);
        if (!$passwordValidation['valid']) {
            $errors[] = $passwordValidation['message'];
        }

        if (empty($errors)) {
            $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $success = $userObj->updatePassword($user['id'], $password_hash);
            if ($success) {
                setFlashMessage('Password changed successfully!', 'success');
            } else {
                setFlashMessage('An error occurred while changing your password.', 'danger');
            }
        } else {
            setFlashMessage(implode(' ', $errors), 'danger');
        }
        redirect('/public/dashboard/profile.php');
    }
}

$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - NextTrak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css"> 
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-light">

        <div class="container-fluid px-4">
                <i data-lucide="target" class="me-2"></i>
                <strong>NextTrak</strong>
            </a>
            </button>
                    <li class="nav-item me-3">
                        <a class="nav-link" href="index.php">
                            <i data-lucide="home" style="width: 25px; height: 25px;"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item me-3">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#addApplicationModal">
                            <i data-lucide="plus-circle" style="width: 25px; height: 25px;"></i>
                            Add Application
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown"
                            role="button" data-bs-toggle="dropdown">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px;">
                                <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                            </div>
                            <?php echo htmlspecialchars($user['first_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item active" href="profile.php"><i data-lucide="user"
                                        style="width: 16px; height: 16px;"></i> Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item text-danger" href="../logout.php"><i data-lucide="log-out"
                                        style="width: 16px; height: 16px;"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold mb-1">My Profile</h2>
                <p class="text-muted">Manage your personal information and password</p>
            </div>
        </div>

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h5 class="card-title fw-semibold mb-3">Account Details</h5>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Email</span>
                                <strong><?php echo htmlspecialchars($user['email']); ?></strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span class="text-muted">Member Since</span>
                                <strong><?php echo formatDate($user['created_at']); ?></strong>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-semibold">Personal Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="profile.php">
                            <input type="hidden" name="action" value="update_details">
                            <div class="row g-3">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label fw-semibold">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label fw-semibold">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i data-lucide="save" style="width: 18px; height: 18px;"></i>
                                Save Changes
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-semibold">Change Password</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="profile.php">
                            <input type="hidden" name="action" value="update_password">
                            <div class="mb-3">
                                <label for="current_password" class="form-label fw-semibold">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label fw-semibold">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" 
                                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                       title="Must contain at least one number, one uppercase and lowercase letter, and at least 8 or more characters"
                                       required>
                                <div class="form-text">
                                    Must be 8+ characters and include one uppercase, one lowercase, and one number.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_new_password" class="form-label fw-semibold">Confirm New Password</label>
                                <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i data-lucide="lock" style="width: 18px; height: 18px;"></i>
                                Change Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addApplicationModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Add application form goes here...</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="addAppForm" class="btn btn-primary">
                        Save Application
                    </button>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>

</html>