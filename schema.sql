-- PERINGATAN: Perintah di bawah ini akan MENGHAPUS SELURUH DATABASE 'leksikon' Anda
-- dan membuat ulang dari awal. Jalankan dengan hati-hati.
DROP DATABASE IF EXISTS `leksikon`;
CREATE DATABASE `leksikon` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `leksikon`;

-- Skema basis data ini dirancang agar berfungsi dengan aplikasi CodeIgniter saat ini.
-- Ini mencakup peningkatan seperti stempel waktu (timestamps) yang konsisten dan dukungan set karakter utf8mb4.
-- Hapus (DROP) tabel jika sudah ada untuk menghindari error.
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

--
-- Struktur Tabel untuk `users`
--
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Struktur Tabel untuk `sumber`
--
DROP TABLE IF EXISTS `sumber`;
CREATE TABLE `sumber` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_sumber` varchar(150) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Struktur Tabel untuk `entri`
--
DROP TABLE IF EXISTS `entri`;
CREATE TABLE `entri` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `term` varchar(255) NOT NULL,
  `definition` text NOT NULL,
  `sumber_id` int(11) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sumber_id` (`sumber_id`),
  CONSTRAINT `entri_sumber_fk` FOREIGN KEY (`sumber_id`) REFERENCES `sumber` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Menambahkan indeks FULLTEXT secara terpisah
ALTER TABLE `entri` ADD FULLTEXT `entri_fulltext` (`term`, `definition`);

--
-- Struktur Tabel untuk `user_favorites`
--
DROP TABLE IF EXISTS `user_favorites`;
CREATE TABLE `user_favorites` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED NOT NULL,
  `entri_id` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_entry_unique` (`user_id`,`entri_id`),
  KEY `user_id` (`user_id`),
  KEY `entri_id` (`entri_id`),
  CONSTRAINT `favorites_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `favorites_entri_fk` FOREIGN KEY (`entri_id`) REFERENCES `entri` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Struktur Tabel untuk `contoh_penggunaan`
--
DROP TABLE IF EXISTS `contoh_penggunaan`;
CREATE TABLE `contoh_penggunaan` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `entri_id` INT(11) UNSIGNED NOT NULL,
    `contoh_teks` TEXT NOT NULL,
    `terjemahan` TEXT,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `entri_id_fk` (`entri_id`),
    CONSTRAINT `contoh_entri_fk` FOREIGN KEY (`entri_id`) REFERENCES `entri` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Struktur Tabel untuk `quiz_attempts`
--
DROP TABLE IF EXISTS `quiz_attempts`;
CREATE TABLE `quiz_attempts` (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) UNSIGNED NOT NULL,
    `score` INT(5) NOT NULL,
    `attempt_date` DATETIME NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id_fk` (`user_id`),
    CONSTRAINT `quiz_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
