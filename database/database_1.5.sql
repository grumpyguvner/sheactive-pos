--
-- Database: `pos`
--

UPDATE `pos_app_config` SET `value` = '1.5' WHERE `key` = 'version';
UPDATE `pos_headoffice` SET `value` = '1.5' WHERE `key` = 'ho_version';

--
-- Dumping data for TABLE `pos_modules`
--
INSERT INTO `pos_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES
('module_locations', 'module_locations_desc', 12, 'locations'),
('module_stocktakes', 'module_stocktakes_desc', 13, 'stocktakes');

INSERT INTO `pos_permissions` (`module_id`, `person_id`) VALUES
('locations', 1),
('stocktakes', 1);

--
-- Table structure for TABLE `pos_locations`
--

CREATE TABLE `pos_locations` (
  `location_id` int(10) NOT NULL AUTO_INCREMENT,
  `location_ref` varchar(32) NOT NULL,
  `location_comment` varchar(255) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY `location_id` (`location_id`),
  UNIQUE KEY `location_ref` (`location_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Table structure for TABLE `pos_items`
--
ALTER TABLE `pos_items`
 ADD COLUMN `location_id` int(10) DEFAULT NULL AFTER `quantity`;

ALTER TABLE `pos_items`
 ADD KEY `pos_items_location_id` (`location_id`);


--
-- Table structure for TABLE `pos_stocktakes`
--

CREATE TABLE `pos_stocktakes` (
  `stocktake_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `location_id` int(10) NOT NULL DEFAULT '0',
  `employee_id` int(10) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `stocktake_id` int(10) NOT NULL AUTO_INCREMENT,
  `ho_update` timestamp DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`stocktake_id`),
  KEY `location_id` (`location_id`),
  KEY `employee_id` (`employee_id`),
  KEY `pos_stocktakes_ho_update` (`ho_update`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Table structure for TABLE `pos_stocktakes_items`
--

CREATE TABLE `pos_stocktakes_items` (
  `stocktake_id` int(10) NOT NULL DEFAULT '0',
  `item_id` int(10) NOT NULL DEFAULT '0',
  `description` varchar(30) DEFAULT NULL,
  `serialnumber` varchar(30) DEFAULT NULL,
  `line` int(3) NOT NULL,
  `quantity_counted` int(10) NOT NULL DEFAULT '0',
  `ho_update` timestamp DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`stocktake_id`,`item_id`,`line`),
  KEY `item_id` (`item_id`),
  KEY `pos_stocktake_items_ho_update` (`ho_update`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
