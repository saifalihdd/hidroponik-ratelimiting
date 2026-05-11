<?php
// Aktifkan CORS agar bisa dipanggil dari service lain
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Tangani preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Parsing path URL
$requestUri  = $_SERVER['REQUEST_URI'];
$path        = parse_url($requestUri, PHP_URL_PATH);
$path        = rtrim($path, '/');
$method      = $_SERVER['REQUEST_METHOD'];

// Routing sederhana
if ($path === '/prediksi' && $method === 'POST') {
    require __DIR__ . '/prediksi.php';

} elseif ($path === '/riwayat' && $method === 'GET') {
    require __DIR__ . '/riwayat.php';

} elseif ($path === '' || $path === '/') {
    echo json_encode([
        'service' => 'Service B - PHP ML Classifier',
        'status'  => 'running',
        'endpoints' => [
            'POST /prediksi' => 'Klasifikasi kondisi hidroponik',
            'GET  /riwayat'  => 'Ambil riwayat prediksi',
        ],
    ]);

} else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint tidak ditemukan.']);
}