-- phpMyAdmin SQL Dump
-- version 2.8.2
-- http://www.phpmyadmin.net
-- 
-- Host: localhost:8889
-- Generation Time: Oct 07, 2010 at 12:41 PM
-- Server version: 5.1.50
-- PHP Version: 5.3.1
-- 
-- Database: `pos`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `pos_app_config`
-- 

CREATE TABLE `pos_app_config` (
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for TABLE `pos_app_config`
-- 

INSERT INTO `pos_app_config` (`key`, `value`) VALUES ('address1', '5 North Street'),
('address2', 'Brighton'),
('company', 'SHEACTIVE'),
('default_tax_1_name', 'VAT'),
('default_tax_1_rate', '20.0'),
('default_tax_2_name', ''),
('default_tax_2_rate', ''),
('default_tax_rate', '20.0'),
('email', 'brighton@sheactive.co.uk'),
('fax', ''),
('phone', '+44 1273 739725'),
('vat_number', 'VAT: GB 814 6894 02'),
('return_policy1', 'Thank you for shopping with sheactive'),
('return_policy2', 'We are happy to exchange or refund any'),
('return_policy3', 'unused items returned to us'),
('return_policy4', 'with this receipt within 14 days'),
('version', '1.3'),
('print_after_sale', '0'),
('print_after_transfer', '0'),
('website', 'www.sheactive.co.uk');

-- --------------------------------------------------------

--
-- Table structure for TABLE `pos_headoffice`
--

CREATE TABLE `pos_headoffice` (
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for TABLE `pos_headoffice`
--

INSERT INTO `pos_headoffice` (`key`, `value`) VALUES
('ho_address', 'Unit 3-4, Eagle Trading Estate'),
('ho_company', 'Sheactive Limited'),
('ho_default_tax_rate', '20.0'),
('ho_email', 'info@sheactive.co.uk'),
('ho_fax', ''),
('ho_phone', '+44 1403 786434'),
('ho_branch', 'BRIGHTON'),
('ho_version', '1.3'),
('ho_website', 'http://www.sheactive.co.uk'),
('ACTIVEWMS_ADDR', 'warehouse.sheactive.co.uk'),
('ACTIVEWMS_USER', 'brighton'),
('ACTIVEWMS_PASS', 'St4rl!ght'),
('ACTIVEWMS_LASTUPDATE', '0000-00-00 00:00:00');

-- --------------------------------------------------------

-- --------------------------------------------------------

--
-- Table structure for TABLE `pos_branches`
--

CREATE TABLE `pos_branches` (
  `branch_name` varchar(255) NOT NULL,
  `branch_ref` varchar(255) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `branch_ref` (`branch_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for TABLE `pos_branches`
--

INSERT INTO `pos_branches` (`branch_ref`, `branch_name`) VALUES
('WEBSTORE', 'Billingshurst'),
('BRIGHTON', 'Brighton'),
('CGARDEN', 'Exhibitions'),
('WHSE', 'Phantom Stock');

-- 
-- Table structure for TABLE `pos_customers`
-- 

CREATE TABLE `pos_customers` (
  `person_id` int(10) NOT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `taxable` int(1) NOT NULL DEFAULT '1',
  `deleted` int(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `account_number` (`account_number`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for TABLE `pos_customers`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for TABLE `pos_employees`
-- 

CREATE TABLE `pos_employees` (
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alt_login` varchar(255) NOT NULL,
  `login_page` varchar(255) NOT NULL DEFAULT 'home',
  `person_id` int(10) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  UNIQUE KEY `username` (`username`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for TABLE `pos_employees`
-- 

INSERT INTO `pos_employees` (`username`, `password`, `alt_login`, `login_page`, `person_id`, `deleted`) VALUES
('itadmin', 'd6d642e77ea5411d8c4111f1da0972bc', '', '', 1, 0),
('claire.keen', '0192023a7bbd73250516f069df18b500', 'c4ca4238a0b923820dcc509a6f75849b', 'sales', 2, 0),
('gavin.humphreys', '0192023a7bbd73250516f069df18b500', '6ea9ab1baa0efb9e19094440c317e21b', 'sales', 3, 0),
('beth.reilly', '0192023a7bbd73250516f069df18b500', 'c81e728d9d4c2f636f067f89cc14862c', 'sales', 4, 0),
('robyn.faulkner', '0192023a7bbd73250516f069df18b500', 'eccbc87e4b5ce2fe28308fd9f2a7baf3', 'sales', 5, 0),
('kate.boys', '0192023a7bbd73250516f069df18b500', 'e4da3b7fbbce2345d7772b0674a318d5', 'sales', 6, 0);

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `pos_inventory`
-- 

CREATE TABLE `pos_inventory` (
  `trans_id` int(11) NOT NULL AUTO_INCREMENT,
  `trans_items` int(11) NOT NULL DEFAULT '0',
  `trans_user` int(11) NOT NULL DEFAULT '0',
  `trans_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `trans_comment` text NOT NULL,
  `trans_inventory` int(11) NOT NULL DEFAULT '0',
  `ho_update` timestamp DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`trans_id`),
  KEY `pos_inventory_ho_update` (`ho_update`)
  KEY `pos_inventory_ibfk_1` (`trans_items`),
  KEY `pos_inventory_ibfk_2` (`trans_user`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for TABLE `pos_inventory`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for TABLE `pos_items`
-- 

CREATE TABLE `pos_items` (
  `name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `item_number` varchar(255) DEFAULT NULL,
  `ean_upc` varchar(255) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `cost_price` double(15,2) NOT NULL,
  `unit_price` double(15,2) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `quantity` double(15,2) NOT NULL DEFAULT '0.00',
  `reorder_level` double(15,2) NOT NULL DEFAULT '0.00',
  `reorder_quantity` double(15,2) DEFAULT '0.00',
  `item_id` int(10) NOT NULL AUTO_INCREMENT,
  `allow_alt_description` tinyint(1) NOT NULL,
  `is_serialized` tinyint(1) NOT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `ho_update` timestamp DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `item_number` (`item_number`),
  KEY `ean_upc` (`ean_upc`),
  KEY `pos_items_location_id` (`location_id`),
  KEY `pos_items_ho_update` (`ho_update`),
  KEY `pos_items_ibfk_1` (`supplier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for TABLE `pos_items`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `pos_items_taxes`
-- 

CREATE TABLE `pos_items_taxes` (
  `item_id` int(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `percent` double(15,2) NOT NULL,
  PRIMARY KEY (`item_id`,`name`,`percent`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for TABLE `pos_items_taxes`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for TABLE `pos_modules`
-- 

CREATE TABLE `pos_modules` (
  `name_lang_key` varchar(255) NOT NULL,
  `desc_lang_key` varchar(255) NOT NULL,
  `sort` int(10) NOT NULL,
  `module_id` varchar(255) NOT NULL,
  PRIMARY KEY (`module_id`),
  UNIQUE KEY `desc_lang_key` (`desc_lang_key`),
  UNIQUE KEY `name_lang_key` (`name_lang_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for TABLE `pos_modules`
-- 

INSERT INTO `pos_modules` (`name_lang_key`, `desc_lang_key`, `sort`, `module_id`) VALUES
('module_sales', 'module_sales_desc', 1, 'sales'),
('module_customers', 'module_customers_desc', 2, 'customers'),
('module_reports', 'module_reports_desc', 3, 'reports'),
('module_items', 'module_items_desc', 4, 'items'),
('module_suppliers', 'module_suppliers_desc', 5, 'suppliers'),
('module_receivings', 'module_receivings_desc', 6, 'receivings'),
('module_branches', 'module_branches_desc', 7, 'branches'),
('module_transfers', 'module_transfers_desc', 8, 'transfers'),
('module_employees', 'module_employees_desc', 9, 'employees'),
('module_config', 'module_config_desc', 10, 'config'),
('module_headoffice', 'module_headoffice_desc', 11, 'headoffice');

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `pos_people`
-- 

CREATE TABLE `pos_people` (
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `phone_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `address_1` varchar(255) NOT NULL,
  `address_2` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `zip` varchar(255) NOT NULL,
  `country` varchar(255) NOT NULL,
  `comments` text NOT NULL,
  `person_id` int(10) NOT NULL AUTO_INCREMENT,
  `ho_update` timestamp DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`person_id`),
  KEY `pos_people_ho_update` (`ho_update`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- 
-- Dumping data for TABLE `pos_people`
-- 

INSERT INTO `pos_people` (`first_name`, `last_name`, `phone_number`, `email`, `address_1`, `address_2`, `city`, `state`, `zip`, `country`, `comments`, `person_id`) VALUES
('IT', 'Admin', '+44 1403 786434', 'itadmin@sheactive.co.uk', 'Unit 3-4, Eagle Trading Estate', 'Brookers Road', 'Billingshurst', 'West Sussex', 'RH14 9RZ', 'UK', '', 1),
('Claire', 'Keen', '+44 1273 739725', 'brighton@sheactive.co.uk', '', '', 'Brighton', '', '', 'UK', '', 2),
('Gavin', 'Humphreys', '+44 1273 739725', 'gavin@sheactive.co.uk', '', '', 'Brighton', '', '', 'UK', '', 3),
('Beth', 'Reilly', '+44 1273 739725', 'brighton@sheactive.co.uk', '', '', 'Brighton', '', '', 'UK', '', 4),
('Robyn', 'Faulkner', '+44 1273 739725', 'brighton@sheactive.co.uk', '', '', 'Brighton', '', '', 'UK', '', 5),
('Kate', 'Boys', '+44 1273 739725', 'brighton@sheactive.co.uk', '', '', 'Brighton', '', '', 'UK', '', 6);

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `pos_permissions`
-- 

CREATE TABLE `pos_permissions` (
  `module_id` varchar(255) NOT NULL,
  `person_id` int(10) NOT NULL,
  PRIMARY KEY (`module_id`,`person_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for TABLE `pos_permissions`
-- 

INSERT INTO `pos_permissions` (`module_id`, `person_id`) VALUES ('config', 1),
('headoffice', 1),
('branches', 1),
('customers', 1),
('employees', 1),
('items', 1),
('receivings', 1),
('transfers', 1),
('reports', 1),
('sales', 1),
('suppliers', 1);

INSERT INTO `pos_permissions` (`module_id`, `person_id`) VALUES
('customers', 2),
('sales', 2),
('items', 2),
('reports', 2);

INSERT INTO `pos_permissions` (`module_id`, `person_id`) VALUES
('customers', 3),
('sales', 3),
('items', 3),
('reports', 3);

INSERT INTO `pos_permissions` (`module_id`, `person_id`) VALUES
('customers', 4),
('sales', 4),
('items', 4),
('reports', 4);

INSERT INTO `pos_permissions` (`module_id`, `person_id`) VALUES
('customers', 5),
('sales', 5),
('items', 5);

INSERT INTO `pos_permissions` (`module_id`, `person_id`) VALUES
('customers', 6),
('sales', 6),
('items', 6);

-- --------------------------------------------------------

-- 
-- Table structure for TABLE `pos_receivings`
-- 

CREATE TABLE `pos_receivings` (
  `receiving_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `supplier_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `receiving_id` int(10) NOT NULL AUTO_INCREMENT,
  `payment_type` varchar(20) DEFAULT NULL,
  `ho_update` timestamp DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`receiving_id`),
  KEY `supplier_id` (`supplier_id`),
  KEY `employee_id` (`employee_id`),
  KEY `pos_receivings_ho_update` (`ho_update`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for TABLE `pos_receivings`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for TABLE `pos_receivings_items`
-- 

CREATE TABLE `pos_receivings_items` (
  `receiving_id` int(10) NOT NULL DEFAULT '0',
  `item_id` int(10) NOT NULL DEFAULT '0',
  `description` varchar(30) DEFAULT NULL,
  `serialnumber` varchar(30) DEFAULT NULL,
  `line` int(3) NOT NULL,
  `quantity_purchased` int(10) NOT NULL DEFAULT '0',
  `item_cost_price` decimal(15,2) NOT NULL,
  `item_unit_price` double(15,2) NOT NULL,
  `discount` double(15,2) NOT NULL DEFAULT '0',
  `discount_type` char(1) NOT NULL DEFAULT '%',
  `discount_reason` varchar(30) NOT NULL DEFAULT '',
  `ho_update` timestamp DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`receiving_id`,`item_id`,`line`),
  KEY `item_id` (`item_id`),
  KEY `pos_receivings_items_ho_update` (`ho_update`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for TABLE `pos_receivings_items`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for TABLE `pos_sales`
-- 

CREATE TABLE `pos_sales` (
  `sale_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_id` int(10) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `sale_id` int(10) NOT NULL AUTO_INCREMENT,
  `payment_type` varchar(512) DEFAULT NULL,
  `ho_update` timestamp DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`sale_id`),
  KEY `customer_id` (`customer_id`),
  KEY `employee_id` (`employee_id`),
  KEY `pos_sales_ho_update` (`ho_update`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- 
-- Dumping data for TABLE `pos_sales`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for TABLE `pos_sales_items`
-- 

CREATE TABLE `pos_sales_items` (
  `sale_id` int(10) NOT NULL DEFAULT '0',
  `item_id` int(10) NOT NULL DEFAULT '0',
  `description` varchar(30) DEFAULT NULL,
  `serialnumber` varchar(30) DEFAULT NULL,
  `line` int(3) NOT NULL DEFAULT '0',
  `quantity_purchased` double(15,2) NOT NULL DEFAULT '0.00',
  `item_cost_price` decimal(15,2) NOT NULL,
  `item_unit_price` double(15,2) NOT NULL,
  `discount` double(15,2) NOT NULL DEFAULT '0',
  `discount_type` char(1) NOT NULL DEFAULT '%',
  `discount_reason` varchar(30) NOT NULL DEFAULT '',
  `ho_update` timestamp DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`sale_id`,`item_id`,`line`),
  KEY `item_id` (`item_id`),
  KEY `pos_sales_items_ho_update` (`ho_update`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for TABLE `pos_sales_items`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for TABLE `pos_sales_items_taxes`
-- 

CREATE TABLE `pos_sales_items_taxes` (
  `sale_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `line` int(3) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `percent` double(15,2) NOT NULL,
  PRIMARY KEY (`sale_id`,`item_id`,`line`,`name`,`percent`),
  KEY `item_id` (`item_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for TABLE `pos_sales_items_taxes`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for TABLE `pos_sales_payments`
-- 

CREATE TABLE `pos_sales_payments` (
  `sale_id` int(10) NOT NULL,
  `payment_type` varchar(40) NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  PRIMARY KEY (`sale_id`,`payment_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for TABLE `pos_sales_payments`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for TABLE `pos_sessions`
-- 

CREATE TABLE `pos_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(50) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for TABLE `pos_sessions`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for TABLE `pos_suppliers`
-- 

CREATE TABLE `pos_suppliers` (
  `person_id` int(10) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `account_number` varchar(255) DEFAULT NULL,
  `deleted` int(1) NOT NULL DEFAULT '0',
  `ho_update` timestamp DEFAULT '0000-00-00 00:00:00',
  UNIQUE KEY `account_number` (`account_number`),
  KEY `person_id` (`person_id`),
  KEY `pos_suppliers_ho_update` (`ho_update`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 
-- Dumping data for TABLE `pos_suppliers`
-- 


-- --------------------------------------------------------

--
-- Table structure for TABLE `pos_transfers`
--

CREATE TABLE `pos_transfers` (
  `transfer_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `branch_ref` varchar(255) DEFAULT NULL,
  `employee_id` int(10) NOT NULL DEFAULT '0',
  `comment` text NOT NULL,
  `transfer_id` int(10) NOT NULL AUTO_INCREMENT,
  `ho_update` timestamp DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`transfer_id`),
  KEY `branch_ref` (`branch_ref`),
  KEY `employee_id` (`employee_id`),
  KEY `pos_transfers_ho_update` (`ho_update`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for TABLE `pos_transfers`
--


-- --------------------------------------------------------

--
-- Table structure for TABLE `pos_transfers_items`
--

CREATE TABLE `pos_transfers_items` (
  `transfer_id` int(10) NOT NULL DEFAULT '0',
  `item_id` int(10) NOT NULL DEFAULT '0',
  `description` varchar(30) DEFAULT NULL,
  `serialnumber` varchar(30) DEFAULT NULL,
  `line` int(3) NOT NULL,
  `quantity_transfered` int(10) NOT NULL DEFAULT '0',
  `ho_update` timestamp DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`transfer_id`,`item_id`,`line`),
  KEY `item_id` (`item_id`),
  KEY `pos_transfers_items_ho_update` (`ho_update`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for TABLE `pos_transfers_items`
--

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for TABLE `pos_customers`
-- 
ALTER TABLE `pos_customers`
  ADD CONSTRAINT `pos_customers_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `pos_people` (`person_id`);

-- 
-- Constraints for TABLE `pos_employees`
-- 
ALTER TABLE `pos_employees`
  ADD CONSTRAINT `pos_employees_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `pos_people` (`person_id`);

-- 
-- Constraints for TABLE `pos_inventory`
-- 
ALTER TABLE `pos_inventory`
  ADD CONSTRAINT `pos_inventory_ibfk_1` FOREIGN KEY (`trans_items`) REFERENCES `pos_items` (`item_id`),
  ADD CONSTRAINT `pos_inventory_ibfk_2` FOREIGN KEY (`trans_user`) REFERENCES `pos_employees` (`person_id`);

-- 
-- Constraints for TABLE `pos_items`
-- 
ALTER TABLE `pos_items`
  ADD CONSTRAINT `pos_items_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `pos_suppliers` (`person_id`);

CREATE INDEX `pos_items_plu` ON `pos_items` (`item_number`);

-- 
-- Constraints for TABLE `pos_items_taxes`
-- 
ALTER TABLE `pos_items_taxes`
  ADD CONSTRAINT `pos_items_taxes_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `pos_items` (`item_id`) ON DELETE CASCADE;

-- 
-- Constraints for TABLE `pos_permissions`
-- 
ALTER TABLE `pos_permissions`
  ADD CONSTRAINT `pos_permissions_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `pos_employees` (`person_id`),
  ADD CONSTRAINT `pos_permissions_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `pos_modules` (`module_id`);

-- 
-- Constraints for TABLE `pos_receivings`
-- 
ALTER TABLE `pos_receivings`
  ADD CONSTRAINT `pos_receivings_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `pos_employees` (`person_id`),
  ADD CONSTRAINT `pos_receivings_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `pos_suppliers` (`person_id`);

-- 
-- Constraints for TABLE `pos_receivings_items`
-- 
ALTER TABLE `pos_receivings_items`
  ADD CONSTRAINT `pos_receivings_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `pos_items` (`item_id`),
  ADD CONSTRAINT `pos_receivings_items_ibfk_2` FOREIGN KEY (`receiving_id`) REFERENCES `pos_receivings` (`receiving_id`);

-- 
-- Constraints for TABLE `pos_sales`
-- 
ALTER TABLE `pos_sales`
  ADD CONSTRAINT `pos_sales_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `pos_employees` (`person_id`),
  ADD CONSTRAINT `pos_sales_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `pos_customers` (`person_id`);

-- 
-- Constraints for TABLE `pos_sales_items`
-- 
ALTER TABLE `pos_sales_items`
  ADD CONSTRAINT `pos_sales_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `pos_items` (`item_id`),
  ADD CONSTRAINT `pos_sales_items_ibfk_2` FOREIGN KEY (`sale_id`) REFERENCES `pos_sales` (`sale_id`);

-- 
-- Constraints for TABLE `pos_sales_items_taxes`
-- 
ALTER TABLE `pos_sales_items_taxes`
  ADD CONSTRAINT `pos_sales_items_taxes_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `pos_sales_items` (`sale_id`),
  ADD CONSTRAINT `pos_sales_items_taxes_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `pos_sales_items` (`item_id`);

-- 
-- Constraints for TABLE `pos_sales_payments`
-- 
ALTER TABLE `pos_sales_payments`
  ADD CONSTRAINT `pos_sales_payments_ibfk_1` FOREIGN KEY (`sale_id`) REFERENCES `pos_sales` (`sale_id`);

-- 
-- Constraints for TABLE `pos_suppliers`
-- 
ALTER TABLE `pos_suppliers`
  ADD CONSTRAINT `pos_suppliers_ibfk_1` FOREIGN KEY (`person_id`) REFERENCES `pos_people` (`person_id`);

--
-- Constraints for TABLE `pos_transfers`
--
ALTER TABLE `pos_transfers`
  ADD CONSTRAINT `pos_transfers_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `pos_employees` (`person_id`),
  ADD CONSTRAINT `pos_transfers_ibfk_2` FOREIGN KEY (`branch_ref`) REFERENCES `pos_branches` (`branch_ref`);

--
-- Constraints for TABLE `pos_transfers_items`
--
ALTER TABLE `pos_transfers_items`
  ADD CONSTRAINT `pos_transfers_items_ibfk_1` FOREIGN KEY (`item_id`) REFERENCES `pos_items` (`item_id`),
  ADD CONSTRAINT `pos_transfers_items_ibfk_2` FOREIGN KEY (`transfer_id`) REFERENCES `pos_transfers` (`transfer_id`);
