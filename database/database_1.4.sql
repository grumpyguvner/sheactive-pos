--
-- Database: `pos`
--

UPDATE `pos_app_config` SET `value` = '1.4' WHERE `key` = 'version';
UPDATE `pos_headoffice` SET `value` = '1.4' WHERE `key` = 'ho_version';

--
-- Table structure for TABLE `pos_items`
--
ALTER TABLE `pos_items`
 ADD COLUMN `supplierref` varchar(32) DEFAULT '' AFTER `supplier_id`;

ALTER TABLE `pos_items`
 ADD KEY `pos_items_supplierref` (`supplierref`);