CREATE TABLE `pesan` (
  `id_Pesan` int NOT NULL AUTO_INCREMENT,
  `id_Pengirim` int NOT NULL,
  `id_Penerima` int NOT NULL,
  `Pesan` text NOT NULL,
  `time_Pengiriman` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_Pesan`)
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci