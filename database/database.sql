CREATE DATABASE IF NOT EXISTS leksikon;
USE leksikon;

-- TABEL 1: SUMBER
-- Ini untuk menyimpan data sumber seperti "Bausastra Jawa"
-- atau "Javanese-English Dictionary".

CREATE TABLE `sumber` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_sumber` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  PRIMARY KEY (`id`)
)

-- TABEL 2: ENTRI
CREATE TABLE `entri` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term` varchar(255) NOT NULL,
  `definition` text NOT NULL,
  `sumber_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `term` (`term`),
  KEY `sumber_id` (`sumber_id`),
  CONSTRAINT `entri_ibfk_1` FOREIGN KEY (`sumber_id`) REFERENCES `sumber` (`id`) ON DELETE SET NULL
)

-- TABEL 3: CONTOH PENGGUNAAN
-- Satu 'entri' bisa punya banyak 'contoh'.

CREATE TABLE `contoh_penggunaan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entri_id` int(11) NOT NULL,
  `contoh_teks` text NOT NULL,
  `terjemahan` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `entri_id` (`entri_id`),
  CONSTRAINT `contoh_penggunaan_ibfk_1` FOREIGN KEY (`entri_id`) REFERENCES `entri` (`id`) ON DELETE CASCADE
)

-- Untuk insert ada di folder seed

CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nama` VARCHAR(255) NOT NULL,
  `username` VARCHAR(255) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` VARCHAR(50) NOT NULL DEFAULT 'user',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
);