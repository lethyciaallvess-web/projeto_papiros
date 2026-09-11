-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: projeto_papiros
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
-- Current Database: `projeto_papiros`
--

CREATE DATABASE /*!32312 IF NOT EXISTS*/ `projeto_papiros` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `projeto_papiros`;

--
-- Table structure for table `categoria`
--

DROP TABLE IF EXISTS `categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nome_categoria` varchar(100) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categoria`
--

LOCK TABLES `categoria` WRITE;
/*!40000 ALTER TABLE `categoria` DISABLE KEYS */;
INSERT INTO `categoria` VALUES (1,'Cadeiras'),(2,'Mesas'),(3,'Armários'),(4,'Longarinas');
/*!40000 ALTER TABLE `categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produto`
--

DROP TABLE IF EXISTS `produto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produto` (
  `id_produto` int(11) NOT NULL AUTO_INCREMENT,
  `nome_produto` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL,
  `quantidade_estoque` int(11) NOT NULL DEFAULT 0,
  `valor_unitario` decimal(10,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`id_produto`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto`
--

LOCK TABLES `produto` WRITE;
/*!40000 ALTER TABLE `produto` DISABLE KEYS */;
INSERT INTO `produto` VALUES (1,'Cadeira Secretária Fixa','Prática e funcional, ideal para escritórios, recepções e ambientes corporativos.','cadeira-secretaria-fixa.png',10,499.90),(2,'Cadeira Presidente','Elegante e confortável, perfeita para escritórios e home offices.','cadeira-presidente.png',3,499.90),(3,'Cadeira Diretor','Moderna e confortável, ideal para ambientes corporativos e home office.','cadeira-diretor.png',15,499.90),(4,'Cadeira Secretária Giratória','Confortável e versátil, ideal para escritórios e ambientes de trabalho.','cadeira-secretaria-giratoria.png',2,499.90),(5,'Cadeira Gamer','Desenvolvida para máximo conforto e desempenho.','cadeira-gamer.png',8,499.90),(6,'Cadeira Reunião','Elegante e confortável, ideal para salas de reunião.','cadeira-reuniao.png',0,499.90),(7,'Cadeira de Plástico','Leve, resistente e prática para diversos ambientes.','cadeira-plastico.png',0,499.90),(8,'Cadeira Obeso','Desenvolvida para oferecer maior resistência, segurança e conforto.','cadeira-obeso.png',0,499.90),(9,'Cadeira Universitária','Prática e funcional, ideal para salas de aula, treinamentos e palestras.','cadeira-universitaria.png',0,499.90),(10,'Mocho','Assento prático e confortável para diversos ambientes.','mocho.png',4,499.90),(11,'Longarina Estofada 3 Lugares','Ideal para recepções, clínicas e salas de espera. Possui assentos estofados e capacidade para 3 pessoas.','longarina-estofada-3.png',12,949.90),(12,'Longarina Estofada 4 Lugares','Ideal para recepções, clínicas e salas de espera. Possui assentos estofados e capacidade para 4 pessoas.','longarina-estofada-4.png',5,949.90),(13,'Longarina Estofada 5 Lugares','Ideal para recepções, clínicas e salas de espera. Possui assentos estofados e capacidade para 5 pessoas.','longarina-estofada-5.png',0,949.90),(14,'Longarina Metalizada 3 Lugares','Estrutura metálica resistente com capacidade para 3 pessoas.','longarina-metalizada-3.png',0,949.90),(15,'Longarina Metalizada 4 Lugares','Estrutura metálica resistente com capacidade para 4 pessoas.','longarina-metalizada-4.png',0,949.90),(16,'Longarina Metalizada 5 Lugares','Estrutura metálica resistente com capacidade para 5 pessoas.','longarina-metalizada-5.png',0,949.90),(17,'Longarina Polipropileno 3 Lugares','Fabricada em polipropileno de alta durabilidade, com 3 lugares.','longarina-polipropileno-3.png',0,949.90),(18,'Longarina Polipropileno 4 Lugares','Fabricada em polipropileno de alta durabilidade, com 4 lugares.','longarina-polipropileno-4.png',0,949.90),(19,'Longarina Polipropileno 5 Lugares','Fabricada em polipropileno de alta durabilidade, com 5 lugares.','longarina-polipropileno-5.png',0,949.90),(20,'Mesa em L','Mesa em formato L, ideal para escritórios e ambientes corporativos.','mesa-L.png',20,799.90),(21,'Mesa Escolar Infantil','Mesa desenvolvida para crianças, ideal para escolas e espaços educativos.','mesa-escolar-infantil.png',1,799.90),(22,'Mesa para Computador','Mesa prática e funcional para computadores e home office.','mesa-computador.png',0,799.90),(23,'Mesa Escolar','Mesa resistente e funcional para salas de aula e ambientes educacionais.','mesa-escolar.png',0,799.90),(24,'Mesa Secretária','Mesa compacta e versátil para escritórios e ambientes de trabalho.','mesa-secretaria.png',0,799.90),(25,'Mesa Executiva','Mesa sofisticada e espaçosa para ambientes corporativos e executivos.','mesa-executiva.png',0,799.90),(26,'Armário Alto com Porta','Armário alto com portas, ideal para armazenamento em escritórios e escolas.','armario-alto-porta.png',7,1299.90),(27,'Armário de Aço para Escritório','Armário de aço resistente para organização de documentos e materiais.','armario-aco-escritorio.png',3,1299.90),(28,'Armário Baixo para Escritório','Armário baixo funcional para apoio e armazenamento em ambientes corporativos.','armario-baixo-escritorio.png',0,1299.90),(29,'Armário Arquivo','Armário desenvolvido para arquivamento e organização de documentos.','armario-arquivo.png',0,1299.90),(30,'Armário Escolar','Armário resistente para armazenamento de materiais escolares.','armario-escolar.png',0,1299.90),(31,'Armário Guarda-Volume','Ideal para guarda de pertences em escolas, empresas e vestiários.','armario-guarda-volume.png',0,1299.90),(32,'Armário de Madeira para Escritório','Armário elegante em madeira para ambientes corporativos.','armario-madeira-escritorio.png',0,1299.90),(33,'Armário Biblioteca','Armário projetado para organização de livros e materiais didáticos.','armario-biblioteca.png',0,1299.90),(34,'Armário Porta de Correr','Armário com portas deslizantes para melhor aproveitamento de espaço.','armario-porta-correr.png',0,1299.90),(35,'Armário Professor','Armário funcional para uso em salas de aula e ambientes educacionais.','armario-prof.png',0,1299.90);
/*!40000 ALTER TABLE `produto` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 trigger trg_produto_estoque_positivo
before update on produto
for each row
set new.quantidade_estoque = abs(new.quantidade_estoque) */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `produto_categoria`
--

DROP TABLE IF EXISTS `produto_categoria`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `produto_categoria` (
  `id_produto` int(11) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  PRIMARY KEY (`id_produto`,`id_categoria`),
  KEY `id_categoria` (`id_categoria`),
  CONSTRAINT `produto_categoria_ibfk_1` FOREIGN KEY (`id_produto`) REFERENCES `produto` (`id_produto`),
  CONSTRAINT `produto_categoria_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produto_categoria`
--

LOCK TABLES `produto_categoria` WRITE;
/*!40000 ALTER TABLE `produto_categoria` DISABLE KEYS */;
INSERT INTO `produto_categoria` VALUES (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,4),(12,4),(13,4),(14,4),(15,4),(16,4),(17,4),(18,4),(19,4),(20,2),(21,2),(22,2),(23,2),(24,2),(25,2),(26,3),(27,3),(28,3),(29,3),(30,3),(31,3),(32,3),(33,3),(34,3),(35,3);
/*!40000 ALTER TABLE `produto_categoria` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
INSERT INTO `usuario` VALUES (1,'admin@papiros.com','$2y$10$rcDCqQiHz.dWW9GkC3zo5ewnRA1MFwWAe5DKSYk60nxtBy7jjrwz2');
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `vw_estoque_por_categoria`
--

DROP TABLE IF EXISTS `vw_estoque_por_categoria`;
/*!50001 DROP VIEW IF EXISTS `vw_estoque_por_categoria`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_estoque_por_categoria` AS SELECT
 1 AS `id_categoria`,
  1 AS `nome_categoria`,
  1 AS `total_produtos`,
  1 AS `estoque_total`,
  1 AS `media_estoque`,
  1 AS `valor_total_estoque`,
  1 AS `produtos_sem_estoque`,
  1 AS `produtos_estoque_critico`,
  1 AS `produtos_estoque_normal` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_produtos_categorias`
--

DROP TABLE IF EXISTS `vw_produtos_categorias`;
/*!50001 DROP VIEW IF EXISTS `vw_produtos_categorias`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_produtos_categorias` AS SELECT
 1 AS `id_produto`,
  1 AS `nome_produto`,
  1 AS `descricao`,
  1 AS `imagem`,
  1 AS `quantidade_estoque`,
  1 AS `valor_unitario`,
  1 AS `id_categoria`,
  1 AS `nome_categoria` */;
SET character_set_client = @saved_cs_client;

--
-- Temporary table structure for view `vw_produtos_estoque`
--

DROP TABLE IF EXISTS `vw_produtos_estoque`;
/*!50001 DROP VIEW IF EXISTS `vw_produtos_estoque`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vw_produtos_estoque` AS SELECT
 1 AS `id_produto`,
  1 AS `nome_produto`,
  1 AS `descricao`,
  1 AS `imagem`,
  1 AS `quantidade_estoque`,
  1 AS `valor_unitario`,
  1 AS `valor_total`,
  1 AS `status_estoque`,
  1 AS `total_categorias`,
  1 AS `categorias` */;
SET character_set_client = @saved_cs_client;

--
-- Dumping events for database 'projeto_papiros'
--

--
-- Dumping routines for database 'projeto_papiros'
--
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_classificar_estoque` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `fn_classificar_estoque`(p_quantidade INT
) RETURNS varchar(30) CHARSET utf8mb4 COLLATE utf8mb4_general_ci
    DETERMINISTIC
BEGIN
    RETURN CASE
        WHEN COALESCE(ABS(p_quantidade), 0) = 0
            THEN 'SEM ESTOQUE'
        WHEN COALESCE(ABS(p_quantidade), 0) BETWEEN 1 AND 5
            THEN 'ESTOQUE CRÍTICO'
        ELSE 'ESTOQUE NORMAL'
    END;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_contar_produtos` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_contar_produtos`(
    in p_busca varchar(100),
    in p_categoria int
)
select
    count(*) as total_registros
from produto as p
where
    (
        p_busca is null
        or p_busca = ''
        or p.nome_produto like concat('%', p_busca, '%')
    )
    and (
        p_categoria is null
        or p_categoria = 0
        or exists (
            select 1
            from produto_categoria as filtro
            where filtro.id_produto = p.id_produto
                and filtro.id_categoria = p_categoria
        )
    ) ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_dashboard_indicadores` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_dashboard_indicadores`()
select
    id_categoria,
    nome_categoria,
    total_produtos,
    estoque_total,
    media_estoque,
    valor_total_estoque,
    produtos_sem_estoque,
    produtos_estoque_critico,
    produtos_estoque_normal
from vw_estoque_por_categoria
order by nome_categoria ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_dashboard_inventario` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_dashboard_inventario`()
select
    id_produto,
    quantidade_estoque,
    valor_unitario,
    round(
        quantidade_estoque * valor_unitario,
        2
    ) as valor_total,
    fn_classificar_estoque(
        quantidade_estoque
    ) as status_estoque
from produto
order by id_produto ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'IGNORE_SPACE,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION' */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_listar_produtos` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_listar_produtos`(
    in p_busca varchar(100),
    in p_categoria int,
    in p_limite int,
    in p_offset int
)
select
    p.id_produto,
    p.nome_produto,
    p.descricao,
    p.imagem,
    p.quantidade_estoque,
    p.valor_unitario,
    round(
        p.quantidade_estoque * p.valor_unitario,
        2
    ) as valor_total,
    fn_classificar_estoque(
        p.quantidade_estoque
    ) as status_estoque,
    min(c.id_categoria) as id_categoria,
    coalesce(
        group_concat(
            distinct c.nome_categoria
            order by c.nome_categoria
            separator ', '
        ),
        'sem categoria'
    ) as nome_categoria
from produto as p
left join produto_categoria as pc
    on pc.id_produto = p.id_produto
left join categoria as c
    on c.id_categoria = pc.id_categoria
where
    (
        p_busca is null
        or p_busca = ''
        or p.nome_produto like concat('%', p_busca, '%')
    )
    and (
        p_categoria is null
        or p_categoria = 0
        or exists (
            select 1
            from produto_categoria as filtro
            where filtro.id_produto = p.id_produto
                and filtro.id_categoria = p_categoria
        )
    )
group by
    p.id_produto,
    p.nome_produto,
    p.descricao,
    p.imagem,
    p.quantidade_estoque,
    p.valor_unitario
order by p.id_produto
limit p_limite
offset p_offset ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Current Database: `projeto_papiros`
--

USE `projeto_papiros`;

--
-- Final view structure for view `vw_estoque_por_categoria`
--

/*!50001 DROP VIEW IF EXISTS `vw_estoque_por_categoria`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_estoque_por_categoria` AS select `c`.`id_categoria` AS `id_categoria`,`c`.`nome_categoria` AS `nome_categoria`,count(distinct `p`.`id_produto`) AS `total_produtos`,coalesce(sum(`p`.`quantidade_estoque`),0) AS `estoque_total`,round(coalesce(avg(`p`.`quantidade_estoque`),0),2) AS `media_estoque`,round(coalesce(sum(`p`.`quantidade_estoque` * `p`.`valor_unitario`),0),2) AS `valor_total_estoque`,count(distinct case when `p`.`quantidade_estoque` = 0 then `p`.`id_produto` end) AS `produtos_sem_estoque`,count(distinct case when `p`.`quantidade_estoque` between 1 and 5 then `p`.`id_produto` end) AS `produtos_estoque_critico`,count(distinct case when `p`.`quantidade_estoque` > 5 then `p`.`id_produto` end) AS `produtos_estoque_normal` from ((`categoria` `c` left join `produto_categoria` `pc` on(`pc`.`id_categoria` = `c`.`id_categoria`)) left join `produto` `p` on(`p`.`id_produto` = `pc`.`id_produto`)) group by `c`.`id_categoria`,`c`.`nome_categoria` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_produtos_categorias`
--

/*!50001 DROP VIEW IF EXISTS `vw_produtos_categorias`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_produtos_categorias` AS select `p`.`id_produto` AS `id_produto`,`p`.`nome_produto` AS `nome_produto`,`p`.`descricao` AS `descricao`,`p`.`imagem` AS `imagem`,`p`.`quantidade_estoque` AS `quantidade_estoque`,`p`.`valor_unitario` AS `valor_unitario`,`c`.`id_categoria` AS `id_categoria`,`c`.`nome_categoria` AS `nome_categoria` from ((`produto` `p` left join `produto_categoria` `pc` on(`pc`.`id_produto` = `p`.`id_produto`)) left join `categoria` `c` on(`c`.`id_categoria` = `pc`.`id_categoria`)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_produtos_estoque`
--

/*!50001 DROP VIEW IF EXISTS `vw_produtos_estoque`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_produtos_estoque` AS select `p`.`id_produto` AS `id_produto`,`p`.`nome_produto` AS `nome_produto`,`p`.`descricao` AS `descricao`,`p`.`imagem` AS `imagem`,`p`.`quantidade_estoque` AS `quantidade_estoque`,`p`.`valor_unitario` AS `valor_unitario`,`p`.`quantidade_estoque` * `p`.`valor_unitario` AS `valor_total`,case when `p`.`quantidade_estoque` = 0 then 'sem estoque' when `p`.`quantidade_estoque` between 1 and 5 then 'estoque crítico' else 'estoque normal' end AS `status_estoque`,count(distinct `c`.`id_categoria`) AS `total_categorias`,coalesce(group_concat(distinct `c`.`nome_categoria` order by `c`.`nome_categoria` ASC separator ', '),'sem categoria') AS `categorias` from ((`produto` `p` left join `produto_categoria` `pc` on(`pc`.`id_produto` = `p`.`id_produto`)) left join `categoria` `c` on(`c`.`id_categoria` = `pc`.`id_categoria`)) group by `p`.`id_produto`,`p`.`nome_produto`,`p`.`descricao`,`p`.`imagem`,`p`.`quantidade_estoque`,`p`.`valor_unitario` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-09 21:47:23
