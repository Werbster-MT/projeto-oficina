CREATE DATABASE  IF NOT EXISTS `oficina` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci */;
USE `oficina`;
-- MySQL dump 10.13  Distrib 8.0.36, for Win64 (x86_64)
--
-- Host: localhost    Database: oficina
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `material`
--

DROP TABLE IF EXISTS `material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `material` (
  `id_material` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `quantidade` int(11) NOT NULL,
  `unidade_medida` varchar(50) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_material`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `material`
--

LOCK TABLES `material` WRITE;
/*!40000 ALTER TABLE `material` DISABLE KEYS */;
INSERT INTO `material` VALUES (1,'Óleo de Motor','Óleo sintético para motores 5W-30',0,'litro',30.00),(2,'Filtro de Óleo','Filtro de óleo para motor',20,'unidade',15.00),(3,'Pneu','Pneu radial aro 16',88,'unidade',250.00),(4,'Velas de Ignição','Conjunto de 4 velas de ignição',192,'conjunto',40.00),(5,'Farol Palio','Farol do fiat palio',48,'',200.00);
/*!40000 ALTER TABLE `material` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servico`
--

DROP TABLE IF EXISTS `servico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `servico` (
  `id_servico` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `descricao` text NOT NULL,
  `data_inicio` datetime NOT NULL,
  `data_fim` datetime DEFAULT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_servico`),
  KEY `usuario` (`usuario`),
  CONSTRAINT `servico_ibfk_1` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servico`
--

LOCK TABLES `servico` WRITE;
/*!40000 ALTER TABLE `servico` DISABLE KEYS */;
INSERT INTO `servico` VALUES (1,'Troca de Óleo','Troca de óleo e filtros','2024-07-15 00:00:00','2024-07-15 00:00:00','mecanicoCarlos',150.00),(2,'Alinhamento','Alinhamento das rodas','2024-07-16 00:00:00','2024-07-16 00:00:00','mecanicoCarlos',1100.00),(3,'Freios','Manutenção de freios','2024-07-18 00:00:00','2024-07-19 00:00:00','mecanicoCarlos',700.00),(4,'Suspensão','Reparo de suspensão','2024-07-20 09:00:00','2024-07-22 10:00:00','mecanicoCarlos',700.00),(5,'Troca de Pneus','Substituição de pneus desgastados','2024-07-25 11:00:00','2024-07-25 12:30:00','mecanicoCarlos',400.00),(6,'Balanceamento','Balanceamento de rodas','2024-07-26 08:00:00','2024-07-26 09:00:00','mecanicoCarlos',120.00),(7,'Revisão Geral','Revisão completa do veículo','2024-07-27 09:00:00','2024-07-27 16:00:00','mecanicoCarlos',600.00),(8,'Troca de Bateria','Substituição da bateria do veículo','2024-07-28 10:00:00','2024-07-28 10:30:00','mecanicoCarlos',180.00),(9,'Troca de Correia Dentada','Substituição da correia dentada','2024-07-29 08:00:00','2024-07-29 12:00:00','mecanicoCarlos',350.00),(10,'Ar Condicionado','Manutenção do sistema de ar condicionado','2024-07-30 09:00:00','2024-07-30 11:00:00','mecanicoCarlos',250.00),(11,'Troca de Velas','Substituição das velas de ignição','2024-07-31 08:00:00','2024-07-31 09:00:00','mecanicoCarlos',80.00),(12,'Limpeza de Bicos Injetores','Limpeza do sistema de injeção eletrônica','2024-08-01 10:00:00','2024-08-01 11:30:00','mecanicoCarlos',150.00),(13,'Manutenção','manutenção geral','2024-07-18 00:00:00','2024-07-19 00:00:00','mecanico',200.00),(16,'Velas','velas trocadas','2024-07-18 00:00:00','2024-07-18 00:00:00','mecanicoCarlos',100.00),(17,'Manutençao Palio','manutenção palio','2024-07-19 00:00:00','2024-07-19 00:00:00','mecanicoCarlos',130.00),(18,'Manutenção Gol','manutenção gol','2024-07-19 00:00:00','2024-07-19 00:00:00','mecanicoCarlos',0.00),(19,'Manutenção Mobi','manutenção mobi','2024-07-19 00:00:00','2024-07-19 00:00:00','mecanicoCarlos',100.00);
/*!40000 ALTER TABLE `servico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servico_material`
--

DROP TABLE IF EXISTS `servico_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `servico_material` (
  `id_servico` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_servico`,`id_material`),
  KEY `id_material` (`id_material`),
  CONSTRAINT `servico_material_ibfk_1` FOREIGN KEY (`id_servico`) REFERENCES `servico` (`id_servico`),
  CONSTRAINT `servico_material_ibfk_2` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servico_material`
--

LOCK TABLES `servico_material` WRITE;
/*!40000 ALTER TABLE `servico_material` DISABLE KEYS */;
INSERT INTO `servico_material` VALUES (1,1,3,30.00,90.00),(1,2,1,15.00,15.00),(2,3,4,250.00,1000.00),(3,3,2,250.00,500.00),(13,3,1,30.00,30.00),(16,4,4,200.00,800.00),(17,1,1,30.00,30.00);
/*!40000 ALTER TABLE `servico_material` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `usuario` varchar(100) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('admin','vendedor','mecanico','almoxarifado') NOT NULL,
  PRIMARY KEY (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES ('admin','admin','$2y$10$NDBsQRI7v5aWaSgT.sQhMO37XaPFVGOyDCDVONCFbVo87dKnS8he6','admin'),('adminJoao','João Silva','$2y$10$mxtSnjcjP/zeZJ8frSNAYeW8FEFJn9FuhhjPySToZXt8hnWrW4mwO','admin'),('almoxarifadoAna','Ana Costa','$2y$10$m2XfI87WB92B9y7jAfhSHe0gK8SJHSW52TrgmYSAyUx4OvsOxolBa','almoxarifado'),('mecanico','mecanico','$2y$10$0zayyyL62hKRxFK1shZs2.99ad1zfZ8d8dlnsEokimAPHKlNRSMVW','mecanico'),('mecanicoCarlos','Carlos Santos','$2y$10$pPzlz8i7nyth/vDvWSVDFe/XMIqc2a4dGTXdubG.ATNygVzacC2z6','mecanico'),('vendedorMaria','Maria Oliveira','$2y$10$SbRcPo1Tx2XR8g0Qe4xpoOu/f3zCtm6M9cGfLXPYc5Z0SbIpwnAe6','vendedor');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `venda`
--

DROP TABLE IF EXISTS `venda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `venda` (
  `id_venda` int(11) NOT NULL AUTO_INCREMENT,
  `data` datetime NOT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_venda`),
  KEY `usuario` (`usuario`),
  CONSTRAINT `venda_ibfk_1` FOREIGN KEY (`usuario`) REFERENCES `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `venda`
--

LOCK TABLES `venda` WRITE;
/*!40000 ALTER TABLE `venda` DISABLE KEYS */;
INSERT INTO `venda` VALUES (2,'2024-07-16 11:00:00','vendedorMaria',1000.00),(3,'2024-07-18 11:04:00','vendedorMaria',0.00),(6,'2024-07-18 00:00:00','vendedorMaria',150.00),(7,'2024-07-16 00:00:00','vendedorMaria',100.00),(8,'2024-07-26 00:00:00','vendedorMaria',100.00),(9,'2024-07-20 00:00:00','vendedorMaria',200.00),(10,'2024-07-19 00:00:00','vendedorMaria',0.00),(11,'2024-07-28 00:00:00','vendedorMaria',0.00),(12,'2024-07-19 00:00:00','vendedorMaria',150.00),(13,'2024-07-19 00:00:00','vendedorMaria',180.00);
/*!40000 ALTER TABLE `venda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `venda_material`
--

DROP TABLE IF EXISTS `venda_material`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `venda_material` (
  `id_venda` int(11) NOT NULL,
  `id_material` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_venda`,`id_material`),
  KEY `id_material` (`id_material`),
  CONSTRAINT `venda_material_ibfk_1` FOREIGN KEY (`id_venda`) REFERENCES `venda` (`id_venda`),
  CONSTRAINT `venda_material_ibfk_2` FOREIGN KEY (`id_material`) REFERENCES `material` (`id_material`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `venda_material`
--

LOCK TABLES `venda_material` WRITE;
/*!40000 ALTER TABLE `venda_material` DISABLE KEYS */;
INSERT INTO `venda_material` VALUES (2,3,4,250.00,1000.00),(6,1,1,25.00,25.00),(7,5,2,50.00,100.00),(8,1,2,30.00,60.00),(9,4,4,50.00,200.00),(10,1,4,30.00,120.00),(12,1,5,30.00,150.00),(13,1,6,30.00,180.00);
/*!40000 ALTER TABLE `venda_material` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-07-19 17:44:32
