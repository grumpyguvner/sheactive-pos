--
-- Database: `pos`
--

UPDATE `pos_app_config` SET `value` = '1.7' WHERE `key` = 'version';
UPDATE `pos_headoffice` SET `value` = '1.7' WHERE `key` = 'ho_version';

UPDATE `pos_app_config` SET `value` = 'RECEIPT' WHERE `key` = 'print_after_sale';

INSERT INTO `pos_app_config` (`value`,`key`) VALUES ('receipt_prefix','UB');
INSERT INTO `pos_app_config` (`value`,`key`) VALUES ('sales_mode','TILL');

--
-- Table structure for TABLE `pos_suppliers`
--
ALTER TABLE `pos_suppliers`
 ADD COLUMN `discount_percent` double(15,2) NOT NULL DEFAULT 0.00 AFTER `deleted`;