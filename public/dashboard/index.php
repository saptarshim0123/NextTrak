<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../config/session_config.php';
require_once '../../src/core/functions.php';
require_once '../../src/classes/Application.php';
require_once '../../src/classes/Company.php';

requireLogin();
$user = getCurrentUser();
$appObj = new Application($pdo);
$add_app_error = '';

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');

    if ($_POST['ajax_action'] === 'update_field') {
        $app_id = intval($_POST['app_id']);
        $field = $_POST['field'];
        $value = $_POST['value'];

        // Build update query based on field
        $allowed_fields = ['status_id', 'priority', 'job_type', 'notes', 'salary', 'location', 'job_url', 'follow_up_date', 'interview_date'];

        if (in_array($field, $allowed_fields)) {
            try {
                $sql = "UPDATE job_applications SET {$field} = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND user_id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$value ?: null, $app_id, $user['id']]);
                echo json_encode(['success' => true]);
            } catch (PDOException $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Invalid field']);
        }
        exit;
    }

    if ($_POST['ajax_action'] === 'delete_application') {
        $app_id = intval($_POST['app_id']);
        $result = $appObj->delete($app_id, $user['id']);
        echo json_encode(['success' => $result]);
        exit;
    }
}

// Handle add application
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
        $company_website = sanitizeInput($_POST['company_website'] ?? '');
        $company_id = $companyObj->getOrCreate($company_name, $company_website);

        if (!$company_id) {
            $add_app_error = "Failed to process company information.";
        } else {
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
    error_log("Error fetching statuses: " . $e->getMessage());
}

$stats = $appObj->getStatsByUserId($user['id']);

// Build filters array from GET parameters
$filters = [];
if (!empty($_GET['filter_status'])) {
    $filters['status_id'] = intval($_GET['filter_status']);
}
if (!empty($_GET['filter_job_type'])) {
    $filters['job_type'] = $_GET['filter_job_type'];
}
if (!empty($_GET['filter_priority'])) {
    $filters['priority'] = $_GET['filter_priority'];
}
if (!empty($_GET['filter_search'])) {
    $filters['search'] = sanitizeInput($_GET['filter_search']);
}
if (!empty($_GET['sort_by'])) {
    $filters['sort_by'] = $_GET['sort_by'];
}

$applications = $appObj->getByUserId($user['id'], $filters);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - NextTrak</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Poppins:wght@600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <style>
        .app-row {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .app-row:hover {
            background-color: #f8f9fa;
            transform: translateX(4px);
        }

        .expandable-details {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(to bottom, #f8f9fa, #ffffff);
        }

        .expandable-details.show {
            max-height: 1000px;
        }

        .detail-card {
            border-left: 4px solid var(--bs-primary);
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .edit-field {
            transition: all 0.2s ease;
            border: 2px solid transparent;
            padding: 0.5rem;
            border-radius: 6px;
        }

        .edit-field:hover {
            border-color: var(--bs-primary);
            background-color: #f8f9fa;
        }

        .edit-field:focus-within {
            border-color: var(--bs-primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .save-indicator {
            opacity: 0;
            transition: opacity 0.3s ease;
            color: var(--bs-success);
            font-size: 0.875rem;
        }

        .save-indicator.show {
            opacity: 1;
        }

        .expand-icon {
            transition: transform 0.3s ease;
        }

        .expand-icon.rotated {
            transform: rotate(180deg);
        }

        .delete-btn {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .expandable-details.show .delete-btn {
            opacity: 1;
        }
        .autocomplete-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #E5E7EB;
            border-top: none;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            max-height: 300px;
            overflow-y: auto;
            z-index: 1050;
            margin-top: -1px;
        }

        .autocomplete-item {
            padding: 12px 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: background-color 0.2s;
            border-bottom: 1px solid #F3F4F6;
        }

        .autocomplete-item:last-child {
            border-bottom: none;
        }

        .autocomplete-item:hover {
            background-color: #F9FAFB;
        }

        .autocomplete-item.active {
            background-color: #EEF2FF;
        }

        .company-logo-autocomplete {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            object-fit: contain;
            background: #F3F4F6;
            padding: 4px;
        }

        .company-logo-fallback {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            background: linear-gradient(135deg, var(--bs-primary), var(--bs-accent));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        .autocomplete-no-results {
            padding: 16px;
            text-align: center;
            color: #6B7280;
            font-size: 14px;
        }

        .company-icon-wrapper {
            position: relative;
            width: 40px;
            height: 40px;
        }

        .company-logo-main {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: contain;
            background: #F3F4F6;
            padding: 6px;
        }

        .company-logo-fallback-main {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--bs-primary), var(--bs-accent));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 16px;
        }
    </style>
</head>

<body class="bg-light">

    <!-- Navigation -->
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
                            role="button" data-bs-toggle="dropdown">
                            <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center me-2"
                                style="width: 32px; height: 32px;">
                                <?php echo strtoupper(substr($user['first_name'], 0, 1)); ?>
                            </div>
                            <?php echo htmlspecialchars($user['first_name']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php"><i data-lucide="user"
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
        <?php if ($flash): ?>
            <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($flash['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="fw-bold mb-1">Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>! 👋</h2>
                <p class="text-muted">Here's your job application overview</p>
            </div>
        </div>

        <!-- Stats Cards -->
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

        <!-- Applications List with Inline Editing -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-semibold">Recent Applications</h5>
                            <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                            data-bs-target="#addApplicationModal">
                            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                            Add Application
                        </a>
                    </div>
                    <button class="btn btn-outline-success btn-sm" type="button" data-bs-toggle="collapse"
                        data-bs-target="#filterPanel">
                        <i data-lucide="filter" class="me-1" style="width: 16px; height: 16px;"></i>
                        Filters
                        <span class="badge bg-success ms-1" id="activeFilterCount"
                            style="display: none;">0</span>
                    </button>
                    </div>
                    <!-- Filter Panel -->
                    <div class="collapse" id="filterPanel">
                        <div class="card-body border-bottom bg-light">
                            <form method="GET" action="index.php" id="filterForm">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Status</label>
                                        <select class="form-select form-select-sm" name="filter_status" id="filter_status">
                                            <option value="">All Statuses</option>
                                            <?php foreach ($statuses as $status): ?>
                                                <option value="<?php echo $status['id']; ?>" 
                                                    <?php echo (isset($_GET['filter_status']) && $_GET['filter_status'] == $status['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($status['status_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Job Type</label>
                                        <select class="form-select form-select-sm" name="filter_job_type" id="filter_job_type">
                                            <option value="">All Types</option>
                                            <option value="WFH" <?php echo (isset($_GET['filter_job_type']) && $_GET['filter_job_type'] == 'WFH') ? 'selected' : ''; ?>>Work From Home</option>
                                            <option value="WFO" <?php echo (isset($_GET['filter_job_type']) && $_GET['filter_job_type'] == 'WFO') ? 'selected' : ''; ?>>Work From Office</option>
                                            <option value="Hybrid" <?php echo (isset($_GET['filter_job_type']) && $_GET['filter_job_type'] == 'Hybrid') ? 'selected' : ''; ?>>Hybrid</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Priority</label>
                                        <select class="form-select form-select-sm" name="filter_priority" id="filter_priority">
                                            <option value="">All Priorities</option>
                                            <option value="Low" <?php echo (isset($_GET['filter_priority']) && $_GET['filter_priority'] == 'Low') ? 'selected' : ''; ?>>Low</option>
                                            <option value="Medium" <?php echo (isset($_GET['filter_priority']) && $_GET['filter_priority'] == 'Medium') ? 'selected' : ''; ?>>Medium</option>
                                            <option value="High" <?php echo (isset($_GET['filter_priority']) && $_GET['filter_priority'] == 'High') ? 'selected' : ''; ?>>High</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Search</label>
                                        <input type="text" class="form-control form-control-sm" name="filter_search" id="filter_search" 
                                            placeholder="Company or job title..." 
                                            value="<?php echo htmlspecialchars($_GET['filter_search'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <label class="form-label fw-semibold small">Sort By</label>
                                        <select class="form-select form-select-sm" name="sort_by" id="sort_by">
                                            <option value="application_date_desc" <?php echo (!isset($_GET['sort_by']) || $_GET['sort_by'] == 'application_date_desc') ? 'selected' : ''; ?>>Application Date (Newest)</option>
                                            <option value="application_date_asc" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'application_date_asc') ? 'selected' : ''; ?>>Application Date (Oldest)</option>
                                            <option value="interview_date_desc" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'interview_date_desc') ? 'selected' : ''; ?>>Interview Date (High-Low)</option>
                                            <option value="interview_date_asc" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'interview_date_asc') ? 'selected' : ''; ?>>Interview Date (Low-High)</option>
                                            <option value="company_asc" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'company_asc') ? 'selected' : ''; ?>>Company (A-Z)</option>
                                            <option value="company_desc" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'company_desc') ? 'selected' : ''; ?>>Company (Z-A)</option>
                                            <option value="priority_desc" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'priority_desc') ? 'selected' : ''; ?>>Priority (High-Low)</option>
                                            <option value="salary_desc" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'salary_desc') ? 'selected' : ''; ?>>Salary (High-Low)</option>
                                        </select>
                                    </div>
                                    
                                    <div class="col-md-9 d-flex align-items-end gap-2">
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i data-lucide="search" style="width: 14px; height: 14px;"></i>
                                            Apply Filters
                                        </button>
                                        <a href="index.php" class="btn btn-outline-danger btn-sm">
                                            <i data-lucide="x" style="width: 14px; height: 14px;"></i>
                                            Clear All
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($applications)): ?>
                            <div class="text-center py-5">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                                    style="width: 80px; height: 80px;">
                                    <i data-lucide="inbox" style="width: 40px; height: 40px; color: #6c757d;"></i>
                                </div>
                                <h5 class="fw-semibold mb-2">No Applications Yet</h5>
                                <p class="text-muted mb-4">Start tracking your job applications to see them here</p>
                                <a href="#" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addApplicationModal">
                                    <i data-lucide="plus-circle" class="me-2" style="width: 25px; height: 25px;"></i>
                                    Add Your First Application
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 40px;"></th>
                                            <th>Company</th>
                                            <th>Job Title</th>
                                            <th>Status</th>
                                            <th>Applied Date</th>
                                            <th>Priority</th>
                                            <th>Salary</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($applications as $app): ?>
                                            <tr class="app-row" data-app-id="<?php echo $app['id']; ?>">
                                                <td>
                                                    <i data-lucide="chevron-down" class="expand-icon"
                                                        style="width: 20px; height: 20px; color: #6c757d;"></i>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php
                                                        $logoUrl = getCompanyLogo($app['company_website'] ?? '');
                                                        $initials = strtoupper(substr($app['company_name'], 0, 2));
                                                        ?>
                                                        <div class="company-icon-wrapper me-2">
                                                            <?php if ($logoUrl): ?>
                                                                <img src="<?php echo htmlspecialchars($logoUrl); ?>" 
                                                                    alt="<?php echo htmlspecialchars($app['company_name']); ?>"
                                                                    class="company-logo-main"
                                                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                            <?php endif; ?>
                                                            <div class="company-logo-fallback-main" style="<?php echo $logoUrl ? 'display:none;' : 'display:flex;'; ?>">
                                                                <?php echo $initials; ?>
                                                            </div>
                                                        </div>
                                                        <strong><?php echo htmlspecialchars($app['company_name']); ?></strong>
                                                    </div>
                                                </td>
                                                <td><?php echo htmlspecialchars($app['job_title']); ?></td>
                                                <td>
                                                    <span
                                                        class="status-badge status-<?php echo strtolower($app['status_name']); ?>">
                                                        <?php echo htmlspecialchars($app['status_name']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($app['application_date'])); ?></td>
                                                <td>
                                                    <span
                                                        class="badge bg-<?php echo $app['priority'] === 'High' ? 'danger' : ($app['priority'] === 'Medium' ? 'warning' : 'secondary'); ?>">
                                                        <?php echo htmlspecialchars($app['priority']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php echo '$' . $app['salary'] ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="6" class="p-0">
                                                    <div class="expandable-details" id="details-<?php echo $app['id']; ?>">
                                                        <div class="p-4">
                                                            <div class="detail-card">
                                                                <div class="row g-4">
                                                                    <!-- Status -->
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label fw-semibold d-flex align-items-center">
                                                                            <i data-lucide="flag" class="me-2"
                                                                                style="width: 16px; height: 16px;"></i>
                                                                            Status
                                                                            <span class="save-indicator ms-2"
                                                                                data-field="status_id-<?php echo $app['id']; ?>">
                                                                                <i data-lucide="check"
                                                                                    style="width: 14px; height: 14px;"></i>
                                                                                Saved
                                                                            </span>
                                                                        </label>
                                                                        <div class="edit-field">
                                                                            <select class="form-select editable-field"
                                                                                data-app-id="<?php echo $app['id']; ?>"
                                                                                data-field="status_id">
                                                                                <?php foreach ($statuses as $status): ?>
                                                                                    <option value="<?php echo $status['id']; ?>"
                                                                                        <?php echo $app['status_id'] == $status['id'] ? 'selected' : ''; ?>>
                                                                                        <?php echo htmlspecialchars($status['status_name']); ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Priority -->
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label fw-semibold d-flex align-items-center">
                                                                            <i data-lucide="zap" class="me-2"
                                                                                style="width: 16px; height: 16px;"></i>
                                                                            Priority
                                                                            <span class="save-indicator ms-2"
                                                                                data-field="priority-<?php echo $app['id']; ?>">
                                                                                <i data-lucide="check"
                                                                                    style="width: 14px; height: 14px;"></i>
                                                                                Saved
                                                                            </span>
                                                                        </label>
                                                                        <div class="edit-field">
                                                                            <select class="form-select editable-field"
                                                                                data-app-id="<?php echo $app['id']; ?>"
                                                                                data-field="priority">
                                                                                <option value="Low" <?php echo $app['priority'] == 'Low' ? 'selected' : ''; ?>>Low</option>
                                                                                <option value="Medium" <?php echo $app['priority'] == 'Medium' ? 'selected' : ''; ?>>Medium</option>
                                                                                <option value="High" <?php echo $app['priority'] == 'High' ? 'selected' : ''; ?>>High</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Job Type -->
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label fw-semibold d-flex align-items-center">
                                                                            <i data-lucide="map-pin" class="me-2"
                                                                                style="width: 16px; height: 16px;"></i>
                                                                            Job Type
                                                                            <span class="save-indicator ms-2"
                                                                                data-field="job_type-<?php echo $app['id']; ?>">
                                                                                <i data-lucide="check"
                                                                                    style="width: 14px; height: 14px;"></i>
                                                                                Saved
                                                                            </span>
                                                                        </label>
                                                                        <div class="edit-field">
                                                                            <select class="form-select editable-field"
                                                                                data-app-id="<?php echo $app['id']; ?>"
                                                                                data-field="job_type">
                                                                                <option value="WFH" <?php echo $app['job_type'] == 'WFH' ? 'selected' : ''; ?>>Work From Home</option>
                                                                                <option value="WFO" <?php echo $app['job_type'] == 'WFO' ? 'selected' : ''; ?>>Work From Office</option>
                                                                                <option value="Hybrid" <?php echo $app['job_type'] == 'Hybrid' ? 'selected' : ''; ?>>Hybrid</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <!-- Salary -->
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label fw-semibold d-flex align-items-center">
                                                                            <i data-lucide="dollar-sign" class="me-2"
                                                                                style="width: 16px; height: 16px;"></i>
                                                                            Salary
                                                                            <span class="save-indicator ms-2"
                                                                                data-field="salary-<?php echo $app['id']; ?>">
                                                                                <i data-lucide="check"
                                                                                    style="width: 14px; height: 14px;"></i>
                                                                                Saved
                                                                            </span>
                                                                        </label>
                                                                        <div class="edit-field">
                                                                            <input type="number"
                                                                                class="form-control editable-field"
                                                                                data-app-id="<?php echo $app['id']; ?>"
                                                                                data-field="salary"
                                                                                value="<?php echo htmlspecialchars($app['salary'] ?? ''); ?>"
                                                                                placeholder="Annual salary">
                                                                        </div>
                                                                    </div>

                                                                    <!-- Location -->
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label fw-semibold d-flex align-items-center">
                                                                            <i data-lucide="map" class="me-2"
                                                                                style="width: 16px; height: 16px;"></i>
                                                                            Location
                                                                            <span class="save-indicator ms-2"
                                                                                data-field="location-<?php echo $app['id']; ?>">
                                                                                <i data-lucide="check"
                                                                                    style="width: 14px; height: 14px;"></i>
                                                                                Saved
                                                                            </span>
                                                                        </label>
                                                                        <div class="edit-field">
                                                                            <input type="text"
                                                                                class="form-control editable-field"
                                                                                data-app-id="<?php echo $app['id']; ?>"
                                                                                data-field="location"
                                                                                value="<?php echo htmlspecialchars($app['location'] ?? ''); ?>"
                                                                                placeholder="City, State">
                                                                        </div>
                                                                    </div>

                                                                    <!-- Job URL -->
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label fw-semibold d-flex align-items-center">
                                                                            <i data-lucide="link" class="me-2"
                                                                                style="width: 16px; height: 16px;"></i>
                                                                            Job URL
                                                                            <span class="save-indicator ms-2"
                                                                                data-field="job_url-<?php echo $app['id']; ?>">
                                                                                <i data-lucide="check"
                                                                                    style="width: 14px; height: 14px;"></i>
                                                                                Saved
                                                                            </span>
                                                                        </label>
                                                                        <div class="edit-field">
                                                                            <input type="url"
                                                                                class="form-control editable-field"
                                                                                data-app-id="<?php echo $app['id']; ?>"
                                                                                data-field="job_url"
                                                                                value="<?php echo htmlspecialchars($app['job_url'] ?? ''); ?>"
                                                                                placeholder="https://">
                                                                        </div>
                                                                    </div>

                                                                    <!-- Follow-up Date -->
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label fw-semibold d-flex align-items-center">
                                                                            <i data-lucide="calendar-days" class="me-2"
                                                                                style="width: 16px; height: 16px;"></i>
                                                                            Follow-up Date
                                                                            <span class="save-indicator ms-2"
                                                                                data-field="follow_up_date-<?php echo $app['id']; ?>">
                                                                                <i data-lucide="check"
                                                                                    style="width: 14px; height: 14px;"></i>
                                                                                Saved
                                                                            </span>
                                                                        </label>
                                                                        <div class="edit-field">
                                                                            <input type="date"
                                                                                class="form-control editable-field"
                                                                                data-app-id="<?php echo $app['id']; ?>"
                                                                                data-field="follow_up_date"
                                                                                value="<?php echo $app['follow_up_date'] ?? ''; ?>">
                                                                        </div>
                                                                    </div>

                                                                    <!-- Interview Date -->
                                                                    <div class="col-md-6">
                                                                        <label
                                                                            class="form-label fw-semibold d-flex align-items-center">
                                                                            <i data-lucide="video" class="me-2"
                                                                                style="width: 16px; height: 16px;"></i>
                                                                            Interview Date/Time
                                                                            <span class="save-indicator ms-2"
                                                                                data-field="interview_date-<?php echo $app['id']; ?>">
                                                                                <i data-lucide="check"
                                                                                    style="width: 14px; height: 14px;"></i>
                                                                                Saved
                                                                            </span>
                                                                        </label>
                                                                        <div class="edit-field">
                                                                            <input type="datetime-local"
                                                                                class="form-control editable-field"
                                                                                data-app-id="<?php echo $app['id']; ?>"
                                                                                data-field="interview_date"
                                                                                value="<?php echo $app['interview_date'] ? date('Y-m-d\TH:i', strtotime($app['interview_date'])) : ''; ?>">
                                                                        </div>
                                                                    </div>

                                                                    <!-- Notes -->
                                                                    <div class="col-12">
                                                                        <label
                                                                            class="form-label fw-semibold d-flex align-items-center">
                                                                            <i data-lucide="file-text" class="me-2"
                                                                                style="width: 16px; height: 16px;"></i>
                                                                            Notes
                                                                            <span class="save-indicator ms-2"
                                                                                data-field="notes-<?php echo $app['id']; ?>">
                                                                                <i data-lucide="check"
                                                                                    style="width: 14px; height: 14px;"></i>
                                                                                Saved
                                                                            </span>
                                                                        </label>
                                                                        <div class="edit-field">
                                                                            <textarea class="form-control editable-field"
                                                                                data-app-id="<?php echo $app['id']; ?>"
                                                                                data-field="notes" rows="3"
                                                                                placeholder="Add notes about this application..."><?php echo htmlspecialchars($app['notes'] ?? ''); ?></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Delete Button -->
                                                                <div class="mt-4 text-end">
                                                                    <button class="btn btn-outline-danger delete-btn"
                                                                        data-app-id="<?php echo $app['id']; ?>"
                                                                        data-company="<?php echo htmlspecialchars($app['company_name']); ?>"
                                                                        data-title="<?php echo htmlspecialchars($app['job_title']); ?>">
                                                                        <i data-lucide="trash-2"
                                                                            style="width: 16px; height: 16px;"></i>
                                                                        Delete Application
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Application Modal -->
    <div class="modal fade" id="addApplicationModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="company_name" name="company_name"
                                        placeholder="Start typing company name..." 
                                        autocomplete="off" required>
                                    <input type="hidden" id="company_website" name="company_website">
                                    
                                    <!-- Autocomplete Dropdown -->
                                    <div id="companyAutocomplete" class="autocomplete-dropdown" style="display: none;">
                                        <!-- Results will be populated here -->
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="job_title" class="form-label fw-semibold">
                                    Job Title <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="job_title" name="job_title"
                                    placeholder="e.g., Frontend Developer" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="job_url" class="form-label fw-semibold">Job Posting URL</label>
                                <input type="url" class="form-control" id="job_url" name="job_url"
                                    placeholder="https://...">
                            </div>
                            <div class="col-md-6">
                                <label for="salary" class="form-label fw-semibold">Salary (Annual)</label>
                                <input type="number" class="form-control" id="salary" name="salary"
                                    placeholder="e.g., 80000">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="location" class="form-label fw-semibold">Location</label>
                                <input type="text" class="form-control" id="location" name="location"
                                    placeholder="e.g., Remote, New York">
                            </div>
                            <div class="col-md-6">
                                <label for="job_type" class="form-label fw-semibold">Job Type</label>
                                <select class="form-select" id="job_type" name="job_type">
                                    <option value="WFH">Work From Home</option>
                                    <option value="WFO" selected>Work From Office</option>
                                    <option value="Hybrid">Hybrid</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="status_id" class="form-label fw-semibold">Application Status</label>
                                <select class="form-select" id="status_id" name="status_id">
                                    <?php foreach ($statuses as $status): ?>
                                        <option value="<?php echo $status['id']; ?>">
                                            <?php echo htmlspecialchars($status['status_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="priority" class="form-label fw-semibold">Priority</label>
                                <select class="form-select" id="priority" name="priority">
                                    <option value="Low">Low</option>
                                    <option value="Medium" selected>Medium</option>
                                    <option value="High">High</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="application_date" class="form-label fw-semibold">Application Date</label>
                                <input type="date" class="form-control" id="application_date" name="application_date"
                                    value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="follow_up_date" class="form-label fw-semibold">Follow-up Date</label>
                                <input type="date" class="form-control" id="follow_up_date" name="follow_up_date">
                            </div>
                            <div class="col-md-4">
                                <label for="interview_date" class="form-label fw-semibold">Interview Date/Time</label>
                                <input type="datetime-local" class="form-control" id="interview_date"
                                    name="interview_date">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="follow_up_email" class="form-label fw-semibold">Follow-up Email</label>
                            <input type="email" class="form-control" id="follow_up_email" name="follow_up_email"
                                placeholder="recruiter@company.com">
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label fw-semibold">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                placeholder="Add notes..."></textarea>
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

        // Toggle expandable details
        document.querySelectorAll('.app-row').forEach(row => {
            row.addEventListener('click', function (e) {
                if (e.target.closest('.editable-field') || e.target.closest('.delete-btn')) return;

                const appId = this.dataset.appId;
                const details = document.getElementById(`details-${appId}`);
                const icon = this.querySelector('.expand-icon');

                details.classList.toggle('show');
                icon.classList.toggle('rotated');
            });
        });

        // Auto-save system
        let saveTimeout;
        document.querySelectorAll('.editable-field').forEach(field => {
            field.addEventListener('change', function () {
                clearTimeout(saveTimeout);
                const element = this;
                const appId = this.dataset.appId;
                const fieldName = this.dataset.field;
                const value = this.value;

                saveTimeout = setTimeout(() => {
                    fetch('index.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `ajax_action=update_field&app_id=${appId}&field=${fieldName}&value=${encodeURIComponent(value)}`
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const indicator = document.querySelector(`[data-field="${fieldName}-${appId}"]`);
                                indicator.classList.add('show');
                                lucide.createIcons();
                                setTimeout(() => indicator.classList.remove('show'), 2000);

                                const mainRow = document.querySelector(`.app-row[data-app-id="${appId}"]`);
                                if (!mainRow) return;

                                if (fieldName === 'status_id') {
                                    const selectedText = element.options[element.selectedIndex].text;
                                    const statusBadge = mainRow.querySelector('.status-badge');

                                    if (statusBadge) {
                                        statusBadge.textContent = selectedText;
                                        const newStatusClass = 'status-' + selectedText.toLowerCase().replace(' ', '-');
                                        statusBadge.className = 'status-badge';
                                        statusBadge.classList.add(newStatusClass);
                                    }
                                }

                                if (fieldName === 'priority') {
                                    const priorityBadge = mainRow.querySelector('td:nth-child(6) .badge');
                                    if (priorityBadge) {
                                        priorityBadge.textContent = value;
                                        priorityBadge.classList.remove('bg-danger', 'bg-warning', 'bg-secondary');
                                        let newClass = 'bg-secondary';
                                        if (value === 'High') newClass = 'bg-danger';
                                        else if (value === 'Medium') newClass = 'bg-warning';
                                        priorityBadge.classList.add(newClass);
                                    }
                                }
                            }
                        });
                }, 500);
            });
        });

        // Delete application
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                const appId = this.dataset.appId;
                const company = this.dataset.company;
                const title = this.dataset.title;

                if (confirm(`Are you sure you want to delete the application for ${title} at ${company}?`)) {
                    fetch('index.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `ajax_action=delete_application&app_id=${appId}`
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            }
                        });
                }
            });
        });

        <?php if (!empty($add_app_error)): ?>
            var addModal = new bootstrap.Modal(document.getElementById('addApplicationModal'));
            addModal.show();
        <?php endif; ?>
        // Update active filter count badge
    function updateFilterBadge() {
        const urlParams = new URLSearchParams(window.location.search);
        let activeFilters = 0;
        
        if (urlParams.get('filter_status')) activeFilters++;
        if (urlParams.get('filter_job_type')) activeFilters++;
        if (urlParams.get('filter_priority')) activeFilters++;
        if (urlParams.get('filter_search')) activeFilters++;
        
        const badge = document.getElementById('activeFilterCount');
        if (activeFilters > 0) {
            badge.textContent = activeFilters;
            badge.style.display = 'inline-block';
        } else {
            badge.style.display = 'none';
        }
    }

    // Run on page load
    updateFilterBadge();

    // Auto-submit on filter change (optional - remove if you prefer manual Apply button)
    document.querySelectorAll('#filterForm select').forEach(select => {
        select.addEventListener('change', () => {
            document.getElementById('filterForm').submit();
        });
    });

    // Company Autocomplete
    const companyInput = document.getElementById('company_name');
    const companyWebsiteInput = document.getElementById('company_website');
    const autocompleteDropdown = document.getElementById('companyAutocomplete');
    let autocompleteTimeout;
    let selectedIndex = -1;
    let autocompleteResults = [];

    if (companyInput) {
        companyInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            clearTimeout(autocompleteTimeout);
            
            if (query.length < 2) {
                hideAutocomplete();
                return;
            }
            
            autocompleteTimeout = setTimeout(() => {
                fetchCompanies(query);
            }, 300);
        });
        
        companyInput.addEventListener('keydown', function(e) {
            const items = autocompleteDropdown.querySelectorAll('.autocomplete-item');
            
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                updateSelection(items);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                selectedIndex = Math.max(selectedIndex - 1, -1);
                updateSelection(items);
            } else if (e.key === 'Enter' && selectedIndex >= 0) {
                e.preventDefault();
                items[selectedIndex].click();
            } else if (e.key === 'Escape') {
                hideAutocomplete();
            }
        });
        
        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!companyInput.contains(e.target) && !autocompleteDropdown.contains(e.target)) {
                hideAutocomplete();
            }
        });
    }

    function fetchCompanies(query) {
        fetch(`../../src/api/search_companies.php?q=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(data => {
                autocompleteResults = data;
                displayAutocomplete(data);
            })
            .catch(error => {
                console.error('Autocomplete error:', error);
            });
    }

    function displayAutocomplete(companies) {
        if (companies.length === 0) {
            autocompleteDropdown.innerHTML = `
                <div class="autocomplete-no-results">
                    <i data-lucide="search" style="width: 20px; height: 20px;"></i>
                    <div class="mt-2">No companies found. Type to add a new one.</div>
                </div>
            `;
            autocompleteDropdown.style.display = 'block';
            lucide.createIcons();
            return;
        }
        
        const html = companies.map((company, index) => `
            <div class="autocomplete-item" data-index="${index}" data-name="${company.name}" data-website="${company.website || ''}">
                ${getCompanyLogoHTML(company)}
                <div>
                    <div class="fw-semibold">${company.name}</div>
                    ${company.website ? `<small class="text-muted">${company.website}</small>` : ''}
                </div>
            </div>
        `).join('');
        
        autocompleteDropdown.innerHTML = html;
        autocompleteDropdown.style.display = 'block';
        selectedIndex = -1;
        
        // Add click handlers
        autocompleteDropdown.querySelectorAll('.autocomplete-item').forEach(item => {
            item.addEventListener('click', function() {
                selectCompany(this.dataset.name, this.dataset.website);
            });
        });
    }

    function getCompanyLogoHTML(company) {
        if (company.logo_url && company.website) {
            return `
                <img src="${company.logo_url}" 
                    alt="${company.name}" 
                    class="company-logo-autocomplete"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                <div class="company-logo-fallback" style="display: none;">
                    ${getInitials(company.name)}
                </div>
            `;
        }
        return `
            <div class="company-logo-fallback">
                ${getInitials(company.name)}
            </div>
        `;
    }

    function getInitials(name) {
        return name
            .split(' ')
            .map(word => word[0])
            .join('')
            .toUpperCase()
            .substring(0, 2);
    }

    function selectCompany(name, website) {
        companyInput.value = name;
        companyWebsiteInput.value = website || '';
        hideAutocomplete();
    }

    function hideAutocomplete() {
        autocompleteDropdown.style.display = 'none';
        selectedIndex = -1;
    }

    function updateSelection(items) {
        items.forEach((item, index) => {
            if (index === selectedIndex) {
                item.classList.add('active');
                item.scrollIntoView({ block: 'nearest' });
            } else {
                item.classList.remove('active');
            }
        });
    }
    </script>
</body>

</html>