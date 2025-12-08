<?php
require_once '../../config/config.php';
require_once '../../config/database.php';
require_once '../../src/classes/Company.php';

header('Content-Type: application/json');

if (!isset($_GET['q'])) {
    echo json_encode([]);
    exit;
}

$search = trim($_GET['q']);

if (strlen($search) < 2) {
    echo json_encode([]);
    exit;
}

$companyObj = new Company($pdo);
$companies = $companyObj->search($search);

// Format results with logo URLs
$results = array_map(function($company) {
    return [
        'id' => $company['id'],
        'name' => $company['name'],
        'website' => $company['website'],
        'logo_url' => getCompanyLogo($company['website'])
    ];
}, $companies);

echo json_encode($results);

function getCompanyLogo($website) {
    if (empty($website)) {
        return null;
    }
    
    // Remove www. and get domain
    $domain = preg_replace('/^www\./i', '', $website);
    
    // Clearbit Logo API (free, no auth)
    return "https://logo.clearbit.com/{$domain}";
}