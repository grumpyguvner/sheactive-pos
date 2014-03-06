-- 
-- Database: `pos`
-- 

UPDATE `pos_app_config` SET `value` = '2.0' WHERE `key` = 'version';
UPDATE `pos_headoffice` SET `value` = '2.0' WHERE `key` = 'ho_version';

--
-- Table structure for TABLE `pos_items`
-- 
ALTER TABLE `pos_items`
 ADD COLUMN `gender` varchar(255) DEFAULT NULL AFTER `name`,
 ADD COLUMN `sport` varchar(255) DEFAULT NULL AFTER `category`,
 ADD COLUMN `producttype` varchar(255) DEFAULT NULL AFTER `sport`;