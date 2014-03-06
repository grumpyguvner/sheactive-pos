-- 
-- Database: `pos`
-- 

UPDATE `pos_app_config` SET `value` = '1.2' WHERE `key` = 'version';
UPDATE `pos_headoffice` SET `value` = '1.2' WHERE `key` = 'ho_version';

INSERT INTO `pos_app_config` (`key`, `value`) VALUES
('CAPTURE_EAN_SALES', 'NO'),
('CAPTURE_EAN_RETURNS', 'NO');

--
-- Table structure for TABLE `pos_items`
-- 
ALTER TABLE `pos_items`
 ADD COLUMN `ean_upc` varchar(255) DEFAULT NULL AFTER `item_number`;

ALTER TABLE `pos_items`
 ADD KEY `ean_upc` (`ean_upc`);