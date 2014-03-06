-- 
-- Database: `pos`
-- 

UPDATE `pos_app_config` SET `value` = '1.10' WHERE `key` = 'version';
UPDATE `pos_headoffice` SET `value` = '1.10' WHERE `key` = 'ho_version';

--
-- Table structure for TABLE `pos_hhreceives`
--
CREATE TABLE `pos_hhreceives` (
  `hhreceives_id` int(10) NOT NULL AUTO_INCREMENT,
  `device` varchar(30) NOT NULL DEFAULT '',
  `branch_ref` varchar(255) NOT NULL DEFAULT '',
  `from_branch` varchar(255) NOT NULL DEFAULT '',
  `location_id` int(10) NOT NULL DEFAULT '0',
  `item_number` varchar(255) NOT NULL DEFAULT '',
  `timestamp` timestamp DEFAULT '0000-00-00 00:00:00',
  `processed` int(1) NOT NULL DEFAULT '0',
  `comment` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`hhreceives_id`),
  UNIQUE KEY (`device`,`item_number`,`timestamp`),
  KEY `processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for TABLE `pos_hhstock`
--
CREATE TABLE `pos_hhstock` (
  `hhstock_id` int(10) NOT NULL AUTO_INCREMENT,
  `device` varchar(30) NOT NULL DEFAULT '',
  `branch_ref` varchar(255) NOT NULL DEFAULT '',
  `location_id` int(10) NOT NULL DEFAULT '0',
  `item_number` varchar(255) NOT NULL DEFAULT '',
  `timestamp` timestamp DEFAULT '0000-00-00 00:00:00',
  `processed` int(1) NOT NULL DEFAULT '0',
  `comment` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`hhstock_id`),
  UNIQUE KEY (`device`,`item_number`,`timestamp`),
  KEY `processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;