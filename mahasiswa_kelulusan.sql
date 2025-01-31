-- Adminer 4.8.1 MySQL 8.0.30 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `mahasiswa_kelulusan`;
CREATE TABLE `mahasiswa_kelulusan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `ipk` float NOT NULL,
  `jumlah_sks` int NOT NULL,
  `jumlah_semester` int NOT NULL,
  `status_kelulusan` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `mahasiswa_kelulusan` (`id`, `nama`, `ipk`, `jumlah_sks`, `jumlah_semester`, `status_kelulusan`) VALUES
(21,	'Andi Saputra',	3.5,	144,	8,	'Tepat Waktu'),
(22,	'Budi Santoso',	2.8,	120,	10,	'Tidak Tepat Waktu'),
(23,	'Citra Maharani',	3.2,	135,	9,	'Tepat Waktu'),
(24,	'Dewi Lestari',	3.75,	150,	8,	'Tepat Waktu'),
(25,	'Eka Pratama',	2.5,	110,	11,	'Tidak Tepat Waktu'),
(26,	'Fajar Nugroho',	3.1,	132,	9,	'Tepat Waktu'),
(27,	'Gina Wulandari',	3.4,	138,	8,	'Tepat Waktu'),
(28,	'Hadi Purnomo',	2.9,	125,	10,	'Tidak Tepat Waktu'),
(29,	'Indah Permata',	3.6,	140,	8,	'Tepat Waktu'),
(30,	'Joko Susanto',	2.7,	115,	11,	'Tidak Tepat Waktu'),
(31,	'Kiki Ramadhan',	3.8,	155,	7,	'Tepat Waktu'),
(32,	'Lina Kusuma',	3,	130,	9,	'Tepat Waktu'),
(33,	'Maman Suherman',	2.6,	118,	10,	'Tidak Tepat Waktu'),
(34,	'Nina Safitri',	3.9,	160,	7,	'Tepat Waktu'),
(35,	'Oki Firmansyah',	3.3,	137,	8,	'Tepat Waktu'),
(36,	'Putri Ayu',	2.85,	122,	10,	'Tidak Tepat Waktu'),
(37,	'Qory Rizky',	3.55,	145,	8,	'Tepat Waktu'),
(38,	'Rina Setiawan',	3,	128,	9,	'Tepat Waktu'),
(39,	'Samsul Bahri',	2.75,	119,	11,	'Tidak Tepat Waktu'),
(40,	'Tari Anindita',	3.7,	150,	8,	'Tepat Waktu');

-- 2025-01-31 02:06:04
