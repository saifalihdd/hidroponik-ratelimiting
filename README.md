# Rate Limiting & Service Integration

---

## Deskripsi

Project ini mengimplementasikan dua service terpisah yang saling berkomunikasi melalui HTTP REST API, dilengkapi dengan mekanisme **Rate Limiting** pada service utama. Studi kasus yang digunakan adalah klasifikasi kondisi air hidroponik berdasarkan nilai pH dan kelembapan udara.

---

## Arsitektur

```
Client / Postman
      │
      ▼
Service A – Node.js (Port 3000)
  └── Rate Limiter (maks. 5 req/menit per IP)
  └── API Gateway → meneruskan ke Service B
      │
      ▼
Service B – PHP (Port 8080)
  └── Klasifikasi kondisi hidroponik
  └── Simpan riwayat ke MySQL
```

---

## Teknologi

| Layer | Teknologi |
|---|---|
| API Gateway | Node.js, Express.js, express-rate-limit, axios |
| Backend Service | PHP (Built-in Server) |
| Database | MySQL |
| Testing | Postman |

---

## Struktur Folder

```
pertemuan10/
├── service-a/
│   ├── package.json
│   └── server.js
│
└── service-b/
    ├── index.php
    ├── prediksi.php
    ├── riwayat.php
    ├── db.php
    └── init_db.sql
```

---

## Endpoints

### Service A – Node.js (`localhost:3000`)

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/` | Health check Service A |
| POST | `/klasifikasi` | Terima input, teruskan ke Service B, kembalikan hasil |
| GET | `/riwayat` | Ambil riwayat prediksi dari Service B |

### Service B – PHP (`localhost:8080`)

| Method | Endpoint | Deskripsi |
|---|---|---|
| GET | `/` | Health check Service B |
| POST | `/prediksi` | Klasifikasi kondisi air hidroponik |
| GET | `/riwayat` | Ambil riwayat prediksi dari database |

---

## Logika Klasifikasi

| Kondisi | Hasil Prediksi |
|---|---|
| pH < 5.5 | Terlalu Asam |
| pH > 7.5 | Terlalu Basa |
| Kelembapan < 60 | Kering |
| Kelembapan > 90 | Terlalu Lembap |
| Selain kondisi di atas | Ideal |

---

## Rate Limiting

Diterapkan di Service A menggunakan `express-rate-limit`:

- **Batas:** 5 request per menit per IP
- **Jika terlampaui:** HTTP `429 Too Many Requests`
- **Pesan:** *"Terlalu banyak permintaan. Coba lagi setelah 1 menit."*

---

## Cara Menjalankan

### Prasyarat
- Node.js v18+
- PHP 8+
- MySQL

### Langkah

1. Import `init_db.sql` ke MySQL
2. Sesuaikan konfigurasi database di `service-b/db.php`
3. Jalankan Service B: `php -S localhost:8080` di folder `service-b`
4. Jalankan Service A: `node server.js` di folder `service-a`

---

## Pengujian

Import file `Pertemuan10_RateLimiting.postman_collection.json` ke Postman.

Collection mencakup:
- Health check kedua service
- Semua skenario klasifikasi (Ideal, Terlalu Asam, Terlalu Basa, Kering, Terlalu Lembap)
- Test input tidak lengkap → `400 Bad Request`
- Test rate limit → `429 Too Many Requests`

---

## Konsep yang Dipelajari

**Rate Limiting** — membatasi jumlah request dari satu client dalam periode waktu tertentu. Request yang melampaui batas langsung ditolak dengan HTTP 429.

**Throttling** — mengendalikan laju pemrosesan request secara dinamis dengan cara mengantrekan atau memperlambat request, bukan langsung menolaknya.
