-- 
-- Database: `pos`
-- 

UPDATE `pos_app_config` SET `value` = '1.9' WHERE `key` = 'version';
UPDATE `pos_headoffice` SET `value` = '1.9' WHERE `key` = 'ho_version';

--
-- Table structure for TABLE `pos_items`
-- 
ALTER TABLE `pos_receivings_items`
 ADD COLUMN `location_id` int(11) DEFAULT NULL AFTER `item_unit_price`;