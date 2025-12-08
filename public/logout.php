<?php

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../config/session_config.php';
require_once '../src/core/functions.php';
require_once '../src/classes/Auth.php';

// Create Auth instance and logout
$auth = new Auth($pdo);
$auth->logout();

// Set flash message
setFlashMessage('You have been logged out successfully.', 'success');

// Redirect to landing page
redirect('/public/index.php');