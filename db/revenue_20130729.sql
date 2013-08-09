--===============================================================--
-- user, roles and relation to region, district and sub-district --
--===============================================================--

CREATE TABLE `usr_users` (
  `username` varchar(30) NOT NULL,
  `pass` varchar(40) NOT NULL default '751ec45015a704a39dc403001c963e97',
  `masterpass` varchar(40) NOT NULL default '751ec45015a704a39dc403001c963e97',
  `title` varchar(50) default NULL,
  `name` varchar(50) default NULL,  
  `role` varchar(50) default NULL,
  `email` varchar(40) default NULL,
  `phone` varchar(15) default NULL,
  `baselanguage` int(1) default '1',
  `activestatus` varchar(5) NOT NULL,
  `loged` int(1) default '0',
  PRIMARY KEY  (`username`)
);

CREATE TABLE `usr_user_role` (
  `username` varchar(30) NOT NULL,
  `roleid` int(3) NOT NULL,
  KEY `username` (`username`,`roleid`)
);

CREATE TABLE `usr_user_region` (
  `username` varchar(30) NOT NULL,
  `regionid` int(7) NOT NULL,
  KEY `username` (`username`,`regionid`)
);

CREATE TABLE `usr_user_district` (
  `username` varchar(30) NOT NULL,
  `districtid` int(7) NOT NULL,
  KEY `username` (`username`,`districtid`)
);

CREATE TABLE `usr_user_subdistrict` (
  `username` varchar(30) NOT NULL,
  `subdistrictid` int(7) NOT NULL,
  PRIMARY KEY  (`username`,`subdistrictid`)
);

CREATE TABLE `usr_user_logs` (
  `id` int(7) NOT NULL auto_increment,
  `username` varchar(30) NOT NULL,
  `activity` varchar(5) NOT NULL,
  `time` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`)
);


--==============================================--
-- regions, district and subdistrict --
--==============================================--

CREATE TABLE `area_region` (
  `id` int(3) NOT NULL,   -- this is region_id, there are only 10 regions, but future increase may be possible
  `lang` int(1) NOT NULL,  -- english + 8 other languages
  `region_name` varchar(100),  
  `activestatus` varchar(5) NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE `area_district` (
  `id` int(3) NOT NULL,		-- this is district_id, region has 30 districts, but it may increase in the future
  `lang` int(1) NOT NULL,		-- english + 8 other languages
  `district_name` varchar(100) NOT NULL,
  `regionid` int(3) NOT NULL,
  `activestatus` varchar(5) NOT NULL,
  PRIMARY KEY  (`id`)  
);

CREATE TABLE `area_subdistrict` (
  `id` int(7) NOT NULL,		-- this is subdistrict_id
  `lang` int(1) NOT NULL,
  `subdistrict_name` varchar(100),
  `districtid` int(3) NOT NULL,
  `activestatus` varchar(5) NOT NULL,
  PRIMARY KEY  (`id`)
);

CREATE TABLE `area_zone` (
  `id` int(7) NOT NULL,		-- this is zone_id
  `lang` int(1) NOT NULL,
  `zone_name` varchar(100),
  `subdistrictid` int(7) NOT NULL,
  `activestatus` varchar(5) NOT NULL,
  PRIMARY KEY  (`id`)
);

--============================================--
-- revenue --
--============================================--

-- create table for owners
CREATE TABLE `own_owners` (
  `id` int(12) NOT NULL AUTO_INCREMENT, 	-- owner_id, this can cover 30 milion inhabitants in Ghana
  `type_code` int(2) not NULL,				-- government, state company, private company, private individual, foreign company etc.
  `type_title` varchar(100) DEFAULT NULL,
  `name` varchar(100) NOT NULL,			-- owner's names,  family name
  `parent_name` varchar(100) DEFAULT NULL,	-- to distringuish between more John Doe, in the same sub-district
  `date` date NOT NULL, 					-- date of birth, date of established 
  `subdistrictid` int(7) NOT NULL, 			-- another filter to distinguish between John Doe, on district level
  `address1` varchar(50) DEFAULT NULL,		-- owner may have several addresses
  `address2` varchar(50) DEFAULT NULL,
  `address3` varchar(50) DEFAULT NULL,
  `tel1` varchar(15) DEFAULT NULL,			-- owner may have several telephones
  `tel2` varchar(15) DEFAULT NULL,
  `tel3` varchar(15) DEFAULT NULL,
  `email1` varchar(50) DEFAULT NULL,		-- owner may have several e-mails
  `email2` varchar(50) DEFAULT NULL,
  `email3` varchar(50) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,		-- comments & other
  PRIMARY KEY (`id`)						-- id is unique to the owner
);

-- owner related to business and property
CREATE TABLE `own_owner_business_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ownerid` int(12) NOT NULL,   			-- id of the owner, comes from owner table (id)
  `upn` varchar(13) NOT NULL,				-- unique for each parcel
  `subupn` varchar(20) DEFAULT NULL,		-- unique for each sub-parcel
  `subdistrictid` int(7) NOT NULL, 			-- sub-district
  `id_property` int(11) DEFAULT NULL,		-- if necessary
  PRIMARY KEY (`id`)
);

CREATE TABLE `business` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `subdistrictid` int(7) NOT NULL,
  `streetname` varchar(50) DEFAULT NULL,
  `housenumber` varchar(30) DEFAULT NULL,
  `town` varchar(50) DEFAULT NULL,
  `locpl` varchar(30) DEFAULT NULL, 		 -- what's the full name?
  `landmark1` varchar(50) DEFAULT NULL,
  `landmark2` varchar(50) DEFAULT NULL,
  `zoneid` int(7) DEFAULT NULL,  				-- different zones within one sub-district, rate-impost varies 
  `locality_code` varchar(15) DEFAULT NULL,  	-- format xxx-x-xxx
  `da_no` varchar(30) DEFAULT NULL, 		 -- ?
  `ownerid` int(12) DEFAULT NULL,
  `business_certif` varchar(30) DEFAULT NULL,  
  `business_name` varchar(50) DEFAULT NULL,
  `date_start` int(11) DEFAULT NULL,			-- established date
  `date_end` int(11) DEFAULT NULL,  			-- closed date, old businesses that are closed, keep history
  `activestatus` boolean NULL,  				-- old businesses that are closed, keep history, speed up search, 1 - closed, 0 - open (default)
  `business_class` int(11) DEFAULT NULL, 		-- classification_code
  `business_type` int(11) DEFAULT NULL,  		-- type_code
  `employees` int(11) DEFAULT NULL,				-- number of employees
  `comments` varchar(50) DEFAULT NULL,
  `utm_x` int(11) DEFAULT NULL,
  `utm_y` int(11) DEFAULT NULL,
  `area_m2` float DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
  UNIQUE KEY `upn` (`upn`)
  UNIQUE KEY `subupn` (`subupn`)
);

CREATE TABLE `property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `subdistrictid` int(7) NOT NULL,
  `streetname` varchar(50) DEFAULT NULL,
  `housenumber` varchar(30) DEFAULT NULL,
  `floor` int(11) DEFAULT NULL,
  `doornumber` int(4) DEFAULT NULL,
  `rooms` int(11) DEFAULT NULL,
  `town` varchar(50) DEFAULT NULL,
  `locpl` varchar(30) DEFAULT NULL,  --  
  `unit_planning` int(11) DEFAULT NULL, --
  `zoneid` int(7) DEFAULT NULL, 
  `locality_code` varchar(15) DEFAULT NULL,  	-- format xxx-x-xxx  
  `structurecode` int(11) DEFAULT NULL,  --?
  `ownerid` int(12) DEFAULT NULL,    
  `prop_code` varchar(24) DEFAULT NULL,			-- type (2), use (2), roofing (2), ownership (2), material (2), storeys (3), persons (5)  xx-xx-xx-xx-xx-xxx-xxxxx
  `value_prop` int(11) DEFAULT NULL, 			
  `prop_descriptor` int(11) DEFAULT NULL, --?
  `planningpermit` int(11) DEFAULT NULL,
  `planningpermit_no` varchar(30) DEFAULT NULL,
  `buildingpermit` int(11) DEFAULT NULL,
  `buildingpermit_no` varchar(30) DEFAULT NULL,
  `date_start` int(11) DEFAULT NULL,			-- date the building completed
  `date_end` int(11) DEFAULT NULL,  			-- date the building was destroyed, keep history
  `activestatus` boolean NOT NULL,  			-- 0 = aktive, 1 = not active (destroyed), keep history, speed up search
  `comments` varchar(50) DEFAULT NULL,
  `utm_x` int(11) DEFAULT NULL,
  `utm_y` int(11) DEFAULT NULL,
  `area_m2` float DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
  UNIQUE KEY `upn` (`upn`)
  UNIQUE KEY `subupn` (`subupn`)
);

CREATE TABLE `other` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` int(11) DEFAULT NULL,
  `subdistrictid` int(7) DEFAULT NULL,
  `zoneid` int(7) DEFAULT NULL,
  `town` varchar(50) DEFAULT NULL,  
  `locpl` varchar(30) DEFAULT NULL, -- what is full name?
  `unit_planning` int(11) DEFAULT NULL,  -- ?  
  `locality_code` varchar(15) DEFAULT NULL,  -- ?
  `da_no` varchar(30) DEFAULT NULL, -- ?
  `landmark1` varchar(50) DEFAULT NULL,
  `landmark2` varchar(50) DEFAULT NULL,
  `ownerid` int(12) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `utm_x` int(11) DEFAULT NULL,
  `utm_y` int(11) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `fees_business` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subdistrictid` int(7) NOT NULL,
  `zoneid` int(7) DEFAULT NULL, 		 
  `year` int(5) DEFAULT NULL,
  `class_code` int(11) DEFAULT NULL,				-- business classification
  `class_title` varchar(100) DEFAULT NULL,
  `type_code` int(11) DEFAULT NULL, 				-- business type code
  `type_title` varchar(100) DEFAULT NULL,  			-- business type name of the code  
  `unit` varchar(30) DEFAULT NULL,  				-- measuring unit e.g. week, month etc
  `assessed` int(11) DEFAULT NULL,  				-- assessment date
  `rate` float DEFAULT NULL, 						-- fee
  `rate_impost` float DEFAULT NULL, 				-- % on top of fee, or reduction  
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `fees_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT, 
  `subdistrictid` int(7) NOT NULL,
  `zoneid` int(7) DEFAULT NULL, 	
  `year` int(5) DEFAULT NULL,  					-- construction finished year
  `property_type` int(2) DEFAULT NULL, 			-- 10 types
  `property_type_title` varchar(50) DEFAULT NULL, 			
  `property_use` int(2) DEFAULT NULL, 			-- 7 use
  `property_use_title` varchar(50) DEFAULT NULL, 
  `roofing_type` int(2) DEFAULT NULL,				-- 6 types
  `roofing_type_title` varchar(50) DEFAULT NULL,
  `ownership_type` int(2) DEFAULT NULL,			-- 6 types
  `ownership_type_title` varchar(50) DEFAULT NULL,
  `constr_material` int(2) DEFAULT NULL,		-- 6 types
  `constr_material_title` varchar(50) DEFAULT NULL,
  `storeys` int(3) DEFAULT NULL,				-- max: 999 storeys 
  `persons` int(5) DEFAULT NULL,				-- max 99999, persons 
  `unit` varchar(30) DEFAULT NULL, 				
  `assessed` int(11) DEFAULT NULL,
  `rate` float DEFAULT NULL, 
  `rate_impost` float DEFAULT NULL,  
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`) 
);

-- market tolls, slaughter house, marriages and divorces, 
-- fees for waste management services, park user fees, burial permits and on-street parking fees
CREATE TABLE `fees_other` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subdistrictid` int(7) DEFAULT NULL,
  `zoneid` int(7) DEFAULT NULL, 
  `year` int(5) DEFAULT NULL,
  `code` int(11) DEFAULT NULL, 
  `code_title` varchar(50) DEFAULT NULL,   
  `class_code` int(4) DEFAULT NULL,     
  `class_title` varchar(50) DEFAULT NULL, 
  `subclass_code` int(5) DEFAULT NULL, 
  `subclass_title` varchar(50) DEFAULT NULL, 
  `unit` varchar(30) DEFAULT NULL,  --?
  `assessed` int(11) DEFAULT NULL,  
  `rate` float DEFAULT NULL,
  `rate_impost` float DEFAULT NULL,  
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `payments_business_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `business` boolean DEFAULT NULL,		-- 0 = bunsiness, 1 = property
  `subdistrictid` int(7) NOT NULL,
  `zoneid` int(7) DEFAULT NULL,  
  `date_payment` date DEFAULT NULL, -- use year in the code, to make sure no fine is added the following year
  `payment` float DEFAULT NULL,
  `station_payment` varchar(20) DEFAULT NULL,
  `receipt_payment` varchar(30) DEFAULT NULL,
  `type_payment` varchar(30) DEFAULT NULL,
  `payer` varchar(50) DEFAULT NULL,
  `paid_for` varchar(20) DEFAULT NULL,
  `collector` varchar(50) DEFAULT NULL,
  `collector_id` int(11) DEFAULT NULL,
  `demand_notice_no` varchar(30) DEFAULT NULL,
  `demand_notice_sent` date DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
 );

CREATE TABLE `payments_other` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) DEFAULT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `subdistrictid` int(7) DEFAULT NULL,
  `zoneid` int(7) DEFAULT NULL, 
  `code` int(11) DEFAULT NULL,		
  `date_payment` date DEFAULT NULL, -- use year in the code, to make sure no fine is added the following year
  `payment` float DEFAULT NULL,
  `station_payment` varchar(20) DEFAULT NULL,
  `receipt_payment` varchar(30) DEFAULT NULL,
  `type_payment` varchar(30) DEFAULT NULL,
  `payer` varchar(50) DEFAULT NULL,
  `paid_for` varchar(20) DEFAULT NULL,
  `collector` varchar(50) DEFAULT NULL,
  `collector_id` int(11) DEFAULT NULL,
  `demand_notice_no` varchar(30) DEFAULT NULL,
  `demand_notice_sent` date DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
 );
 
CREATE TABLE `balance_business_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `business` boolean DEFAULT NULL,		-- 0 = bunsiness, 1 = property
  `subdistrictid` int(7) NOT NULL,
  `zoneid` int(7) DEFAULT NULL, 
  `year` int(5) DEFAULT NULL,
  `owed` float DEFAULT NULL, 		-- rate, 100, 100
  `payed` float DEFAULT NULL, 		-- 50, 45, 0
  `fine` float DEFAULT NULL, 		-- 1%
  `balance` float DEFAULT NULL, 	-- 50, 5, 105.005
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `balance_other` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) DEFAULT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `subdistrictid` int(7) DEFAULT NULL,
  `zoneid` int(7) DEFAULT NULL, 
  `code` int(11) DEFAULT NULL,	
  `year` int(5) DEFAULT NULL,
  `owed` float DEFAULT NULL,
  `payed` float DEFAULT NULL,
  `fine` float DEFAULT NULL,
  `balance` float DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
 
-- NON - SPATIAL 

-- Lands
CREATE TABLE `lands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `districtid` int(3) NOT NULL, 			-- district
  `regnumber` varchar(30) DEFAULT NULL, -- what is this?
  `ownerid` int(12) DEFAULT NULL,
  `date_start` int(11) DEFAULT NULL,
  `date_end` int(11) DEFAULT NULL,  
  `activestatus` varchar(5) NOT NULL,
  `name` varchar(100) DEFAULT NULL,			-- tenant
  `parent_name` varchar(100) DEFAULT NULL,	-- to distringuish between more John Doe, in the same sub-district
  `address` varchar(50) DEFAULT NULL,		-- tenant's address
  `tel` varchar(15) DEFAULT NULL,			-- tenant's phone
  `email` varchar(50) DEFAULT NULL,			-- tenant's email  
  `comments` varchar(50) DEFAULT NULL,
  `utm_x` int(11) DEFAULT NULL,
  `utm_y` int(11) DEFAULT NULL,
  `area_m2` float DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `balance_lands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `landsid` int(11) NOT NULL,
  `year` int(5) DEFAULT NULL,
  `owed` float DEFAULT NULL, 		-- rate, 100, 100
  `payed` float DEFAULT NULL, 		-- 50, 45, 0
  `fine` float DEFAULT NULL, 		-- 1%
  `balance` float DEFAULT NULL, 	-- 50, 5, 105.005
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- fines
CREATE TABLE `fines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subdistrictid` int(7) DEFAULT NULL,
  `zoneid` int(7) DEFAULT NULL, 
  `year` int(5) DEFAULT NULL,
  `code` int(11) DEFAULT NULL, 
  `code_title` varchar(100) DEFAULT NULL,   
  `fine` float DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- rent - has upn - very much like property but owned by government
CREATE TABLE `rent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `subdistrictid` int(7) NOT NULL,
  `zoneid` int(7) DEFAULT NULL, 
  `town` varchar(50) DEFAULT NULL,
  `locpl` varchar(30) DEFAULT NULL,  
  `streetname` varchar(50) DEFAULT NULL,
  `housenumber` varchar(30) DEFAULT NULL,
  `landmark1` varchar(50) DEFAULT NULL,
  `landmark2` varchar(50) DEFAULT NULL,
  `ownerid` int(12) DEFAULT NULL,
  `date_start` int(11) DEFAULT NULL,
  `date_end` int(11) DEFAULT NULL,  
  `activestatus` varchar(5) NOT NULL, 
  `name` varchar(100) DEFAULT NULL,			-- tenant
  `parent_name` varchar(100) DEFAULT NULL,	-- to distringuish between more John Doe, in the same sub-district
  `address` varchar(50) DEFAULT NULL,		-- tenant's address
  `tel` varchar(15) DEFAULT NULL,			-- tenant's phone
  `email` varchar(50) DEFAULT NULL,			-- tenant's email
  `comments` varchar(50) DEFAULT NULL,
  `utm_x` int(11) DEFAULT NULL,
  `utm_y` int(11) DEFAULT NULL,
  `area_m2` float DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,  
   PRIMARY KEY (`id`) 
);

CREATE TABLE `balance_rents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `subdistrictid` int(7) NOT NULL,
  `zoneid` int(7) DEFAULT NULL, 
  `year` int(5) DEFAULT NULL,
  `owed` float DEFAULT NULL, 		-- rate, 100, 100
  `payed` float DEFAULT NULL, 		-- 50, 45, 0
  `fine` float DEFAULT NULL, 		-- 1%
  `balance` float DEFAULT NULL, 	-- 50, 5, 105.005
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

-- Investment
CREATE TABLE `investments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,    
  `subdistrictid` int(7) NOT NULL,    
  `investment_code` int(11) DEFAULT NULL,
  `investment_name` varchar(50) DEFAULT NULL, -- e.g. interests, dividends and equipment 
  `investment_value` float DEFAULT NULL,   
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `balance_investments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `investment_code` int(11) NOT NULL,
  `year` int(5) DEFAULT NULL,
  `owed` float DEFAULT NULL, 		-- rate, 100, 100
  `payed` float DEFAULT NULL, 		-- 50, 45, 0
  `fine` float DEFAULT NULL, 		-- 1%
  `balance` float DEFAULT NULL, 	-- 50, 5, 105.005
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
);
