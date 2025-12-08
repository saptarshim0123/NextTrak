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
$results = array_map(function ($company) {
    return [
        'id' => $company['id'],
        'name' => $company['name'],
        'website' => $company['website'],
        'logo_url' => getCompanyLogo($company['website'])
    ];
}, $companies);

echo json_encode($results);

function getCompanyLogo($website)
{
    if (empty($website)) {
        return null;
    }

    // 1. Ensure a scheme/protocol is present for parse_url to work correctly
    $url = $website;
    if (strpos($url, '://') === false) {
        $url = 'http://' . $url;
    }

    // 2. Extract the host/domain name
    $host = parse_url($url, PHP_URL_HOST);

    if (empty($host)) {
        return null; // Could not extract a valid domain
    }

    // 3. Remove optional 'www.' prefix
    $domain = preg_replace('/^www\./i', '', $host);

    // Clearbit Logo API
    return "https://logo.clearbit.com/{$domain}";
}