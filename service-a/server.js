const express = require('express');
const rateLimit = require('express-rate-limit');
const axios = require('axios');

const app = express();
const PORT = 4081;

const SERVICE_B_URL = process.env.SERVICE_B_URL || 'http://103.147.92.134:4181';

app.use(express.json());

// KONFIGURASI RATE LIMITER (Maks. 5 request per menit per IP)
const limiter = rateLimit({
  windowMs: 60 * 1000,   
  max: 5,          
  standardHeaders: true,
  legacyHeaders: false,
  message: {
    status: 429,
    error: 'Terlalu banyak permintaan. Coba lagi setelah 1 menit.',
  },
});

// Terapkan rate limiter ke semua route
app.use(limiter);

// ROUTE: GET / (Halaman utama (health check))
app.get('/', (req, res) => {
  res.json({
    service: 'Service A - API Gateway',
    status: 'running',
    rateLimit: 'Maks. 5 request per menit per IP',
  });
});

// ROUTE: POST /klasifikasi
// Terima input → teruskan ke Service B → kembalikan hasil
app.post('/klasifikasi', async (req, res) => {
  const { ph, lembap_udara } = req.body;

  // Validasi input
  if (ph === undefined || lembap_udara === undefined) {
    return res.status(400).json({
      error: 'Input tidak lengkap. Diperlukan: ph dan lembap_udara.',
    });
  }

  try {
    // Kirim request ke Service B (PHP)
    const response = await axios.post(`${SERVICE_B_URL}/prediksi`, {
      ph,
      lembap_udara,
    });

    const hasilB = response.data;

    // Kembalikan hasil ke client
    return res.status(200).json({
      input: { ph, lembap_udara },
      prediksi: hasilB.prediksi,
      model_confidence: hasilB.nilai_confidence,
      disimpan_ke_db: hasilB.disimpan ?? false,
    });
  } catch (err) {
    console.error('Error menghubungi Service B:', err.message);
    return res.status(502).json({
      error: 'Gagal menghubungi Service B.',
      detail: err.message,
    });
  }
});

// ROUTE: GET /riwayat
// Ambil riwayat prediksi dari Service B
app.get('/riwayat', async (req, res) => {
  try {
    const response = await axios.get(`${SERVICE_B_URL}/riwayat`);
    return res.status(200).json(response.data);
  } catch (err) {
    return res.status(502).json({ error: 'Gagal mengambil riwayat.', detail: err.message });
  }
});

// JALANKAN SERVER
app.listen(PORT, () => {
  console.log(`[Service A] Berjalan di http://localhost:${PORT}`);
  console.log(`[Service A] Terhubung ke Service B: ${SERVICE_B_URL}`);
});