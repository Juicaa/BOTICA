-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 20, 2025 at 02:59 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: boticabienestar
--

-- --------------------------------------------------------

--
-- Table structure for table alertas
--

CREATE TABLE alertas (
  id_alerta int NOT NULL,
  tipo enum('vencimiento','stock_bajo') NOT NULL,
  mensaje text NOT NULL,
  id_lote int NOT NULL,
  atendida tinyint(1) NOT NULL DEFAULT '0',
  fecha_generada datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table categorias
--

CREATE TABLE categorias (
  id_categoria int NOT NULL,
  nombre varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table categorias
--

INSERT INTO categorias (id_categoria, nombre) VALUES
(1, 'Analgésicos y Antipiréticos'),
(2, 'Antibióticos'),
(3, 'Antiinflamatorios'),
(4, 'Antigripales y Antialérgicos'),
(5, 'Antitusivos y Expectorantes'),
(6, 'Gastrointestinales'),
(7, 'Antifúngicos'),
(8, 'Anticonceptivos'),
(9, 'Hipotensores y Cardiovasculares'),
(10, 'Antidiabéticos'),
(11, 'Vitaminas y Minerales'),
(12, 'Psicofármacos'),
(13, 'Tópicos y Primeros Auxilios');

-- --------------------------------------------------------

--
-- Table structure for table clientes
--

CREATE TABLE clientes (
  id_cliente int NOT NULL,
  dni char(8) NOT NULL,
  nombre_completo varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table clientes
--

INSERT INTO clientes (id_cliente, dni, nombre_completo) VALUES
(1, '72495052', 'WALTER DAVID SANTOS MENDIETA NAPAN'),
(2, '15353027', 'ALEYDA CASTRO MORENO'),
(3, '75364125', 'KATHERIN SELENY MISAICO BENDEZU'),
(4, '73142548', 'MARX ENGELS LOPEZ TAFUR'),
(5, '73142582', 'JARED LOZANO PANDURO'),
(6, '15413417', 'FREDEN HERMES JUICA SERVA'),
(7, '04412417', 'MARTIN ALBERTO VIZCARRA CORNEJO');

-- --------------------------------------------------------

--
-- Table structure for table lotes
--

CREATE TABLE lotes (
  id_lote int NOT NULL,
  id_medicamento int NOT NULL,
  cantidad int NOT NULL,
  fecha_ingreso date NOT NULL,
  fecha_vencimiento date NOT NULL,
  precio_unitario decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table lotes
--

INSERT INTO lotes (id_lote, id_medicamento, cantidad, fecha_ingreso, fecha_vencimiento, precio_unitario) VALUES
(1, 1, 82, '2025-06-01', '2026-06-01', '0.50'),
(2, 2, 105, '2025-06-01', '2026-06-01', '0.80'),
(3, 3, 75, '2025-06-02', '2026-05-15', '1.10'),
(4, 4, 90, '2025-06-02', '2026-05-30', '0.70'),
(5, 5, 68, '2025-06-03', '2026-05-10', '0.90'),
(6, 6, 97, '2025-06-01', '2026-04-30', '1.50'),
(7, 7, 100, '2025-06-01', '2026-03-30', '1.80'),
(8, 8, 100, '2025-06-01', '2026-04-15', '1.40'),
(9, 9, 100, '2025-06-01', '2026-05-10', '1.70'),
(10, 10, 100, '2025-06-01', '2026-06-01', '2.00'),
(11, 11, 90, '2025-06-02', '2026-06-02', '1.10'),
(12, 12, 90, '2025-06-02', '2026-06-02', '1.30'),
(13, 13, 90, '2025-06-02', '2026-06-02', '1.20'),
(14, 14, 5, '2025-06-03', '2026-06-03', '0.50'),
(15, 15, 25, '2025-06-03', '2026-06-03', '0.60'),
(16, 16, 80, '2025-06-03', '2026-06-03', '0.55'),
(17, 17, 10, '2025-06-04', '2026-06-04', '0.75'),
(18, 18, 60, '2025-06-04', '2026-06-04', '0.80'),
(19, 19, 60, '2025-06-04', '2026-06-04', '0.85'),
(20, 20, 70, '2025-06-05', '2026-06-05', '0.95'),
(21, 21, 70, '2025-06-05', '2026-06-05', '0.50'),
(22, 22, 70, '2025-06-05', '2026-06-05', '1.25'),
(23, 23, 70, '2025-06-05', '2026-06-05', '0.45');

-- --------------------------------------------------------

--
-- Table structure for table medicamentos
--

CREATE TABLE medicamentos (
  id_medicamento int NOT NULL,
  nombre varchar(100) NOT NULL,
  id_categoria int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table medicamentos
--

INSERT INTO medicamentos (id_medicamento, nombre, id_categoria) VALUES
(1, 'Paracetamol', 1),
(2, 'Ibuprofeno', 1),
(3, 'Naproxeno', 1),
(4, 'Metamizol', 1),
(5, 'Diclofenaco', 1),
(6, 'Amoxicilina', 2),
(7, 'Azitromicina', 2),
(8, 'Cefalexina', 2),
(9, 'Ciprofloxacino', 2),
(10, 'Doxiciclina', 2),
(11, 'Piroxicam', 3),
(12, 'Ketoprofeno', 3),
(13, 'Meloxicam', 3),
(14, 'Loratadina', 4),
(15, 'Cetirizina', 4),
(16, 'Clorfenamina', 4),
(17, 'Dextrometorfano', 5),
(18, 'Ambroxol', 5),
(19, 'Bromhexina', 5),
(20, 'Loperamida', 6),
(21, 'Sales de Rehidratación Oral', 6),
(22, 'Omeprazol', 6),
(23, 'Ranitidina', 6),
(24, 'Lactulosa', 6),
(25, 'Clotrimazol', 7),
(26, 'Miconazol', 7),
(27, 'Fluconazol', 7),
(28, 'Anticonceptivos orales combinados', 8),
(29, 'Anticonceptivos inyectables', 8),
(30, 'Enalapril', 9),
(31, 'Losartán', 9),
(32, 'Amlodipino', 9),
(33, 'Metformina', 10),
(34, 'Glibenclamida', 10),
(35, 'Insulina', 10),
(36, 'Multivitamínicos', 11),
(37, 'Vitamina C', 11),
(38, 'Vitamina D', 11),
(39, 'Hierro', 11),
(40, 'Calcio', 11),
(41, 'Fluoxetina', 12),
(42, 'Sertralina', 12),
(43, 'Diazepam', 12),
(44, 'Clonazepam', 12),
(45, 'Neomicina', 13),
(46, 'Bacitracina', 13),
(47, 'Óxido de zinc', 13),
(48, 'Alcohol', 13),
(49, 'Povidona yodada', 13);

-- --------------------------------------------------------

--
-- Table structure for table salidalotes
--

CREATE TABLE salidalotes (
  id_salida int NOT NULL,
  id_lote int NOT NULL,
  id_venta int NOT NULL,
  cantidad int NOT NULL,
  fecha_salida datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table salidalotes
--

INSERT INTO salidalotes (id_salida, id_lote, id_venta, cantidad, fecha_salida) VALUES
(1, 3, 1, 2, '2025-06-19 13:43:12'),
(2, 5, 2, 2, '2025-06-19 13:47:01'),
(3, 2, 2, 6, '2025-06-19 13:47:01'),
(4, 2, 3, 6, '2025-06-19 13:50:31'),
(5, 3, 4, 3, '2025-06-19 14:06:02'),
(6, 1, 5, 3, '2025-06-19 14:34:36'),
(7, 5, 5, 5, '2025-06-19 14:34:36'),
(8, 2, 5, 3, '2025-06-19 14:34:36'),
(9, 1, 6, 5, '2025-06-19 19:24:58'),
(10, 6, 6, 3, '2025-06-19 19:24:58'),
(11, 1, 7, 10, '2025-06-19 21:26:45');

-- --------------------------------------------------------

--
-- Table structure for table usuarios
--

CREATE TABLE usuarios (
  id_usuario int NOT NULL,
  usuario varchar(100) NOT NULL,
  contraseña varchar(255) NOT NULL,
  rol enum('administrador','vendedor') NOT NULL,
  token_recuperacion varchar(255) DEFAULT NULL,
  expira_token datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table usuarios
--

INSERT INTO usuarios (id_usuario, usuario, contraseña, rol, token_recuperacion, expira_token) VALUES
(1, 'David', 'david929113', 'administrador', NULL, NULL),
(2, 'Juica', 'juica123456', 'vendedor', NULL, NULL),
(3, 'Becerra', 'becerra123', 'vendedor', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table ventas
--

CREATE TABLE ventas (
  id_venta int NOT NULL,
  fecha datetime DEFAULT CURRENT_TIMESTAMP,
  total decimal(10,2) NOT NULL,
  id_usuario int NOT NULL,
  id_cliente int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table ventas
--

INSERT INTO ventas (id_venta, fecha, total, id_usuario, id_cliente) VALUES
(1, '2025-06-19 13:43:12', '2.20', 2, 1),
(2, '2025-06-19 13:47:01', '6.60', 2, 2),
(3, '2025-06-19 13:50:31', '4.80', 2, 3),
(4, '2025-06-19 14:06:02', '3.30', 2, 4),
(5, '2025-06-19 14:34:36', '8.40', 2, 5),
(6, '2025-06-19 19:24:58', '7.00', 2, 6),
(7, '2025-06-19 21:26:45', '5.00', 2, 7);

--
-- Indexes for dumped tables
--

--
-- Indexes for table alertas
--
ALTER TABLE alertas
  ADD PRIMARY KEY (id_alerta),
  ADD KEY id_lote (id_lote);

--
-- Indexes for table categorias
--
ALTER TABLE categorias
  ADD PRIMARY KEY (id_categoria);

--
-- Indexes for table clientes
--
ALTER TABLE clientes
  ADD PRIMARY KEY (id_cliente),
  ADD UNIQUE KEY dni (dni);

--
-- Indexes for table lotes
--
ALTER TABLE lotes
  ADD PRIMARY KEY (id_lote),
  ADD KEY id_medicamento (id_medicamento);

--
-- Indexes for table medicamentos
--
ALTER TABLE medicamentos
  ADD PRIMARY KEY (id_medicamento),
  ADD KEY id_categoria (id_categoria);

--
-- Indexes for table salidalotes
--
ALTER TABLE salidalotes
  ADD PRIMARY KEY (id_salida),
  ADD KEY id_lote (id_lote),
  ADD KEY id_venta (id_venta);

--
-- Indexes for table usuarios
--
ALTER TABLE usuarios
  ADD PRIMARY KEY (id_usuario);

--
-- Indexes for table ventas
--
ALTER TABLE ventas
  ADD PRIMARY KEY (id_venta),
  ADD KEY id_usuario (id_usuario),
  ADD KEY id_cliente (id_cliente);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table alertas
--
ALTER TABLE alertas
  MODIFY id_alerta int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table categorias
--
ALTER TABLE categorias
  MODIFY id_categoria int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table clientes
--
ALTER TABLE clientes
  MODIFY id_cliente int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table lotes
--
ALTER TABLE lotes
  MODIFY id_lote int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table medicamentos
--
ALTER TABLE medicamentos
  MODIFY id_medicamento int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table salidalotes
--
ALTER TABLE salidalotes
  MODIFY id_salida int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table usuarios
--
ALTER TABLE usuarios
  MODIFY id_usuario int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table ventas
--
ALTER TABLE ventas
  MODIFY id_venta int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table alertas
--
ALTER TABLE alertas
  ADD CONSTRAINT alertas_ibfk_1 FOREIGN KEY (id_lote) REFERENCES lotes (id_lote);

--
-- Constraints for table lotes
--
ALTER TABLE lotes
  ADD CONSTRAINT lotes_ibfk_1 FOREIGN KEY (id_medicamento) REFERENCES medicamentos (id_medicamento);

--
-- Constraints for table medicamentos
--
ALTER TABLE medicamentos
  ADD CONSTRAINT medicamentos_ibfk_1 FOREIGN KEY (id_categoria) REFERENCES categorias (id_categoria);

--
-- Constraints for table salidalotes
--
ALTER TABLE salidalotes
  ADD CONSTRAINT salidalotes_ibfk_1 FOREIGN KEY (id_lote) REFERENCES lotes (id_lote),
  ADD CONSTRAINT salidalotes_ibfk_2 FOREIGN KEY (id_venta) REFERENCES ventas (id_venta);

--
-- Constraints for table ventas
--
ALTER TABLE ventas
  ADD CONSTRAINT ventas_ibfk_1 FOREIGN KEY (id_usuario) REFERENCES usuarios (id_usuario),
  ADD CONSTRAINT ventas_ibfk_2 FOREIGN KEY (id_cliente) REFERENCES clientes (id_cliente) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;