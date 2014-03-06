--
-- Database: `pos`
--

UPDATE `pos_app_config` SET `value` = '1.8' WHERE `key` = 'version';
UPDATE `pos_headoffice` SET `value` = '1.8' WHERE `key` = 'ho_version';

-- 
-- `pos_modules`
-- 

INSERT INTO `pos_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES
('module_customerreturns', 'module_customerreturns_desc', 1, 'customerreturns');

-- 
-- `pos_permissions`
-- 

INSERT INTO `pos_permissions` (`module_id`, `person_id`) VALUES 
('customerreturns', 1);

-- 
-- Table structure for TABLE `pos_returns`
-- 

CREATE TABLE `pos_customerreturns` (
  `customerreturn_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `orderref` varchar(64) NOT NULL DEFAULT '',
  `customer_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `customerreturn_id` int(10) NOT NULL AUTO_INCREMENT,
  `ho_update` timestamp DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`customerreturn_id`),
  KEY `customer_id` (`customer_id`),
  KEY `employee_id` (`employee_id`),
  KEY `pos_customerreturns_ho_update` (`ho_update`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Table structure for TABLE `pos_returns_items`
-- 

CREATE TABLE `pos_customerreturns_items` (
  `customerreturn_id` int(10) NOT NULL DEFAULT '0',
  `item_id` int(10) NOT NULL DEFAULT '0',
  `description` varchar(30) DEFAULT NULL,
  `serialnumber` varchar(30) DEFAULT NULL,
  `line` int(3) NOT NULL DEFAULT '0',
  `quantity_returned` double(15,2) NOT NULL DEFAULT '0.00',
  `item_cost_price` decimal(15,2) NOT NULL,
  `item_unit_price` double(15,2) NOT NULL,
  `discount` double(15,2) NOT NULL DEFAULT '0',
  `discount_type` char(1) NOT NULL DEFAULT '%',
  `discount_reason` varchar(30) NOT NULL DEFAULT '',
  `reason_code` int(1) NOT NULL DEFAULT '0',
  `faulty` int(1) NOT NULL DEFAULT '0',
  `restock` int(1) NOT NULL DEFAULT '1',
  `comment` varchar(30) NOT NULL DEFAULT '',
  `ho_update` timestamp DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`customerreturn_id`,`item_id`,`line`),
  KEY `item_id` (`item_id`),
  KEY `pos_customerreturns_items_ho_update` (`ho_update`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
