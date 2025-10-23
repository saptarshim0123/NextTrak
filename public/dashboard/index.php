<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../config/session_config.php';
require_once '../../src/core/functions.php';
require_once '../../src/classes/Application.php';
require_once '../../src/classes/Company.php';

if (!isset($pdo) || !$pdo instanceof PDO) {
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        die('Database connection failed: ' . $e->getMessage());
    }
}

requireLogin();

$user = getCurrentUser();

$add_app_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_application') {
    $company_name = sanitizeInput($_POST['company_name'] ?? '');
    $job_title = sanitizeInput($_POST['job_title'] ?? '');
    $job_url = sanitizeInput($_POST['job_url'] ?? '');
    $salary = sanitizeInput($_POST['salary'] ?? '');
    $location = sanitizeInput($_POST['location'] ?? '');
    $job_type = $_POST['job_type'] ?? 'WFO';
    $priority = $_POST['priority'] ?? 'Medium';
    $application_date = $_POST['application_date'] ?? date('Y-m-d');
    $status_id = $_POST['status_id'] ?? 1;
    $follow_up_date = $_POST['follow_up_date'] ?? null;
    $follow_up_email = sanitizeInput($_POST['follow_up_email'] ?? '');
    $interview_date = $_POST['interview_date'] ?? null;
    $notes = sanitizeInput($_POST['notes'] ?? '');

    if (empty($company_name) || empty($job_title)) {
        $add_app_error = "Company name and job title are required.";
    } else {
        $companyObj = new Company($pdo);
        $company_id = $companyObj->getOrCreate($company_name);
        
        if (!$company_id) {
            $add_app_error = "Failed to process company information.";
        } else {
            $appObj = new Application($pdo);
            
            $data = [
                'user_id' => $user['id'],
                'company_id' => $company_id,
                'status_id' => $status_id,
                'job_title' => $job_title,
                'job_url' => !empty($job_url) ? $job_url : null,
                'salary' => !empty($salary) ? intval($salary) : null,
                'location' => !empty($location) ? $location : null,
                'job_type' => $job_type,
                'priority' => $priority,
                'application_date' => $application_date,
                'follow_up_date' => !empty($follow_up_date) ? $follow_up_date : null,
                'follow_up_email' => !empty($follow_up_email) ? $follow_up_email : null,
                'interview_date' => !empty($interview_date) ? $interview_date : null,
                'notes' => !empty($notes) ? $notes : null
            ];
            
            $result = $appObj->create($data);
            
            if ($result) {
                setFlashMessage('Application added successfully!', 'success');
                redirect('/public/dashboard/index.php');
            } else {
                $add_app_error = "Failed to add application. Please try again.";
            }
        }
    }
}

$flash = getFlashMessage();

$statuses = [];
try {

    $stmt = $pdo->query("SELECT * FROM application_statuses ORDER BY id");
    $statuses = $stmt->fetchAll();
} catch (PDOException $e) {
}

// Dummy for now
$stats = [
    'total' => 0,
    'applied' => 0,
    'interview' => 0,
    'offer' => 0,
    'rejected' => 0
];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NextTrak</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="../assets/css/style.css">

    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="index.php">
                <i data-lucide="target" class="me-2"></i>
                <strong>NextTrak</strong>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
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
                            role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px;">
                                <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                            </div>
                            <?php echo htmlspecialchars($user['first_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                            <li>
                                <a class="dropdown-item" href="profile.php">
                                    <i data-lucide="user" style="width: 16px; height: 16px;"></i>
                                    Profile
                                </a>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="../logout.php">
                                    <i data-lucide="log-out" style="width: 16px; height: 16px;"></i>
                                    Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid px-4 py-4">

        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold mb-1">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>! 👋</h2>
                <p class="text-muted">Here's your job application overview</p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Total Applications</p>
                                <h3 class="fw-bold mb-0"><?php echo $stats['total']; ?></h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 rounded p-3">
                                <i data-lucide="briefcase"
                                    style="width: 24px; height: 24px; color: var(--bs-primary);"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Applied</p>
                                <h3 class="fw-bold mb-0"><?php echo $stats['applied']; ?></h3>
                            </div>
                            <div class="bg-info bg-opacity-10 rounded p-3">
                                <i data-lucide="send" style="width: 24px; height: 24px; color: #0dcaf0;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Interviews</p>
                                <h3 class="fw-bold mb-0"><?php echo $stats['interview']; ?></h3>
                            </div>
                            <div class="bg-warning bg-opacity-10 rounded p-3">
                                <i data-lucide="calendar" style="width: 24px; height: 24px; color: #ffc107;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="text-muted mb-1 small">Offers</p>
                                <h3 class="fw-bold mb-0"><?php echo $stats['offer']; ?></h3>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded p-3">
                                <i data-lucide="check-circle"
                                    style="width: 24px; height: 24px; color: var(--bs-success);"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-semibold">Recent Applications</h5>
                            <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addApplicationModal">
                                <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                                Add Application
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                style="width: 80px; height: 80px;">
                                <i data-lucide="inbox" style="width: 40px; height: 40px; color: #6c757d;"></i>
                            </div>
                            <h5 class="fw-semibold mb-2">No Applications Yet</h5>
                            <p class="text-muted mb-4">Start tracking your job applications to see them here</p>
                            <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addApplicationModal">
                                <i data-lucide="plus-circle" class="me-2" style="width: 25px; height: 25px;"></i>
                                Add Your First Application
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    
    <div class="modal fade" id="addApplicationModal" tabindex="-1" aria-labelledby="addApplicationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="addApplicationModalLabel">Add New Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <?php if (!empty($add_app_error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?php echo htmlspecialchars($add_app_error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php" id="addAppForm">
                        <input type="hidden" name="action" value="add_application">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="company_name" class="form-label fw-semibold">
                                    Company Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                    placeholder="e.g., Google, Microsoft" 
                                    value="<?php echo htmlspecialchars($_POST['company_name'] ?? ''); ?>" 
                                    required>
                                <small class="text-muted">If company doesn't exist, it will be created</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="job_title" class="form-label fw-semibold">
                                    Job Title <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="job_title" name="job_title" 
                                    placeholder="e.g., Frontend Developer" 
                                    value="<?php echo htmlspecialchars($_POST['job_title'] ?? ''); ?>" 
                                    required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="job_url" class="form-label fw-semibold">Job Posting URL</label>
                                <input type="url" class="form-control" id="job_url" name="job_url" 
                                    placeholder="https://..." 
                                    value="<?php echo htmlspecialchars($_POST['job_url'] ?? ''); ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="salary" class="form-label fw-semibold">Salary (Annual)</label>
                                <input type="number" class="form-control" id="salary" name="salary" 
                                    placeholder="e.g., 80000" 
                                    value="<?php echo htmlspecialchars($_POST['salary'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="location" class="form-label fw-semibold">Location</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                    placeholder="e.g., Remote, New York, NY" 
                                    value="<?php echo htmlspecialchars($_POST['location'] ?? ''); ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="job_type" class="form-label fw-semibold">Job Type</label>
                                <select class="form-select" id="job_type" name="job_type">
                                    <option value="WFH" <?php echo (($_POST['job_type'] ?? '') === 'WFH') ? 'selected' : ''; ?>>
                                        Work From Home
                                    </option>
                                    <option value="WFO" <?php echo (($_POST['job_type'] ?? 'WFO') === 'WFO') ? 'selected' : ''; ?>>
                                        Work From Office
                                    </option>
                                    <option value="Hybrid" <?php echo (($_POST['job_type'] ?? '') === 'Hybrid') ? 'selected' : ''; ?>>
                                        Hybrid
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="status_id" class="form-label fw-semibold">Application Status</label>
                                <select class="form-select" id="status_id" name="status_id">
                                    <?php foreach ($statuses as $status): ?>
                                        <option value="<?php echo $status['id']; ?>" 
                                            <?php echo (($_POST['status_id'] ?? 1) == $status['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($status['status_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="priority" class="form-label fw-semibold">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="Low" <?php echo (($_POST['priority'] ?? '') === 'Low') ? 'selected' : ''; ?>>
                                        Low
                                    </option>
                                    <option value="Medium" <?php echo (($_POST['priority'] ?? 'Medium') === 'Medium') ? 'selected' : ''; ?>>
                                        Medium
                                    </option>
                                    <option value="High" <?php echo (($_POST['priority'] ?? '') === 'High') ? 'selected' : ''; ?>>
                                        High
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="application_date" class="form-label fw-semibold">Application Date</label>
                                <input type="date" class="form-control" id="application_date" name="application_date" 
                                    value="<?php echo $_POST['application_date'] ?? date('Y-m-d'); ?>">
                            </div>
                            
                            <div class="col-md-4">
                                <label for="follow_up_date" class="form-label fw-semibold">Follow-up Date</label>
                                <input type="date" class="form-control" id="follow_up_date" name="follow_up_date" 
                                    value="<?php echo $_POST['follow_up_date'] ?? ''; ?>">
                            </div>
                            
                            <div class="col-md-4">
                                <label for="interview_date" class="form-label fw-semibold">Interview Date/Time</label>
                                <input type="datetime-local" class="form-control" id="interview_date" name="interview_date" 
                                    value="<?php echo $_POST['interview_date'] ?? ''; ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="follow_up_email" class="form-label fw-semibold">Follow-up Contact Email</label>
                            <input type="email" class="form-control" id="follow_up_email" name="follow_up_email" 
                                placeholder="recruiter@company.com" 
                                value="<?php echo htmlspecialchars($_POST['follow_up_email'] ?? ''); ?>">
                        </div>

                        <div class="mb-3"> <label for="notes" class="form-label fw-semibold">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" 
                                    placeholder="Add any additional notes..."><?php echo htmlspecialchars($_POST['notes'] ?? ''); ?></textarea>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" form="addAppForm" class="btn btn-primary">
                        <i data-lucide="save" class="me-2" style="width: 18px; height: 18px;"></i>
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
    
    <script>
        <?php if (!empty($add_app_error)): ?>
            var addModalElement = document.getElementById('addApplicationModal');
            if (addModalElement) {
                var addModal = new bootstrap.Modal(addModalElement, {});
                addModal.show();
            }
        <?php endif; ?>
    </script>
</body>

</html>