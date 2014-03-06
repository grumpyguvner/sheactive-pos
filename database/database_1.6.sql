--
-- Database: `pos`
--

UPDATE `pos_app_config` SET `value` = '1.6' WHERE `key` = 'version';
UPDATE `pos_headoffice` SET `value` = '1.6' WHERE `key` = 'ho_version';

--
-- Table structure for TABLE `pos_items`
--
ALTER TABLE `pos_items`
 ADD COLUMN `retail_price` double(15,2) DEFAULT NULL AFTER `unit_price`;
