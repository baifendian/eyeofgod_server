/*
SQLyog Ultimate v11.42 (64 bit)
MySQL - 5.5.37-log : Database - eye.of.god
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`eye.of.god` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `eye.of.god`;

/*Table structure for table `dict` */

DROP TABLE IF EXISTS `dict`;

CREATE TABLE `dict` (
  `textid` int(10) NOT NULL AUTO_INCREMENT COMMENT '字典类型ID',
  `text` varchar(20) DEFAULT NULL COMMENT '类型名称',
  PRIMARY KEY (`textid`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

/*Data for the table `dict` */

insert  into `dict`(`textid`,`text`) values (1,'男坐便'),(2,'男蹲便'),(3,'男淋浴'),(4,'女坐便'),(5,'女蹲便'),(6,'女淋浴'),(7,'蛋椅'),(8,'男休息室'),(9,'女休息室');

/*Table structure for table `event` */

DROP TABLE IF EXISTS `event`;

CREATE TABLE `event` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '事件ID',
  `mark` varchar(20) DEFAULT NULL COMMENT '设备编号',
  `state` int(10) DEFAULT NULL COMMENT '事件类型',
  `timestamp` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `event` */

insert  into `event`(`id`,`mark`,`state`,`timestamp`) values (1,'S-PI-1',1,1467966453),(2,'S-PI-1',0,1467966459),(3,'S-PI-1',1,1467966489);

/*Table structure for table `remind` */

DROP TABLE IF EXISTS `remind`;

CREATE TABLE `remind` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '预定ID',
  `userid` int(10) DEFAULT NULL COMMENT '用户ID',
  `sourceid` int(10) DEFAULT NULL COMMENT '资源ID',
  `state` tinyint(1) DEFAULT '0' COMMENT '处理状态 0 待通知 1 已通知',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `remind` */

/*Table structure for table `source` */

DROP TABLE IF EXISTS `source`;

CREATE TABLE `source` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '资源ID',
  `textid` int(10) DEFAULT NULL COMMENT '资源类型',
  `location` char(1) DEFAULT NULL COMMENT '所属区域',
  `mark` varchar(20) DEFAULT NULL COMMENT '设备编号',
  `state` int(10) DEFAULT '0' COMMENT '资源状态（0 空闲 1 占用）',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

/*Data for the table `source` */

insert  into `source`(`id`,`textid`,`location`,`mark`,`state`) values (6,9,'C','女休息室',0),(7,9,'C','女休息室',0),(8,9,'C','女休息室',0),(9,9,'C','女休息室',0),(10,9,'C','女休息室',0),(11,9,'C','女休息室',0),(12,8,'C','男休息室',0),(13,8,'C','男休息室',0),(14,8,'C','男休息室',1),(15,8,'C','男休息室',1),(16,8,'C','男休息室',1),(17,8,'C','男休息室',1),(18,8,'C','男休息室',1),(19,8,'C','男休息室',1),(20,8,'C','男休息室',1),(21,8,'C','男休息室',1),(22,8,'C','男休息室',1),(23,8,'C','男休息室',1),(24,8,'C','男休息室',1),(25,8,'C','男休息室',1),(26,7,'D','蛋椅',1),(27,7,'D','蛋椅',1),(28,7,'D','蛋椅',1),(29,7,'D','蛋椅',0),(30,7,'D','蛋椅',0),(31,7,'D','蛋椅',0),(32,7,'D','蛋椅',0),(33,7,'D','蛋椅',0),(34,6,'B','女淋浴',0),(35,3,'B','男淋浴',0),(36,5,'A','女蹲便',0),(37,5,'A','女蹲便',0),(38,5,'A','女蹲便',0),(39,5,'B','女蹲便',0),(40,5,'B','女蹲便',0),(41,4,'A','女坐便',0),(42,4,'A','女坐便',0),(43,4,'A','女坐便',0),(44,4,'B','女坐便',0),(45,4,'B','女坐便',0),(46,2,'A','男坐便',0),(47,2,'A','男坐便',0),(48,2,'A','男蹲便',0),(49,2,'B','男坐便',0),(50,2,'B','男坐便',0),(51,2,'B','男蹲便',0),(52,2,'B','男蹲便',0);

/*Table structure for table `user` */

DROP TABLE IF EXISTS `user`;

CREATE TABLE `user` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `sex` int(10) NOT NULL DEFAULT '1' COMMENT '性别',
  `location` char(1) DEFAULT NULL COMMENT '位置信息',
  `advanced` int(10) DEFAULT NULL COMMENT '高级设置',
  `mac` varchar(64) DEFAULT NULL COMMENT 'mac地址',
  `createtime` int(10) DEFAULT '0' COMMENT '创建时间',
  `modifytime` int(10) DEFAULT NULL COMMENT '更新时间',
  `token` varchar(64) DEFAULT NULL COMMENT 'TOKEN',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `user` */

/* Trigger structure for table `event` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `event_trigger` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'%' */ /*!50003 TRIGGER `event_trigger` AFTER INSERT ON `event` FOR EACH ROW BEGIN
	if new.state=1 then
	UPDATE `source` SET `state` = 1 where mark = new.mark;
	end if;
	IF new.state=0 THEN
	UPDATE `source` SET `state` = 0 WHERE mark = new.mark;
	END IF;
    END */$$


DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
