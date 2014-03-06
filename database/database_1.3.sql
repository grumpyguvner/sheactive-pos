-- 
-- Database: `pos`
-- 

UPDATE `pos_app_config` SET `value` = '1.3' WHERE `key` = 'version';
UPDATE `pos_headoffice` SET `value` = '1.3' WHERE `key` = 'ho_version';

INSERT INTO `pos_app_config` (`key`, `value`) VALUES
('print_after_transfer', '0');

--
-- Table structure for TABLE `pos_items`
-- 
ALTER TABLE `pos_items`
 ADD COLUMN `reorder_quantity` double(15,2) DEFAULT '0.00' AFTER `reorder_level`,
 ADD COLUMN `location_id` int(11) DEFAULT NULL AFTER `unit_price`;

ALTER TABLE `pos_items`
 ADD KEY `pos_items_location_id` (`location_id`);