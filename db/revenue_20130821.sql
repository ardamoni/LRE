--
-- Database: `revenue`
--

-- -----------------------------------------------------------
-- clarification of comments used
-- OLD - we should avoid using this column in our queries
-- MATCH - column matches our new design
-- NEW - new columns is added to the old LUPMIS structure
-- -----------------------------------------------------------

DROP TABLE IF EXISTS `area_region`;
CREATE TABLE IF NOT EXISTS `area_region` (
  `id` int NOT NULL AUTO_INCREMENT,			-- auto incremental
  `regionid` int(2) NOT NULL,				-- unique identifier for region
  `region_name` varchar(100) NOT NULL,		-- region name
  `activestatus` boolean DEFAULT NULL,		-- default NULL is active
  PRIMARY KEY (`id`),
  UNIQUE KEY `regionid` (`regionid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `area_district`;
CREATE TABLE IF NOT EXISTS `area_district` (
  `id` int NOT NULL AUTO_INCREMENT,
  `districtid` int(4) NOT NULL,
  `district_name` varchar(100) NOT NULL,
  `regionid` int(2) NOT NULL,
  `activestatus` boolean DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `districtid` (`districtid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `area_subdistrict`;
CREATE TABLE IF NOT EXISTS `area_subdistrict` (
  `id` int NOT NULL AUTO_INCREMENT,
  `subdistrictid` int(6) NOT NULL,
  `subdistrict_name` varchar(100) NOT NULL,
  `districtid` int(3) NOT NULL,
  `activestatus` boolean DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subdistrictid` (`subdistrictid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `area_zone`;
CREATE TABLE IF NOT EXISTS `area_zone` (
  `id` int NOT NULL AUTO_INCREMENT,
  `zoneid` int(8) NOT NULL,
  `zone_name` varchar(100) NOT NULL,
  `subdistrictid` int(6) NOT NULL,
  `activestatus` boolean DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `zoneid` (`zoneid`)  
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- ============================================== --

DROP TABLE IF EXISTS `property`;
CREATE TABLE IF NOT EXISTS `property` (
  `id` int NOT NULL AUTO_INCREMENT,				-- MATCH
  `upn` varchar(13) NOT NULL,					-- MATCH
  `subupn` varchar(20) DEFAULT NULL,			-- MATCH
  `year` int(5) DEFAULT NULL,  					-- MATCH
  `town` varchar(50) DEFAULT NULL,				-- MATCH
  `locpl` varchar(30) DEFAULT NULL,				-- MATCH
  `pay_status` int(11) DEFAULT NULL,			-- OLD
  `revenue_due` float DEFAULT NULL,				-- OLD
  `revenue_collected` float DEFAULT NULL,		-- OLD
  `revenue_balance` float DEFAULT NULL,			-- OLD
  `collector` varchar(50) DEFAULT NULL,			-- OLD
  `collector_id` int(11) DEFAULT NULL,			-- OLD
  `date_payment` date DEFAULT NULL,				-- OLD
  `regnumber` varchar(30) DEFAULT NULL,			-- OLD
  `streetname` varchar(50) DEFAULT NULL,		-- MATCH
  `housenumber` varchar(30) DEFAULT NULL,		-- MATCH
  `floor` int(11) DEFAULT NULL,					-- MATCH
  `unit_planning` int(11) DEFAULT NULL,			-- MATCH
  `zone_revenue` varchar(10) DEFAULT NULL,		-- OLD
  `locality_code` varchar(15) DEFAULT NULL,		-- MATCH
  `business` varchar(30) DEFAULT NULL,			-- OLD
  `structure_code` int DEFAULT NULL,			-- MATCH
  `owner` varchar(50) CHARACTER SET utf8 DEFAULT NULL,	-- OLD
  `owneraddress` varchar(50) DEFAULT NULL,		-- OLD
  `owner_tel` varchar(15) DEFAULT NULL,			-- OLD
  `owner_email` varchar(50) DEFAULT NULL,		-- OLD
  `rooms` int DEFAULT NULL,						-- MATCH
  `year_construction` int(11) DEFAULT NULL,		-- OLD
  `property_type` int DEFAULT NULL,				-- MATCH
  `property_use` int DEFAULT NULL,				-- MATCH
  `persons` int DEFAULT NULL,  					-- MATCH
  `roofing` int DEFAULT NULL,					-- MATCH
  `ownership_type` int DEFAULT NULL,			-- MATCH
  `constr_material` int DEFAULT NULL,			-- MATCH
  `storeys` int DEFAULT NULL,  					-- MATCH
  `value_prop` float DEFAULT NULL,				-- MATCH
  `prop_descriptor` int DEFAULT NULL,			-- MATCH
  `planningpermit` int DEFAULT NULL,			-- MATCH
  `planningpermit_no` varchar(30) DEFAULT NULL,	-- MATCH
  `buildingpermit` int DEFAULT NULL,			-- MATCH
  `buildingpermit_no` varchar(30) DEFAULT NULL,	-- MATCH
  `comments` varchar(50) DEFAULT NULL,			-- MATCH
  `utm_x` int(11) DEFAULT NULL,					-- MATCH
  `utm_y` int(11) DEFAULT NULL,					-- MATCH
  `area_m2` float DEFAULT NULL,					-- MATCH
  `district` varchar(20) DEFAULT NULL,			-- OLD
  `lastentry_person` varchar(10) DEFAULT NULL,	-- MATCH
  `lastentry_date` date DEFAULT NULL,			-- MATCH
  
  `districtid` int(4) NOT NULL,					-- NEW
  `subdistrictid` int(6) NOT NULL,				-- NEW
  `zoneid` int(8) DEFAULT NULL,  				-- NEW
  `doornumber` int(4) DEFAULT NULL,    			-- NEW
  `ownerid` int DEFAULT NULL,  					-- NEW
  `prop_descriptor_title` varchar(50) DEFAULT NULL,	-- NEW
  `rate_code` int NOT NULL,						-- NEW		-- all properties must have a rate code, if not assigned then default is 0 - exempt from rate
  `rate_impost_code` int DEFAULT NULL,			-- NEW		-- if not assigned then default is 0 - exempt from rate  
  `property_type_title` varchar(50) DEFAULT NULL,	-- NEW 
  `property_use_title` varchar(50) DEFAULT NULL,	-- NEW   
  `ownership_type_title` varchar(50) DEFAULT NULL,  -- NEW
  `constr_material_title` varchar(50) DEFAULT NULL, -- NEW
  `roofing_type_title` varchar(50) DEFAULT NULL,	-- NEW
  `date_start` int(11) DEFAULT NULL,			-- NEW
  `date_end` int(11) DEFAULT NULL,				-- NEW
  `activestatus` boolean DEFAULT NULL,			-- NEW -- default NULL is active
  `assessed` int(11) DEFAULT NULL,  			-- NEW
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `property_due`;
CREATE TABLE IF NOT EXISTS `property_due` (
  `id` int NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL, 
  `year` int(5) DEFAULT NULL,
  `prop_value` int(11) DEFAULT NULL,  
  `unit` varchar(30) DEFAULT NULL,			-- annually
  `assessed` int(11) DEFAULT NULL,			-- assesment date / code?
  `rate_value` float DEFAULT NULL,
  `rate_impost_value` float DEFAULT NULL,
  `feefi_code` int DEFAULT NULL,
  `feefi_value` float DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(100) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `property_payments`;
CREATE TABLE IF NOT EXISTS `property_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_value` float DEFAULT NULL,
  `instalments` int DEFAULT NULL,			-- number of instalments
  `instalment_order` int DEFAULT NULL,  		-- order of instalment
  `collectorid` varchar(30) DEFAULT NULL,		-- it is the user of the system and identified by username.
  `station_payment` varchar(20) DEFAULT NULL,
  `receipt_payment` varchar(30) DEFAULT NULL,
  `type_payment` varchar(30) DEFAULT NULL,
  `payer` varchar(50) DEFAULT NULL,
  `paid_for` varchar(20) DEFAULT NULL,  
  `demand_notice_no` varchar(30) DEFAULT NULL,
  `demand_notice_sent` date DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `property_balance`;
CREATE TABLE IF NOT EXISTS `property_balance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `due` float DEFAULT NULL,
  `payed` float DEFAULT NULL,
  `feefi_value` float DEFAULT NULL,
  `balance` float DEFAULT NULL, 
  `instalment` int(5) DEFAULT NULL,  
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- rate and rate impost are valid for property

DROP TABLE IF EXISTS `rates`;
CREATE TABLE IF NOT EXISTS `rates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `regionid` varchar(2) NOT NULL,
  `districtid` int(4) NOT NULL,
  `subdistrictid` int(6) NOT NULL,
  `zoneid` int(8) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `value` float DEFAULT NULL,   
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)  
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `rate_imposts`;
CREATE TABLE IF NOT EXISTS `rate_imposts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `regionid` varchar(2) NOT NULL,
  `districtid` int(4) NOT NULL,
  `subdistrictid` int(6) NOT NULL,
  `zoneid` int(8) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `value` float DEFAULT NULL,   
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`) 
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- =========================================================== --

DROP TABLE IF EXISTS `business`;
CREATE TABLE IF NOT EXISTS `business` (
  `id` int NOT NULL AUTO_INCREMENT,			-- MATCH
  `upn` varchar(13) NOT NULL,				-- MATCH -- must match the property
  `subupn` varchar(20) DEFAULT NULL,		-- MATCH 
  `id_property` int(11) DEFAULT NULL,		-- OLD
  `year` int(11) DEFAULT NULL, 				-- MATCH (it should be int(5))
  `town` varchar(50) DEFAULT NULL,			-- MATCH
  `locpl` varchar(30) DEFAULT NULL,			-- MATCH
  `pay_status` int(11) DEFAULT NULL,		-- OLD
  `revenue_due` float DEFAULT NULL,			-- OLD
  `revenue_collected` float DEFAULT NULL,	-- OLD
  `revenue_balance` float DEFAULT NULL,		-- OLD
  `collector` varchar(50) DEFAULT NULL,		-- OLD
  `collector_id` int(11) DEFAULT NULL,		-- OLD
  `date_payment` date DEFAULT NULL,			-- OLD
  `streetname` varchar(50) DEFAULT NULL,	-- MATCH
  `housenumber` varchar(30) DEFAULT NULL,	-- MATCH
  `unit_planning` int DEFAULT NULL,		-- MATCH
  `zone_revenue` varchar(10) DEFAULT NULL,	-- OLD
  `locality_code` varchar(15) DEFAULT NULL,	-- MATCH
  `da_no` varchar(30) DEFAULT NULL,			-- MATCH
  `business_certif` varchar(30) DEFAULT NULL,	-- MATCH
  `employees` int DEFAULT NULL,				-- MATCH
  `business_name` varchar(50) DEFAULT NULL,	-- MATCH
  `year_establ` int(11) DEFAULT NULL,		-- OLD
  `landmark1` varchar(50) DEFAULT NULL,		-- MATCH
  `landmark2` varchar(50) DEFAULT NULL, 	-- MATCH
  `owner` varchar(50) DEFAULT NULL,			-- OLD
  `owneraddress` varchar(50) DEFAULT NULL,	-- OLD
  `owner_tel` varchar(15) DEFAULT NULL,		-- OLD
  `owner_email` varchar(50) DEFAULT NULL,	-- OLD
  `business_class` int DEFAULT NULL,  		-- MATCH
  `comments` varchar(50) DEFAULT NULL,		-- MATCH
  `utm_x` int(11) DEFAULT NULL,				-- MATCH
  `utm_y` int(11) DEFAULT NULL,  			-- MATCH
  `area_m2` float DEFAULT NULL,				-- MATCH
  `district` varchar(20) DEFAULT NULL,		-- OLD
  `lastentry_person` varchar(10) DEFAULT NULL,	-- MATCH
  `lastentry_date` date DEFAULT NULL,		-- MATCH
  
  `districtid` int(4) NOT NULL,				-- NEW
  `subdistrictid` int(6) NOT NULL,			-- NEW
  `zoneid` int(8) DEFAULT NULL,				-- NEW
  `floor` int(11) DEFAULT NULL,				-- NEW
  `doornumber` int(4) DEFAULT NULL,			-- NEW	
  `ownerid` int DEFAULT NULL, 				-- NEW
  `date_start` int(11) DEFAULT NULL,		-- NEW
  `date_end` int(11) DEFAULT NULL, 			-- NEW
  `activestatus` boolean DEFAULT NULL,		-- NEW	-- default NULL is active
  `category_code` int NOT NULL,				-- NEW	-- all businesses must have a busines category code e.g. Restaurant is 6403
  `class_code` int DEFAULT NULL,			-- NEW	-- they may have classes, e.g. Fast Food
  `subclass_code` int DEFAULT NULL,			-- NEW	-- subclass, e.g. Restaurant - Fast Food - A  
  `pumps_no` int DEFAULT NULL,				-- NEW
  `storage_capacity` int DEFAULT NULL,		-- NEW
  `business_nature` int DEFAULT NULL,		-- NEW
  `environment_impact` int DEFAULT NULL,	-- NEW
  `business_type` int DEFAULT NULL,			-- NEW
  `business_code` int DEFAULT NULL, 		-- NEW
  `business_code_name` varchar(100) DEFAULT NULL, -- NEW  
  `assessed` int(11) DEFAULT NULL,			-- NEW  
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `business_due`;
CREATE TABLE IF NOT EXISTS `business_due` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,				-- must match the property
  `subupn` varchar(20) DEFAULT NULL,  
  `year` int(5) DEFAULT NULL,   
  `unit` varchar(30) DEFAULT NULL,  
  `assessed` int(11) DEFAULT NULL,
  `bo_value` float DEFAULT NULL,			-- sum of 13 values: category_code, class_code, subclass_code, locality_code, employees, area_m2, pumps_no, storage_capacity, business_nature, environment_impact, business_type and business_class
  `bo_impost_value` float DEFAULT NULL,		-- any additional values
  `feefi_code` int DEFAULT NULL,
  `feefi_value` float DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `business_payments`;
CREATE TABLE IF NOT EXISTS `business_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_value` float DEFAULT NULL,
  `instalments` int DEFAULT NULL,			-- number of instalments
  `instalment_order` int DEFAULT NULL,  		-- order of instalment
  `collectorid` varchar(30) DEFAULT NULL,		-- it is the user of the system and identified by username.
  `station_payment` varchar(20) DEFAULT NULL,
  `receipt_payment` varchar(30) DEFAULT NULL,
  `type_payment` varchar(30) DEFAULT NULL,
  `payer` varchar(50) DEFAULT NULL,
  `paid_for` varchar(20) DEFAULT NULL,  
  `demand_notice_no` varchar(30) DEFAULT NULL,
  `demand_notice_sent` date DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `business_balance`;
CREATE TABLE IF NOT EXISTS `business_balance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `due` float DEFAULT NULL,
  `payed` float DEFAULT NULL,
  `feefi_value` float DEFAULT NULL,
  `balance` float DEFAULT NULL, 
  `instalment` int(5) DEFAULT NULL,  
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- hotel, restaurant etc 64-01 to 64-43
DROP TABLE IF EXISTS `business_category`;
CREATE TABLE IF NOT EXISTS `business_category` (
  `id` int NOT NULL AUTO_INCREMENT,
  `regionid` varchar(13) NOT NULL,
  `districtid` int(4) NOT NULL,
  `subdistrictid` int(6) DEFAULT NULL,
  `zoneid` int(8) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `value` float DEFAULT NULL,   
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- restaurant, fast food joint, snack bar
DROP TABLE IF EXISTS `business_class`;
CREATE TABLE IF NOT EXISTS `business_class` (
  `id` int NOT NULL AUTO_INCREMENT,
  `regionid` varchar(13) NOT NULL,
  `districtid` int(4) NOT NULL,
  `subdistrictid` int(6) DEFAULT NULL,
  `zoneid` int(8) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `value` float DEFAULT NULL,   
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- category A, category B, category C, category D
DROP TABLE IF EXISTS `business_subclass`;
CREATE TABLE IF NOT EXISTS `business_subclass` (
  `id` int NOT NULL AUTO_INCREMENT,
  `regionid` varchar(13) NOT NULL,
  `districtid` int(4) NOT NULL,
  `subdistrictid` int(6) DEFAULT NULL,
  `zoneid` int(8) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `value` float DEFAULT NULL,   
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- Prime, good, fair
DROP TABLE IF EXISTS `business_location`;
CREATE TABLE IF NOT EXISTS `business_location` (
  `id` int NOT NULL AUTO_INCREMENT,
  `regionid` varchar(13) NOT NULL,
  `districtid` int(4) NOT NULL,
  `subdistrictid` int(6) DEFAULT NULL,
  `zoneid` int(8) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `value` float DEFAULT NULL,   
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- upto 10, 10-20, 20-50, over 50 
DROP TABLE IF EXISTS `business_employees`;
CREATE TABLE IF NOT EXISTS `business_employees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `regionid` varchar(13) NOT NULL,
  `districtid` int(4) NOT NULL,
  `subdistrictid` int(6) DEFAULT NULL,
  `zoneid` int(8) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `value` float DEFAULT NULL,   
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- less than 20m2, 20-50m2, 100-500m2, over 500m2
DROP TABLE IF EXISTS `business_area`;
CREATE TABLE IF NOT EXISTS `business_area` (
  `id` int NOT NULL AUTO_INCREMENT,
  `regionid` varchar(13) NOT NULL,
  `districtid` int(4) NOT NULL,
  `subdistrictid` int(6) DEFAULT NULL,
  `zoneid` int(8) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `value` float DEFAULT NULL,   
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- upto 3, 4-6, over 6
DROP TABLE IF EXISTS `business_pumps`;
CREATE TABLE IF NOT EXISTS `business_pumps` (
  `id` int NOT NULL AUTO_INCREMENT,
  `regionid` varchar(13) NOT NULL,
  `districtid` int(4) NOT NULL,
  `subdistrictid` int(6) DEFAULT NULL,
  `zoneid` int(8) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `value` float DEFAULT NULL,   
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- upto 100m2, 100-1000m2, 1000-5000m2, over 5000m2
DROP TABLE IF EXISTS `business_storage capacity`;
CREATE TABLE IF NOT EXISTS `business_storage_capacity` (
  `id` int NOT NULL AUTO_INCREMENT,
  `regionid` varchar(13) NOT NULL,
  `districtid` int(4) NOT NULL,
  `subdistrictid` int(6) DEFAULT NULL,
  `zoneid` int(8) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `value` float DEFAULT NULL,   
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- local, national, international
DROP TABLE IF EXISTS `business_nature`;
CREATE TABLE IF NOT EXISTS `business_nature` (
  `id` int NOT NULL AUTO_INCREMENT,
  `regionid` varchar(13) NOT NULL,
  `districtid` int(4) NOT NULL,
  `subdistrictid` int(6) DEFAULT NULL,
  `zoneid` int(8) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `value` float DEFAULT NULL,   
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- road damage, waste genrated, pollution: noise, air, water.
DROP TABLE IF EXISTS `business_environment_impact`;
CREATE TABLE IF NOT EXISTS `business_environment_impact` (
  `id` int NOT NULL AUTO_INCREMENT,
  `regionid` varchar(13) NOT NULL,
  `districtid` int(4) NOT NULL,
  `subdistrictid` int(6) DEFAULT NULL,
  `zoneid` int(8) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `value` float DEFAULT NULL,   
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- main office, area office, branch office, agency
DROP TABLE IF EXISTS `business_type`;
CREATE TABLE IF NOT EXISTS `business_type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `regionid` varchar(13) NOT NULL,
  `districtid` int(4) NOT NULL,
  `subdistrictid` int(6) DEFAULT NULL,
  `zoneid` int(8) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `value` float DEFAULT NULL,   
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- this table is for future use and will include above 12 business "type/category" distinction tables
DROP TABLE IF EXISTS `business_code`;
CREATE TABLE IF NOT EXISTS `business_code` (
  `id` int NOT NULL AUTO_INCREMENT,
  `regionid` varchar(13) NOT NULL,
  `districtid` int(4) NOT NULL,
  `subdistrictid` int(6) DEFAULT NULL,
  `zoneid` int(8) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `value` float DEFAULT NULL,  
  `category_code` int NOT NULL,	
  `class_code` int DEFAULT NULL,				-- they may have classes, e.g. Fast Food
  `subclass_code` int DEFAULT NULL,				-- subclass, e.g. Restaurant - Fast Food - A
  `locality_code` int DEFAULT NULL,  
  `employees` int DEFAULT NULL,
  `area_m2` int DEFAULT NULL,
  `pumps_no` int DEFAULT NULL,
  `storage_capacity` int DEFAULT NULL,
  `business_nature` int DEFAULT NULL,
  `environment_impact` int DEFAULT NULL,
  `business_type` int DEFAULT NULL,			
  `business_class` int DEFAULT NULL,  
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- =============================================== --

DROP TABLE IF EXISTS `rent`;
CREATE TABLE IF NOT EXISTS `rent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,  
  `districtid` int(4) NOT NULL,
  `town` varchar(50) DEFAULT NULL,
  `locpl` varchar(30) DEFAULT NULL,
  `streetname` varchar(50) DEFAULT NULL,
  `housenumber` varchar(30) DEFAULT NULL,
  `floor` int(11) DEFAULT NULL,
  `doornumber` int(4) DEFAULT NULL,
  `landmark1` varchar(50) DEFAULT NULL,
  `landmark2` varchar(50) DEFAULT NULL,  
  `rent_value` float DEFAULT NULL,			-- the value of this "property" as assessed annually
  `assessed` int DEFAULT NULL,
  `contractid` varchar(50) DEFAULT NULL, 
  `contract_name` varchar(100) DEFAULT NULL, 
  `contract_value` float DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `date_start` int(11) DEFAULT NULL,
  `date_end` int(11) DEFAULT NULL,
  `activestatus` boolean DEFAULT NULL, 			-- default NULL is let (rented out)
  `ownerid` int(12) DEFAULT NULL,
  `clientid` int(12) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `utm_x` int(11) DEFAULT NULL,
  `utm_y` int(11) DEFAULT NULL,
  `area_m2` float DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `upn` (`upn`, `subupn`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `rent_due`;
CREATE TABLE IF NOT EXISTS `rent_due` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,  
  `unit` varchar(30) DEFAULT NULL,			-- annually, monthly, etc
  `assessed` int(11) DEFAULT NULL,
  `feefi_code` int DEFAULT NULL,
  `feefi_value` float DEFAULT NULL,  
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `rent_payments`;
CREATE TABLE IF NOT EXISTS `rent_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_value` float DEFAULT NULL,
  `instalments` int DEFAULT NULL,			-- number of instalments
  `instalment_order` int DEFAULT NULL,  		-- order of instalment
  `collectorid` varchar(30) DEFAULT NULL,		-- it is the user of the system and identified by username.
  `station_payment` varchar(20) DEFAULT NULL,
  `receipt_payment` varchar(30) DEFAULT NULL,
  `type_payment` varchar(30) DEFAULT NULL,
  `payer` varchar(50) DEFAULT NULL,
  `paid_for` varchar(20) DEFAULT NULL,  
  `demand_notice_no` varchar(30) DEFAULT NULL,
  `demand_notice_sent` date DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `rent_balance`;
CREATE TABLE IF NOT EXISTS `rent_balance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `due` float DEFAULT NULL,
  `payed` float DEFAULT NULL,
  `feefi_value` float DEFAULT NULL,
  `balance` float DEFAULT NULL,
  `instalment` int(5) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- ===================================

DROP TABLE IF EXISTS `land`;
CREATE TABLE IF NOT EXISTS `land` (
  `id` int NOT NULL AUTO_INCREMENT,
  `districtid` int(4) NOT NULL,
  `landid` varchar(50) NOT NULL, 
  `land_name` varchar(100) NOT NULL,  
  `land_value` float DEFAULT NULL,
  `year` int(5) DEFAULT NULL,  
  `assessed` int DEFAULT NULL,
  `contractid` varchar(50) DEFAULT NULL, 
  `contract_name` varchar(100) DEFAULT NULL, 
  `contract_value` float DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `date_start` int(11) DEFAULT NULL,
  `date_end` int(11) DEFAULT NULL,
  `activestatus` boolean NULL,				-- NULL is active
  `ownerid` int(12) NOT NULL,
  `clientid` int(12) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `utm_x` int(11) DEFAULT NULL,
  `utm_y` int(11) DEFAULT NULL,
  `area_m2` float DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `land_due`;
CREATE TABLE IF NOT EXISTS `land_due` (
  `id` int NOT NULL AUTO_INCREMENT,
  `landid` varchar(50) NOT NULL,
  `year` int(5) DEFAULT NULL,
  `unit` varchar(30) DEFAULT NULL,			-- annually, monthly, etc
  `assessed` int(11) DEFAULT NULL,
  `feefi_code` int DEFAULT NULL,
  `feefi_value` float DEFAULT NULL,  
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `land_payments`;
CREATE TABLE IF NOT EXISTS `land_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `landid` varchar(50) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_value` float DEFAULT NULL,
  `instalments` int DEFAULT NULL,			-- number of instalments
  `instalment_order` int DEFAULT NULL,  		-- order of instalment
  `collectorid` varchar(30) DEFAULT NULL,		-- it is the user of the system and identified by username.
  `station_payment` varchar(20) DEFAULT NULL,
  `receipt_payment` varchar(30) DEFAULT NULL,
  `type_payment` varchar(30) DEFAULT NULL,
  `payer` varchar(50) DEFAULT NULL,
  `paid_for` varchar(20) DEFAULT NULL,  
  `demand_notice_no` varchar(30) DEFAULT NULL,
  `demand_notice_sent` date DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


DROP TABLE IF EXISTS `land_balance`;
CREATE TABLE IF NOT EXISTS `land_balance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `landid` varchar(50) NOT NULL,
  `year` int(5) DEFAULT NULL,
  `due` float DEFAULT NULL,
  `payed` float DEFAULT NULL,
  `feefi_value` float DEFAULT NULL,
  `balance` float DEFAULT NULL,  
  `instalment` int(5) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- ===========================================

DROP TABLE IF EXISTS `investments`;
CREATE TABLE IF NOT EXISTS `investments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `districtid` int(4) NOT NULL,
  `investmentid` varchar(50) NOT NULL, 
  `investment_name` varchar(100) NOT NULL,  
  `investment_value` float DEFAULT NULL,
  `year` int(5) DEFAULT NULL,  
  `assessed` int DEFAULT NULL,
  `contractid` varchar(50) DEFAULT NULL, 
  `contract_name` varchar(100) DEFAULT NULL, 
  `contract_value` float DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `date_start` int(11) DEFAULT NULL,
  `date_end` int(11) DEFAULT NULL,
  `activestatus` boolean NULL,				-- NULL is active
  `ownerid` int(12) DEFAULT NULL,
  `clientid` int(12) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `investment_due`;
CREATE TABLE IF NOT EXISTS `investment_due` (
  `id` int NOT NULL AUTO_INCREMENT,
  `investmentid` varchar(50) NOT NULL, 
  `year` int(5) DEFAULT NULL,
  `unit` varchar(30) DEFAULT NULL,			-- annually
  `assessed` int(11) DEFAULT NULL,
  `feefi_code` int DEFAULT NULL,
  `feefi_value` float DEFAULT NULL,  
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `investment_payments`;
CREATE TABLE IF NOT EXISTS `investment_payments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `investmentid` varchar(50) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_value` float DEFAULT NULL,
  `instalments` int DEFAULT NULL,				-- number of instalments
  `instalment_order` int DEFAULT NULL,  		-- order of instalment
  `collectorid` varchar(30) DEFAULT NULL,		-- it is the user of the system and identified by username.
  `station_payment` varchar(20) DEFAULT NULL,
  `receipt_payment` varchar(30) DEFAULT NULL,
  `type_payment` varchar(30) DEFAULT NULL,
  `payer` varchar(50) DEFAULT NULL,
  `paid_for` varchar(20) DEFAULT NULL,  
  `demand_notice_no` varchar(30) DEFAULT NULL,
  `demand_notice_sent` date DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `investment_balance`;
CREATE TABLE IF NOT EXISTS `investment_balance` (
  `id` int NOT NULL AUTO_INCREMENT,
  `investmentid` int(11) NOT NULL,
  `year` int(5) DEFAULT NULL,
  `due` float DEFAULT NULL,
  `payed` float DEFAULT NULL,
  `feefi_value` float DEFAULT NULL,
  `balance` float DEFAULT NULL,
  `instalment` int(5) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- ==================================================

DROP TABLE IF EXISTS `fees_fines`;
CREATE TABLE IF NOT EXISTS `fees_fines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `regionid` int(2) DEFAULT NULL,
  `districtid` int(4) DEFAULT NULL,
  `subdistrictid` int(6) DEFAULT NULL,
  `zoneid` int(8) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `category_code` int NOT NULL,
  `category_desc` varchar(100) NOT NULL,
  `class_code` int NOT NULL,
  `class_desc` varchar(100) NOT NULL,
  `subclass_code` int DEFAULT NULL,
  `subclass_desc` varchar(100) NOT NULL,
  `subsub_code` int DEFAULT NULL,
  `subsub_desc` varchar(100) NOT NULL,
  `code` int NOT NULL,
  `code_desc` varchar(100) NOT NULL,
  `value` float DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- =============================================

DROP TABLE IF EXISTS `own_owner`;
CREATE TABLE IF NOT EXISTS `own_owner` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `ownerid` int(12) NOT NULL,
  `type_code` int(2) NOT NULL,					-- individual, joint, private, company etc
  `description` varchar(100) DEFAULT NULL,		-- title of the code
  `name` varchar(100) NOT NULL,					-- owner name and last name
  `parent_name` varchar(100) DEFAULT NULL,
  `dob` date NOT NULL,							-- date of birth or date of establishment
  `address1` varchar(100) NOT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `address3` varchar(100) DEFAULT NULL,
  `tel1` varchar(15) DEFAULT NULL,
  `tel2` varchar(15) DEFAULT NULL,
  `tel3` varchar(15) DEFAULT NULL,
  `email1` varchar(50) DEFAULT NULL,
  `email2` varchar(50) DEFAULT NULL,
  `email3` varchar(50) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ownerid` (`ownerid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

DROP TABLE IF EXISTS `own_client`;
CREATE TABLE IF NOT EXISTS `own_client` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `clientid` int(12) NOT NULL,
  `type_code` int(2) NOT NULL,					-- individual, joint, private, company etc
  `description` varchar(100) DEFAULT NULL,		-- title of the code
  `name` varchar(100) NOT NULL,					-- owner name and last name
  `parent_name` varchar(100) DEFAULT NULL,
  `dob` date NOT NULL,							-- date of birth or date of establishment
  `address1` varchar(100) NOT NULL,
  `address2` varchar(100) DEFAULT NULL,
  `address3` varchar(100) DEFAULT NULL,
  `tel1` varchar(15) DEFAULT NULL,
  `tel2` varchar(15) DEFAULT NULL,
  `tel3` varchar(15) DEFAULT NULL,
  `email1` varchar(50) DEFAULT NULL,
  `email2` varchar(50) DEFAULT NULL,
  `email3` varchar(50) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `clientid` (`clientid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- ============================================

-- user, collector, manager, admin, master
DROP TABLE IF EXISTS `usr_roles`;
CREATE TABLE IF NOT EXISTS `usr_roles` (  
  `id` int(3) NOT NULL,				
  `description` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `usr_users`;
CREATE TABLE IF NOT EXISTS `usr_users` (
  `username` varchar(30) NOT NULL,
  `pass` varchar(40) NOT NULL DEFAULT '751ec45015a704a39dc403001c963e97',
  `adminpass` varchar(40) NOT NULL DEFAULT '751ec45015a704a39dc403001c963e97',
  `masterpass` varchar(40) NOT NULL DEFAULT '751ec45015a704a39dc403001c963e97',
  `title` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `position` varchar(50) DEFAULT NULL,
  `email` varchar(40) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `baselanguage` int(1) DEFAULT '0',		-- 0 for English
  `activestatus` boolean DEFAULT NULL,		-- default NULL is active user
  `loged` boolean DEFAULT NULL,				-- default NULL is logging 
  PRIMARY KEY (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `usr_user_role`;
CREATE TABLE IF NOT EXISTS `usr_user_role` (
  `username` varchar(30) NOT NULL,
  `roleid` int(3) NOT NULL,						
  KEY `username` (`username`,`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `usr_user_region`;
CREATE TABLE IF NOT EXISTS `usr_user_region` (
  `username` varchar(30) NOT NULL,
  `regionid` int(2) NOT NULL,
  KEY `username` (`username`,`regionid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `usr_user_district`;
CREATE TABLE IF NOT EXISTS `usr_user_district` (
  `username` varchar(30) NOT NULL,
  `regionid` int(2) NOT NULL,
  `districtid` int(4) NOT NULL,
  KEY `username` (`username`,`districtid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `usr_user_subdistrict`;
CREATE TABLE IF NOT EXISTS `usr_user_subdistrict` (
  `username` varchar(30) NOT NULL,
  `regionid` int(2) NOT NULL,
  `districtid` int(4) NOT NULL,
  `subdistrictid` int(6) NOT NULL,
  KEY `username` (`username`,`subdistrictid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `usr_user_zone`;
CREATE TABLE IF NOT EXISTS `usr_user_zone` (
  `username` varchar(30) NOT NULL,
  `regionid` int(2) NOT NULL,
  `districtid` int(4) NOT NULL,
  `subdistrictid` int(6) NOT NULL,
  `zoneid` int(8) NOT NULL,
  KEY `username` (`username`,`zoneid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `usr_user_logs`;
CREATE TABLE IF NOT EXISTS `usr_user_logs` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `activity` varchar(5) NOT NULL,
  `time` varchar(20) NOT NULL,
  `comments` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- =================================================

CREATE TABLE `system_labels` (
  `id` varchar(50) NOT NULL,
  `lang` int(1) NOT NULL,
  `label` varchar(500) NOT NULL,
  `info` text,
  `error` varchar(300) default NULL,
  PRIMARY KEY  (`id`,`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

-- ===================================================

CREATE TABLE `tickets` (
  `id` varchar(50) NOT NULL,
  `starting` varchar(50) NOT NULL,
  `ending` varchar(50) NOT NULL,
  `username` varchar(30) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;