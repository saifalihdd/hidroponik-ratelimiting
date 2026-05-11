<?php
require __DIR__ . '/db.php';

// Baca body JSON dari request
$body = json_decode(file_get_contents('php://input'), true);

if (!isset($body['ph']) || !isset($body['lembap_udara'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Input tidak lengkap. Diperlukan: ph dan lembap_udara.']);
    exit;
}

$ph          = (float) $body['ph'];
$lembap_udara = (float) $body['lembap_udara'];

// LOGIKA KLASIFIKASI KONDISI AIR HIDROPONIK
function klasifikasi(float $ph, float $lembap): array {
    if ($ph < 5.5) {
        return ['prediksi' => 'Terlalu Asam',   'nilai_confidence' => 0.90];
    }
    if ($ph > 7.5) {
        return ['prediksi' => 'Terlalu Basa',   'nilai_confidence' => 0.90];
    }
    if ($lembap < 60) {
        return ['prediksi' => 'Kering',         'nilai_confidence' => 0.85];
    }
    if ($lembap > 90) {
        return ['prediksi' => 'Terlalu Lembap', 'nilai_confidence' => 0.85];
    }
    return ['prediksi' => 'Ideal',              'nilai_confidence' => 0.95];
}

$hasil = klasifikasi($ph, $lembap_udara);

// SIMPAN HASIL KE DATABASE
$disimpan = false;
try {
    $pdo  = getDB();
    $stmt = $pdo->prepare(
        'INSERT INTO riwayat_prediksi (ph, lembap_udara, prediksi, confidence)
         VALUES (:ph, :lembap, :prediksi, :confidence)'
    );
    $stmt->execute([
        ':ph'         => $ph,
        ':lembap'     => $lembap_udara,
        ':prediksi'   => $hasil['prediksi'],
        ':confidence' => $hasil['nilai_confidence'],
    ]);
    $disimpan = true;
} catch (PDOException $e) {
    // Log error tapi jangan gagalkan response
    error_log('DB Error: ' . $e->getMessage());
}

// KEMBALIKAN HASIL
http_response_code(200);
echo json_encode([
    'prediksi'         => $hasil['prediksi'],
    'nilai_confidence' => $hasil['nilai_confidence'],
    'disimpan'         => $disimpan,
]);