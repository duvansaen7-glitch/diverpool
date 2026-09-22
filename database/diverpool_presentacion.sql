-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: diverpool
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Current Database: `diverpool`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `diverpool` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;

USE `diverpool`;

--
-- Table structure for table `calificaciones`
--

DROP TABLE IF EXISTS `calificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `calificaciones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reserva_id` int(10) unsigned NOT NULL,
  `usuario_id` int(10) unsigned NOT NULL,
  `profesional_id` int(10) unsigned DEFAULT NULL,
  `puntuacion` tinyint(3) unsigned NOT NULL,
  `comentario` text DEFAULT NULL,
  `respuesta` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `reserva_id` (`reserva_id`),
  KEY `fk_calificaciones_usuario` (`usuario_id`),
  KEY `fk_calificaciones_profesional` (`profesional_id`),
  CONSTRAINT `fk_calificaciones_profesional` FOREIGN KEY (`profesional_id`) REFERENCES `profesionales` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_calificaciones_reserva` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_calificaciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_puntuacion` CHECK (`puntuacion` between 1 and 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calificaciones`
--

LOCK TABLES `calificaciones` WRITE;
/*!40000 ALTER TABLE `calificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `calificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias_servicios`
--

DROP TABLE IF EXISTS `categorias_servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categorias_servicios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias_servicios`
--

LOCK TABLES `categorias_servicios` WRITE;
/*!40000 ALTER TABLE `categorias_servicios` DISABLE KEYS */;
INSERT INTO `categorias_servicios` VALUES (1,'Peluquería','Cuidado estético e higiene',NULL,1,'2026-09-19 17:00:46'),(2,'Veterinaria','Atención veterinaria',NULL,1,'2026-09-19 17:00:46'),(3,'Terapias','Terapias y bienestar',NULL,1,'2026-09-19 17:00:46'),(4,'Guardería','Cuidado durante el día',NULL,1,'2026-09-19 17:00:46'),(5,'Guardería campestre','Cuidado en espacios abiertos',NULL,1,'2026-09-19 17:00:46'),(6,'Consultas','Consultas especializadas',NULL,1,'2026-09-19 17:00:46');
/*!40000 ALTER TABLE `categorias_servicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `disponibilidad`
--

DROP TABLE IF EXISTS `disponibilidad`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `disponibilidad` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `profesional_id` int(10) unsigned NOT NULL,
  `horario_id` int(10) unsigned NOT NULL,
  `fecha` date DEFAULT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `estado` enum('disponible','ocupado','bloqueado') DEFAULT 'disponible',
  PRIMARY KEY (`id`),
  KEY `fk_disponibilidad_profesional` (`profesional_id`),
  KEY `fk_disponibilidad_horario` (`horario_id`),
  CONSTRAINT `fk_disponibilidad_horario` FOREIGN KEY (`horario_id`) REFERENCES `horarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_disponibilidad_profesional` FOREIGN KEY (`profesional_id`) REFERENCES `profesionales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `disponibilidad`
--

LOCK TABLES `disponibilidad` WRITE;
/*!40000 ALTER TABLE `disponibilidad` DISABLE KEYS */;
/*!40000 ALTER TABLE `disponibilidad` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `especialidades`
--

DROP TABLE IF EXISTS `especialidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `especialidades` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `especialidades`
--

LOCK TABLES `especialidades` WRITE;
/*!40000 ALTER TABLE `especialidades` DISABLE KEYS */;
/*!40000 ALTER TABLE `especialidades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `especies`
--

DROP TABLE IF EXISTS `especies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `especies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `especies`
--

LOCK TABLES `especies` WRITE;
/*!40000 ALTER TABLE `especies` DISABLE KEYS */;
INSERT INTO `especies` VALUES (1,'Perro','Canino doméstico',1,'2026-09-19 17:00:46'),(2,'Gato','Felino doméstico',1,'2026-09-19 17:00:46'),(3,'Ave','Ave doméstica',1,'2026-09-19 17:00:46'),(4,'Conejo','Conejo doméstico',1,'2026-09-19 17:00:46'),(5,'Otro','Otra especie',1,'2026-09-19 17:00:46');
/*!40000 ALTER TABLE `especies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estados_reserva`
--

DROP TABLE IF EXISTS `estados_reserva`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `estados_reserva` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estados_reserva`
--

LOCK TABLES `estados_reserva` WRITE;
/*!40000 ALTER TABLE `estados_reserva` DISABLE KEYS */;
INSERT INTO `estados_reserva` VALUES (1,'Pendiente','Reserva creada esperando confirmación',1),(2,'Confirmada','Reserva confirmada',1),(3,'En proceso','Servicio actualmente en ejecución',1),(4,'Completada','Servicio terminado',1),(5,'Cancelada','Reserva cancelada',1);
/*!40000 ALTER TABLE `estados_reserva` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facturas`
--

DROP TABLE IF EXISTS `facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `facturas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reserva_id` int(10) unsigned NOT NULL,
  `numero` varchar(50) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `impuesto` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL,
  `fecha_emision` datetime DEFAULT current_timestamp(),
  `estado` enum('emitida','anulada') DEFAULT 'emitida',
  PRIMARY KEY (`id`),
  UNIQUE KEY `reserva_id` (`reserva_id`),
  UNIQUE KEY `numero` (`numero`),
  CONSTRAINT `fk_facturas_reserva` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facturas`
--

LOCK TABLES `facturas` WRITE;
/*!40000 ALTER TABLE `facturas` DISABLE KEYS */;
/*!40000 ALTER TABLE `facturas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_medico`
--

DROP TABLE IF EXISTS `historial_medico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `historial_medico` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mascota_id` int(10) unsigned NOT NULL,
  `profesional_id` int(10) unsigned DEFAULT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `tipo` varchar(100) NOT NULL,
  `diagnostico` text DEFAULT NULL,
  `tratamiento` text DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `archivo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_historial_mascota` (`mascota_id`),
  KEY `fk_historial_profesional` (`profesional_id`),
  CONSTRAINT `fk_historial_mascota` FOREIGN KEY (`mascota_id`) REFERENCES `mascotas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_historial_profesional` FOREIGN KEY (`profesional_id`) REFERENCES `profesionales` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_medico`
--

LOCK TABLES `historial_medico` WRITE;
/*!40000 ALTER TABLE `historial_medico` DISABLE KEYS */;
/*!40000 ALTER TABLE `historial_medico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `horarios`
--

DROP TABLE IF EXISTS `horarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `horarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sede_id` int(10) unsigned NOT NULL,
  `dia_semana` tinyint(3) unsigned NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `activo` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `fk_horarios_sede` (`sede_id`),
  CONSTRAINT `fk_horarios_sede` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_dia_semana` CHECK (`dia_semana` between 1 and 7)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `horarios`
--

LOCK TABLES `horarios` WRITE;
/*!40000 ALTER TABLE `horarios` DISABLE KEYS */;
INSERT INTO `horarios` VALUES (1,1,1,'08:00:00','17:00:00',1),(2,1,2,'08:00:00','17:00:00',1),(3,1,3,'08:00:00','17:00:00',1),(4,1,4,'08:00:00','17:00:00',1),(5,1,5,'08:00:00','17:00:00',1),(6,1,6,'08:00:00','17:00:00',1);
/*!40000 ALTER TABLE `horarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mascota_vacunas`
--

DROP TABLE IF EXISTS `mascota_vacunas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mascota_vacunas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mascota_id` int(10) unsigned NOT NULL,
  `vacuna_id` int(10) unsigned NOT NULL,
  `fecha_aplicacion` date NOT NULL,
  `fecha_proxima` date DEFAULT NULL,
  `veterinario` varchar(150) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_mascota_vacunas_mascota` (`mascota_id`),
  KEY `fk_mascota_vacunas_vacuna` (`vacuna_id`),
  CONSTRAINT `fk_mascota_vacunas_mascota` FOREIGN KEY (`mascota_id`) REFERENCES `mascotas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_mascota_vacunas_vacuna` FOREIGN KEY (`vacuna_id`) REFERENCES `vacunas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mascota_vacunas`
--

LOCK TABLES `mascota_vacunas` WRITE;
/*!40000 ALTER TABLE `mascota_vacunas` DISABLE KEYS */;
/*!40000 ALTER TABLE `mascota_vacunas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mascotas`
--

DROP TABLE IF EXISTS `mascotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mascotas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(10) unsigned NOT NULL,
  `especie_id` int(10) unsigned NOT NULL,
  `raza_id` int(10) unsigned DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `sexo` enum('macho','hembra') NOT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `peso` decimal(6,2) DEFAULT NULL,
  `color` varchar(80) DEFAULT NULL,
  `microchip` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `esterilizado` tinyint(1) DEFAULT 0,
  `observaciones` text DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `microchip` (`microchip`),
  KEY `fk_mascotas_usuario` (`usuario_id`),
  KEY `fk_mascotas_especie` (`especie_id`),
  KEY `fk_mascotas_raza` (`raza_id`),
  CONSTRAINT `fk_mascotas_especie` FOREIGN KEY (`especie_id`) REFERENCES `especies` (`id`),
  CONSTRAINT `fk_mascotas_raza` FOREIGN KEY (`raza_id`) REFERENCES `razas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_mascotas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mascotas`
--

LOCK TABLES `mascotas` WRITE;
/*!40000 ALTER TABLE `mascotas` DISABLE KEYS */;
INSERT INTO `mascotas` VALUES (1,1,2,NULL,'manchas','macho','2022-03-15',6.70,'varios',NULL,NULL,1,'es muy canson no deja dormir',0,'2026-09-21 03:33:20','2026-09-21 17:53:43'),(2,1,1,NULL,'yoooo','macho','2006-12-15',80.00,'no se varios',NULL,'uploads/mascotas/mascota_2_4e1ad684c59f4ed0.png',1,NULL,0,'2026-09-21 03:55:27','2026-09-21 17:53:32'),(3,1,2,NULL,'prueba','macho','2019-12-15',8.50,'blancoc',NULL,NULL,0,NULL,0,'2026-09-21 03:59:16','2026-09-21 17:53:36'),(4,1,2,NULL,'marina','hembra','2009-03-15',46.00,'blanco',NULL,NULL,0,'i like my',0,'2026-09-21 17:36:26','2026-09-21 17:53:40'),(5,1,3,NULL,'hola','hembra','2000-12-15',80.00,'rojo',NULL,'uploads/mascotas/mascota_1_dc5a5ebab4b2a4e3.jpg',1,NULL,0,'2026-09-21 17:41:07','2026-09-21 17:53:38'),(6,1,2,NULL,'manchas','macho','2022-05-22',8.50,'varios colores',NULL,'uploads/mascotas/mascota_1_ca1ec9db1ed116ab.jpg',1,'es muy cariñoso',0,'2026-09-21 17:56:58','2026-09-22 03:06:00'),(7,1,2,NULL,'melman','macho','2023-06-15',6.30,'naranja con blanco',NULL,'uploads/mascotas/mascota_1_65f132bcf6bfa70d.jpg',1,'el de Madagascar jajjaj',0,'2026-09-21 18:09:26','2026-09-22 03:06:04'),(8,1,1,NULL,'coffi','macho','2007-03-05',9.30,'blanco',NULL,'uploads/mascotas/mascota_8_cc8c7d0638cb40b0.jpg',1,'el coffi',0,'2026-09-21 18:10:13','2026-09-22 03:05:57'),(9,1,4,NULL,'mariana','hembra','2009-03-15',4.50,'no se',NULL,NULL,1,'te quiero muchooooooo',0,'2026-09-21 18:15:04','2026-09-21 18:39:39'),(10,1,5,NULL,'yooooo','macho','2006-12-15',85.00,'no se bro',NULL,NULL,0,'holaaaaaaaaaa',0,'2026-09-21 18:50:49','2026-09-21 18:51:31'),(11,1,2,NULL,'michi','hembra','2023-03-15',6.30,'multicolor',NULL,'uploads/mascotas/mascota_11_64f626d97ae4a5eb.jpg',0,'el michi miau',0,'2026-09-21 19:09:57','2026-09-22 03:06:06'),(12,5,2,NULL,'michico','hembra','2024-09-05',5.30,'multicolor',NULL,'uploads/mascotas/mascota_5_f07b35a250fe859b.jpg',0,'michiiiiiiiiiiiiiiii',1,'2026-09-22 00:45:24','2026-09-22 00:45:24'),(13,1,2,NULL,'hola','macho','2006-12-15',8.30,'muchos',NULL,'uploads/mascotas/mascota_1_1319b40a2edf624b.jpg',0,'ponga cuidado',0,'2026-09-22 03:07:13','2026-09-22 03:07:22'),(14,1,2,NULL,'melman','macho','2023-05-12',5.30,'naranja con blanco',NULL,'uploads/mascotas/mascota_1_43e17c9f3c4d8928.jpg',1,'el de madagascar',1,'2026-09-22 14:52:03','2026-09-22 14:52:03');
/*!40000 ALTER TABLE `mascotas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metodos_pago`
--

DROP TABLE IF EXISTS `metodos_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `metodos_pago` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(80) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metodos_pago`
--

LOCK TABLES `metodos_pago` WRITE;
/*!40000 ALTER TABLE `metodos_pago` DISABLE KEYS */;
INSERT INTO `metodos_pago` VALUES (1,'Efectivo','Pago realizado en el establecimiento',1),(2,'Tarjeta','Pago mediante tarjeta',1),(3,'Transferencia','Transferencia bancaria',1),(4,'PSE','Pago electrónico mediante PSE',1);
/*!40000 ALTER TABLE `metodos_pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notificaciones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(10) unsigned NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `mensaje` text NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `referencia_id` int(10) unsigned DEFAULT NULL,
  `leida` tinyint(1) DEFAULT 0,
  `fecha_lectura` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_notificaciones_usuario` (`usuario_id`),
  CONSTRAINT `fk_notificaciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notificaciones`
--

LOCK TABLES `notificaciones` WRITE;
/*!40000 ALTER TABLE `notificaciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `notificaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos`
--

DROP TABLE IF EXISTS `pagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pagos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reserva_id` int(10) unsigned NOT NULL,
  `metodo_pago_id` int(10) unsigned NOT NULL,
  `referencia` varchar(150) DEFAULT NULL,
  `monto` decimal(12,2) NOT NULL,
  `estado` enum('pendiente','aprobado','rechazado','reembolsado') DEFAULT 'pendiente',
  `fecha_pago` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_pagos_reserva` (`reserva_id`),
  KEY `fk_pagos_metodo` (`metodo_pago_id`),
  CONSTRAINT `fk_pagos_metodo` FOREIGN KEY (`metodo_pago_id`) REFERENCES `metodos_pago` (`id`),
  CONSTRAINT `fk_pagos_reserva` FOREIGN KEY (`reserva_id`) REFERENCES `reservas` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos`
--

LOCK TABLES `pagos` WRITE;
/*!40000 ALTER TABLE `pagos` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permisos`
--

DROP TABLE IF EXISTS `permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permisos` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `modulo` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos`
--

LOCK TABLES `permisos` WRITE;
/*!40000 ALTER TABLE `permisos` DISABLE KEYS */;
/*!40000 ALTER TABLE `permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profesional_especialidades`
--

DROP TABLE IF EXISTS `profesional_especialidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profesional_especialidades` (
  `profesional_id` int(10) unsigned NOT NULL,
  `especialidad_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`profesional_id`,`especialidad_id`),
  KEY `fk_prof_especialidad_especialidad` (`especialidad_id`),
  CONSTRAINT `fk_prof_especialidad_especialidad` FOREIGN KEY (`especialidad_id`) REFERENCES `especialidades` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_prof_especialidad_profesional` FOREIGN KEY (`profesional_id`) REFERENCES `profesionales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profesional_especialidades`
--

LOCK TABLES `profesional_especialidades` WRITE;
/*!40000 ALTER TABLE `profesional_especialidades` DISABLE KEYS */;
/*!40000 ALTER TABLE `profesional_especialidades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profesionales`
--

DROP TABLE IF EXISTS `profesionales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profesionales` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(10) unsigned NOT NULL,
  `numero_tarjeta_profesional` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `experiencia_anios` int(10) unsigned DEFAULT 0,
  `foto` varchar(255) DEFAULT NULL,
  `estado` enum('activo','inactivo','vacaciones') DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `fk_profesionales_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profesionales`
--

LOCK TABLES `profesionales` WRITE;
/*!40000 ALTER TABLE `profesionales` DISABLE KEYS */;
INSERT INTO `profesionales` VALUES (1,4,'TP-001','Profesional encargado de la atención y cuidado de mascotas.',3,NULL,'vacaciones','2026-09-21 23:19:10','2026-09-22 13:52:40'),(2,2,'TP-002',NULL,1,NULL,'activo','2026-09-22 14:14:26','2026-09-22 15:18:58'),(3,8,'TP-003',NULL,0,NULL,'inactivo','2026-09-22 17:29:01','2026-09-22 17:30:51');
/*!40000 ALTER TABLE `profesionales` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER asignar_servicios_al_crear_profesional
AFTER INSERT ON profesionales
FOR EACH ROW
INSERT IGNORE INTO servicio_profesionales (
    servicio_id,
    profesional_id
)
SELECT
    s.id,
    NEW.id
FROM servicios s
WHERE s.estado = 'activo' */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `razas`
--

DROP TABLE IF EXISTS `razas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `razas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `especie_id` int(10) unsigned NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_raza_especie` (`especie_id`,`nombre`),
  CONSTRAINT `fk_razas_especie` FOREIGN KEY (`especie_id`) REFERENCES `especies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `razas`
--

LOCK TABLES `razas` WRITE;
/*!40000 ALTER TABLE `razas` DISABLE KEYS */;
/*!40000 ALTER TABLE `razas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reservas`
--

DROP TABLE IF EXISTS `reservas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reservas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usuario_id` int(10) unsigned NOT NULL,
  `mascota_id` int(10) unsigned NOT NULL,
  `servicio_id` int(10) unsigned NOT NULL,
  `profesional_id` int(10) unsigned DEFAULT NULL,
  `sede_id` int(10) unsigned NOT NULL,
  `estado_id` int(10) unsigned NOT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `precio` decimal(12,2) NOT NULL DEFAULT 0.00,
  `observaciones` text DEFAULT NULL,
  `motivo_cancelacion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_reservas_usuario` (`usuario_id`),
  KEY `fk_reservas_mascota` (`mascota_id`),
  KEY `fk_reservas_servicio` (`servicio_id`),
  KEY `fk_reservas_profesional` (`profesional_id`),
  KEY `fk_reservas_sede` (`sede_id`),
  KEY `fk_reservas_estado` (`estado_id`),
  CONSTRAINT `fk_reservas_estado` FOREIGN KEY (`estado_id`) REFERENCES `estados_reserva` (`id`),
  CONSTRAINT `fk_reservas_mascota` FOREIGN KEY (`mascota_id`) REFERENCES `mascotas` (`id`),
  CONSTRAINT `fk_reservas_profesional` FOREIGN KEY (`profesional_id`) REFERENCES `profesionales` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_reservas_sede` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`),
  CONSTRAINT `fk_reservas_servicio` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`),
  CONSTRAINT `fk_reservas_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reservas`
--

LOCK TABLES `reservas` WRITE;
/*!40000 ALTER TABLE `reservas` DISABLE KEYS */;
INSERT INTO `reservas` VALUES (1,1,7,3,1,1,4,'2026-09-22','08:00:00','08:30:00',20000.00,NULL,NULL,'2026-09-22 00:35:32','2026-09-22 18:02:49'),(2,5,12,3,1,1,5,'2026-09-22','11:30:00','12:00:00',20000.00,NULL,NULL,'2026-09-22 00:45:46','2026-09-22 18:02:35'),(3,5,12,3,1,1,4,'2026-09-22','08:30:00','09:00:00',20000.00,NULL,NULL,'2026-09-22 00:48:52','2026-09-22 18:02:42'),(4,5,12,6,1,1,2,'2026-09-24','11:00:00','12:00:00',70000.00,NULL,NULL,'2026-09-22 03:08:46','2026-09-22 18:02:20'),(5,1,14,1,2,1,4,'2026-09-23','08:00:00','09:00:00',35000.00,NULL,NULL,'2026-09-22 15:24:46','2026-09-22 18:03:02');
/*!40000 ALTER TABLE `reservas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rol_permisos`
--

DROP TABLE IF EXISTS `rol_permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rol_permisos` (
  `rol_id` int(10) unsigned NOT NULL,
  `permiso_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`rol_id`,`permiso_id`),
  KEY `fk_rol_permisos_permiso` (`permiso_id`),
  CONSTRAINT `fk_rol_permisos_permiso` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_rol_permisos_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rol_permisos`
--

LOCK TABLES `rol_permisos` WRITE;
/*!40000 ALTER TABLE `rol_permisos` DISABLE KEYS */;
/*!40000 ALTER TABLE `rol_permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Administrador','Administración completa del sistema',1,'2026-09-19 17:00:46'),(2,'Cliente','Usuario propietario de mascotas',1,'2026-09-19 17:00:46'),(3,'Profesional','Profesional encargado de prestar servicios',1,'2026-09-19 17:00:46');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sedes`
--

DROP TABLE IF EXISTS `sedes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sedes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) NOT NULL,
  `direccion` varchar(255) NOT NULL,
  `ciudad` varchar(100) NOT NULL DEFAULT 'Bogotá',
  `telefono` varchar(30) DEFAULT NULL,
  `correo` varchar(150) DEFAULT NULL,
  `latitud` decimal(10,7) DEFAULT NULL,
  `longitud` decimal(10,7) DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sedes`
--

LOCK TABLES `sedes` WRITE;
/*!40000 ALTER TABLE `sedes` DISABLE KEYS */;
INSERT INTO `sedes` VALUES (1,'Diverpoool Mascotas','Dirección pendiente','Bogotá','3000000000','contacto@diverpoool.com',NULL,NULL,1,'2026-09-19 17:00:46');
/*!40000 ALTER TABLE `sedes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servicio_profesionales`
--

DROP TABLE IF EXISTS `servicio_profesionales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `servicio_profesionales` (
  `servicio_id` int(10) unsigned NOT NULL,
  `profesional_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`servicio_id`,`profesional_id`),
  KEY `fk_servicio_prof_profesional` (`profesional_id`),
  CONSTRAINT `fk_servicio_prof_profesional` FOREIGN KEY (`profesional_id`) REFERENCES `profesionales` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_servicio_prof_servicio` FOREIGN KEY (`servicio_id`) REFERENCES `servicios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicio_profesionales`
--

LOCK TABLES `servicio_profesionales` WRITE;
/*!40000 ALTER TABLE `servicio_profesionales` DISABLE KEYS */;
INSERT INTO `servicio_profesionales` VALUES (1,1),(1,2),(1,3),(2,1),(2,2),(2,3),(3,1),(3,2),(3,3),(4,1),(4,2),(4,3),(5,1),(5,2),(5,3),(6,1),(6,2),(6,3),(7,1),(7,2),(7,3),(8,1),(8,2),(8,3),(9,1),(9,2),(9,3),(10,1),(10,2),(10,3),(11,1),(11,2),(11,3),(12,1),(12,2),(12,3);
/*!40000 ALTER TABLE `servicio_profesionales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servicios`
--

DROP TABLE IF EXISTS `servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `servicios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `categoria_id` int(10) unsigned NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `duracion_minutos` int(10) unsigned NOT NULL,
  `precio` decimal(12,2) NOT NULL DEFAULT 0.00,
  `imagen` varchar(255) DEFAULT NULL,
  `requiere_profesional` tinyint(1) DEFAULT 1,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_servicios_categoria` (`categoria_id`),
  CONSTRAINT `fk_servicios_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_servicios` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servicios`
--

LOCK TABLES `servicios` WRITE;
/*!40000 ALTER TABLE `servicios` DISABLE KEYS */;
INSERT INTO `servicios` VALUES (1,1,'Baño y secado','Baño completo, secado y cepillado de la mascota.',60,35000.00,NULL,1,'activo','2026-09-21 23:07:32','2026-09-21 23:07:32'),(2,1,'Corte y baño','Baño completo acompañado de corte de pelo.',90,50000.00,NULL,1,'activo','2026-09-21 23:07:32','2026-09-21 23:07:32'),(3,1,'Corte de uñas','Corte y cuidado de uñas de la mascota.',30,20000.00,NULL,1,'activo','2026-09-21 23:07:32','2026-09-21 23:07:32'),(4,2,'Consulta veterinaria','Consulta general con profesional veterinario.',45,60000.00,NULL,1,'activo','2026-09-21 23:07:32','2026-09-21 23:07:32'),(5,2,'Valoración veterinaria','Valoración general del estado de salud de la mascota.',30,45000.00,NULL,1,'activo','2026-09-21 23:07:32','2026-09-21 23:07:32'),(6,3,'Terapia física','Sesión de terapia física para mascotas.',60,70000.00,NULL,1,'activo','2026-09-21 23:07:32','2026-09-21 23:07:32'),(7,3,'Terapia de recuperación','Sesión orientada a recuperación y movilidad.',60,75000.00,NULL,1,'activo','2026-09-21 23:07:32','2026-09-21 23:07:32'),(8,4,'Guardería medio día','Servicio de cuidado durante media jornada.',240,45000.00,NULL,1,'activo','2026-09-21 23:07:32','2026-09-21 23:07:32'),(9,4,'Guardería día completo','Servicio de cuidado durante una jornada completa.',480,70000.00,NULL,1,'activo','2026-09-21 23:07:32','2026-09-21 23:07:32'),(10,5,'Guardería campestre','Jornada de cuidado y actividades al aire libre.',480,90000.00,NULL,1,'activo','2026-09-21 23:07:32','2026-09-21 23:07:32'),(11,6,'Consulta comportamental','Consulta especializada sobre comportamiento de la mascota.',60,80000.00,NULL,1,'activo','2026-09-21 23:07:32','2026-09-21 23:07:32'),(12,6,'Consulta nutricional','Orientación nutricional para la mascota.',45,65000.00,NULL,1,'activo','2026-09-21 23:07:32','2026-09-21 23:07:32');
/*!40000 ALTER TABLE `servicios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuarios` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rol_id` int(10) unsigned NOT NULL,
  `nombres` varchar(100) NOT NULL,
  `apellidos` varchar(100) NOT NULL,
  `correo` varchar(150) NOT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `documento` varchar(30) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `estado` enum('activo','inactivo','bloqueado') NOT NULL DEFAULT 'activo',
  `ultimo_acceso` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`),
  UNIQUE KEY `documento` (`documento`),
  KEY `fk_usuarios_rol` (`rol_id`),
  CONSTRAINT `fk_usuarios_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,2,'Duvan Felipe','Saenz Reyes','pruebas@diverpool.test','3022436201','$2y$10$yYxL20JFL8DiyYNS8nhGW.ZwrC2MmTyCn.GcEqDSMzcGpp8MgrYzC','1032940154','uploads/perfiles/usuario_1_83bd4e43f365a1d4.jpg','activo','2026-09-22 10:24:28','2026-09-20 23:23:45','2026-09-22 15:24:28'),(2,3,'mariana','riaño','pruebas@diverpool.com','3010000000','$2y$10$X1SVjsetN6XHcsrQcHrF6OevSvburH3M3LNZYO/9J8vP2MLIKR10.','1555554444',NULL,'activo','2026-09-22 12:29:29','2026-09-20 23:43:23','2026-09-22 17:29:29'),(3,1,'Administrador','Principal','admin@diverpool.test','3000000000','$2y$10$lJ7DwFH45ZF5sgFgUH4uquyafEdDQQ7F8ryEOiRepz/7rJi38efvG','1000000000',NULL,'activo','2026-09-22 13:05:42','2026-09-21 20:03:54','2026-09-22 18:05:42'),(4,3,'Carlos','Veterinario','profesional@diverpool.test','3001112233','$2y$10$YyrluG7R/oJwi0eGQU8WI.VBIXxMoUoorGgSUcq5D6SJ9QBd6ERF2','PROF001',NULL,'activo',NULL,'2026-09-21 23:18:57','2026-09-21 23:20:43'),(5,2,'mariana','riaño','mariana.prueba@gmail.com','3142862332','$2y$10$gzyejZe61IYgAD.v2RZDzOb.4h1WkpE40pZ4DmB9tOiYUW2cPG57q','1032942565',NULL,'activo','2026-09-22 12:29:21','2026-09-22 00:44:10','2026-09-22 17:29:21'),(8,3,'manchas','david','duvansaen7@gmail.com','3022436203','$2y$10$ni.r/d21R3nE6bpvFUnSSuxF85Gfh1cyw5b8QPe10uQqlxWEygiYu','1032940153',NULL,'activo','2026-09-22 12:29:09','2026-09-22 17:28:12','2026-09-22 17:29:09');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vacunas`
--

DROP TABLE IF EXISTS `vacunas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `vacunas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `periodicidad_meses` int(10) unsigned DEFAULT NULL,
  `estado` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vacunas`
--

LOCK TABLES `vacunas` WRITE;
/*!40000 ALTER TABLE `vacunas` DISABLE KEYS */;
/*!40000 ALTER TABLE `vacunas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'diverpool'
--
