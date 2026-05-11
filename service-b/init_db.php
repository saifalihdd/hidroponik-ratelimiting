CREATE DATABASE IF NOT EXISTS 2410511081_db_hidroponik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE 2410511081_db_hidroponik;

CREATE TABLE IF NOT EXISTS riwayat_prediksi (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  ph          FLOAT        NOT NULL,
  lembap_udara FLOAT       NOT NULL,
  prediksi    VARCHAR(50)  NOT NULL,
  confidence  FLOAT        NOT NULL,
  created_at  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
);