<?php
require __DIR__ . '/db.php';

try {
    $pdo  = getDB();
    $stmt = $pdo->query(
        'SELECT id, ph, lembap_udara, prediksi, confidence, created_at
         FROM riwayat_prediksi
         ORDER BY created_at DESC
         LIMIT 50'
    );
    $data = $stmt->fetchAll();

    http_response_code(200);
    echo json_encode([
        'total'  => count($data),
        'data'   => $data,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Gagal mengambil data.', 'detail' => $e->getMessage()]);
}