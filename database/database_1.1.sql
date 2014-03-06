-- 
-- Database: `pos`
-- 

UPDATE `pos_app_config` SET `value` = '1.1' WHERE `key` = 'version';
UPDATE `pos_headoffice` SET `value` = '1.1' WHERE `key` = 'ho_version';

INSERT INTO `pos_headoffice` (`key`, `value`) VALUES
('ACTIVEWMS_ADDR', 'warehouse.sheactive.co.uk'),
('ACTIVEWMS_USER', 'brighton'),
('ACTIVEWMS_PASS', 'St4rl!ght'),
('ACTIVEWMS_LASTUPDATE', '0000-00-00 00:00:00');

-- 
-- Table structure for TABLE `pos_inventory`
-- 
ALTER TABLE `pos_inventory`
 ADD COLUMN `ho_update` timestamp DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `pos_inventory`
 ADD KEY `pos_inventory_ho_update` (`ho_update`);

-- 
-- Table structure for TABLE `pos_items`
-- 
ALTER TABLE `pos_items`
 ADD COLUMN `ho_update` timestamp DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `pos_items`
 ADD KEY `pos_items_ho_update` (`ho_update`);

-- 
-- Table structure for TABLE `pos_people`
-- 
ALTER TABLE `pos_people`
 ADD COLUMN `ho_update` timestamp DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `pos_people`
 ADD KEY `pos_people_ho_update` (`ho_update`);

-- 
-- Table structure for TABLE `pos_receivings`
-- 
ALTER TABLE `pos_receivings`
 ADD COLUMN `ho_update` timestamp DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `pos_receivings`
 ADD KEY `pos_receivings_ho_update` (`ho_update`);

-- 
-- Table structure for TABLE `pos_receivings_items`
-- 
ALTER TABLE `pos_receivings_items`
 ADD COLUMN `ho_update` timestamp DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `pos_receivings_items`
 ADD KEY `pos_receivings_items_ho_update` (`ho_update`);

-- 
-- Table structure for TABLE `pos_sales`
-- 
ALTER TABLE `pos_sales`
 ADD COLUMN `ho_update` timestamp DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `pos_sales`
 ADD KEY `pos_sales_ho_update` (`ho_update`);

-- 
-- Table structure for TABLE `pos_sales_items`
-- 
ALTER TABLE `pos_sales_items`
 ADD COLUMN `ho_update` timestamp DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `pos_sales_items`
 ADD KEY `pos_sales_items_ho_update` (`ho_update`);

-- 
-- Table structure for TABLE `pos_suppliers`
-- 
ALTER TABLE `pos_suppliers`
 ADD COLUMN `ho_update` timestamp DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `pos_suppliers`
 ADD KEY `pos_suppliers_ho_update` (`ho_update`);

--
-- Table structure for TABLE `pos_transfers`
--
ALTER TABLE `pos_transfers`
 ADD COLUMN `ho_update` timestamp DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `pos_transfers`
 ADD KEY `pos_transfers_ho_update` (`ho_update`);

--
-- Table structure for TABLE `pos_transfers_items`
--
ALTER TABLE `pos_transfers_items`
 ADD COLUMN `ho_update` timestamp DEFAULT '0000-00-00 00:00:00';

ALTER TABLE `pos_transfers_items`
 ADD KEY `pos_transfers_items_ho_update` (`ho_update`);