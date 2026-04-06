CREATE TABLE `kontak` (
  `id_kontak` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `nama` varchar(50) NOT NULL,
  `nomor` varchar(20) NOT NULL,
  `id_penerima` int NOT NULL,
  PRIMARY KEY (`id_kontak`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci