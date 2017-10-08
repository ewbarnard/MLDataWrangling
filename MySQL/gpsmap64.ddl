/*
 Navicat MySQL Data Transfer

 Source Server         : MAMP-Pro-localhost
 Source Server Type    : MySQL
 Source Server Version : 50542
 Source Host           : localhost
 Source Database       : gpsmap64

 Target Server Type    : MySQL
 Target Server Version : 50542
 File Encoding         : utf-8

 Date: 10/08/2017 07:30:43 AM
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
--  Table structure for `calculate`
-- ----------------------------
DROP TABLE IF EXISTS `calculate`;
CREATE TABLE `calculate` (
    `id`                INT(10) UNSIGNED       NOT NULL AUTO_INCREMENT,
    `trkpt_id`          INT(10) UNSIGNED       NOT NULL DEFAULT '0',
    `power_sequence_id` MEDIUMINT(8) UNSIGNED  NOT NULL DEFAULT '0',
    `wpt_id`            MEDIUMINT(8) UNSIGNED  NOT NULL DEFAULT '0',
    `motion_id`         TINYINT(3) UNSIGNED    NOT NULL DEFAULT '0',
    `feet`              INT(10) UNSIGNED       NOT NULL DEFAULT '0',
    `seconds`           SMALLINT(5) UNSIGNED   NOT NULL DEFAULT '0',
    `mph`               DECIMAL(6, 2) UNSIGNED NOT NULL DEFAULT '0.00',
    `climb`             DECIMAL(10, 2)         NOT NULL DEFAULT '0.00',
    `power_on_seconds`  MEDIUMINT(8) UNSIGNED  NOT NULL DEFAULT '0',
    `is_sequence_start` TINYINT(3) UNSIGNED    NOT NULL DEFAULT '0',
    `has_speed`         TINYINT(3) UNSIGNED    NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `trkpt_id` (`trkpt_id`),
    KEY `has_speed` (`has_speed`, `power_sequence_id`),
    KEY `power_sequence_id` (`power_sequence_id`, `trkpt_id`)
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 63746
    DEFAULT CHARSET = latin1;

-- ----------------------------
--  Table structure for `directory`
-- ----------------------------
DROP TABLE IF EXISTS `directory`;
CREATE TABLE `directory` (
    `id`   TINYINT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255)        NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 6
    DEFAULT CHARSET = latin1;

-- ----------------------------
--  Table structure for `distance`
-- ----------------------------
DROP TABLE IF EXISTS `distance`;
CREATE TABLE `distance` (
    `id`       INT(10) UNSIGNED      NOT NULL AUTO_INCREMENT,
    `wpt_id`   MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    `trkpt_id` INT(10) UNSIGNED      NOT NULL DEFAULT '0',
    `feet`     INT(10) UNSIGNED      NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `wpt_id` (`trkpt_id`, `wpt_id`) USING BTREE
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 2996016
    DEFAULT CHARSET = latin1;

-- ----------------------------
--  Table structure for `file`
-- ----------------------------
DROP TABLE IF EXISTS `file`;
CREATE TABLE `file` (
    `id`           SMALLINT(5) UNSIGNED NOT NULL AUTO_INCREMENT,
    `directory_id` TINYINT(3) UNSIGNED  NOT NULL DEFAULT '0',
    `name`         VARCHAR(255)         NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    UNIQUE KEY `directory_id` (`directory_id`, `name`)
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 27
    DEFAULT CHARSET = latin1;

-- ----------------------------
--  Table structure for `motion`
-- ----------------------------
DROP TABLE IF EXISTS `motion`;
CREATE TABLE `motion` (
    `id`   TINYINT(3) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255)                 DEFAULT '',
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 4
    DEFAULT CHARSET = latin1;

-- ----------------------------
--  Table structure for `power_sequence`
-- ----------------------------
DROP TABLE IF EXISTS `power_sequence`;
CREATE TABLE `power_sequence` (
    `id`         MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    `time_begin` DATETIME              NOT NULL,
    `time_end`   DATETIME              NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `time_begin` (`time_begin`)
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 245
    DEFAULT CHARSET = latin1;

-- ----------------------------
--  Table structure for `predict`
-- ----------------------------
DROP TABLE IF EXISTS `predict`;
CREATE TABLE `predict` (
    `id`          SMALLINT(5) UNSIGNED   NOT NULL AUTO_INCREMENT,
    `file`        VARCHAR(255)           NOT NULL DEFAULT '',
    `all`         DECIMAL(6, 2) UNSIGNED NOT NULL DEFAULT '0.00',
    `drive`       DECIMAL(6, 2) UNSIGNED NOT NULL DEFAULT '0.00',
    `walk`        DECIMAL(6, 2) UNSIGNED NOT NULL DEFAULT '0.00',
    `hike`        DECIMAL(6, 2) UNSIGNED NOT NULL DEFAULT '0.00',
    `total_all`   MEDIUMINT(8) UNSIGNED  NOT NULL DEFAULT '0',
    `total_drive` MEDIUMINT(8) UNSIGNED  NOT NULL DEFAULT '0',
    `total_walk`  MEDIUMINT(8) UNSIGNED  NOT NULL DEFAULT '0',
    `total_hike`  MEDIUMINT(8) UNSIGNED  NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`),
    UNIQUE KEY `file` (`file`)
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 41
    DEFAULT CHARSET = latin1;

-- ----------------------------
--  Table structure for `split`
-- ----------------------------
DROP TABLE IF EXISTS `split`;
CREATE TABLE `split` (
    `id`          MEDIUMINT(8) UNSIGNED                    NOT NULL AUTO_INCREMENT,
    `run_step`    TINYINT(3) UNSIGNED                      NOT NULL DEFAULT '0'
    COMMENT 'For splitting differently for different models',
    `type`        ENUM ('train', 'test', 'oops', 'public') NOT NULL DEFAULT 'oops',
    `slug`        VARCHAR(32)                              NOT NULL DEFAULT ''
    COMMENT 'Export name without extension',
    `begin`       DATETIME                                 NOT NULL,
    `end`         DATETIME                                 NOT NULL,
    `description` VARCHAR(255)                             NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    UNIQUE KEY `slug` (`slug`),
    UNIQUE KEY `begin` (`begin`, `run_step`) USING BTREE
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 17
    DEFAULT CHARSET = latin1;

-- ----------------------------
--  Table structure for `trk`
-- ----------------------------
DROP TABLE IF EXISTS `trk`;
CREATE TABLE `trk` (
    `id`         MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    `file_id`    SMALLINT(5) UNSIGNED  NOT NULL DEFAULT '0',
    `trk_csv_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0'
    COMMENT 'ID in csv file',
    `name`       VARCHAR(255)          NOT NULL DEFAULT '',
    `cmt`        VARCHAR(255)          NOT NULL DEFAULT '',
    `desc`       VARCHAR(255)          NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    UNIQUE KEY `trk_id` (`file_id`, `trk_csv_id`) USING BTREE
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 185
    DEFAULT CHARSET = latin1;

-- ----------------------------
--  Table structure for `trkpt`
-- ----------------------------
DROP TABLE IF EXISTS `trkpt`;
CREATE TABLE `trkpt` (
    `id`            INT(10) UNSIGNED      NOT NULL AUTO_INCREMENT,
    `trkseg_id`     MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    `trkpt_csv_id`  INT(10) UNSIGNED      NOT NULL DEFAULT '0'
    COMMENT 'ID in csv file',
    `trkseg_csv_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0'
    COMMENT 'trkseg.trkseg_csv_id',
    `lat`           DECIMAL(10, 6)        NOT NULL DEFAULT '0.000000',
    `lon`           DECIMAL(10, 6)        NOT NULL DEFAULT '0.000000',
    `ele`           DECIMAL(8, 2)         NOT NULL DEFAULT '0.00',
    `time`          DATETIME              NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `time` (`time`),
    KEY `trkseg_id` (`trkseg_id`)
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 63746
    DEFAULT CHARSET = latin1;

-- ----------------------------
--  Table structure for `trkseg`
-- ----------------------------
DROP TABLE IF EXISTS `trkseg`;
CREATE TABLE `trkseg` (
    `id`            MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    `trk_id`        MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0',
    `trkseg_csv_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0'
    COMMENT 'ID in csv file',
    `trk_csv_id`    MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0'
    COMMENT 'trk.ID and trkID in csv file, trk.trk_csv_id here',
    PRIMARY KEY (`id`),
    UNIQUE KEY `trk_id` (`trk_id`, `trkseg_csv_id`) USING BTREE
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 185
    DEFAULT CHARSET = latin1;

-- ----------------------------
--  Table structure for `wpt`
-- ----------------------------
DROP TABLE IF EXISTS `wpt`;
CREATE TABLE `wpt` (
    `id`         MEDIUMINT(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    `wpt_csv_id` MEDIUMINT(8) UNSIGNED NOT NULL DEFAULT '0'
    COMMENT 'ID in csv file',
    `lat`        DECIMAL(10, 6)        NOT NULL DEFAULT '0.000000',
    `lon`        DECIMAL(10, 6)        NOT NULL DEFAULT '0.000000',
    `ele`        DECIMAL(8, 2)         NOT NULL DEFAULT '0.00',
    `time`       DATETIME              NOT NULL,
    `name`       VARCHAR(255)          NOT NULL DEFAULT '',
    `cmt`        VARCHAR(255)          NOT NULL DEFAULT '',
    `desc`       VARCHAR(255)          NOT NULL DEFAULT '',
    PRIMARY KEY (`id`),
    UNIQUE KEY `time` (`lat`, `lon`, `ele`) USING BTREE
)
    ENGINE = InnoDB
    AUTO_INCREMENT = 48
    DEFAULT CHARSET = latin1;

SET FOREIGN_KEY_CHECKS = 1;
