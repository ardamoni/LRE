-- phpMyAdmin SQL Dump
-- version 3.5.8.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 30, 2013 at 11:47 AM
-- Server version: 5.5.32
-- PHP Version: 5.5.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `revenue`
--

-- --------------------------------------------------------

--
-- Table structure for table `area_region`
--

DROP TABLE IF EXISTS `area_region`;
CREATE TABLE IF NOT EXISTS `area_region` (
  `id` int(3) NOT NULL AUTO_INCREMENT,		-- auto incremental
  `lang` int(1) DEFAULT NULL,				-- default NULL, for English
  `regionid` int(3) NOT NULL,				-- unique identifier for region
  `region_name` varchar(100) NOT NULL,	-- region name
  `activestatus` boolean DEFAULT NULL,		-- default NULL is active
  PRIMARY KEY (`id`)
  UNIQUE KEY `regionid` (`regionid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `area_district`
--

DROP TABLE IF EXISTS `area_district`;
CREATE TABLE IF NOT EXISTS `area_district` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `lang` int(1) DEFAULT NULL,
  `districtid` int(3) NOT NULL,
  `district_name` varchar(100) NOT NULL,
  `regionid` int(3) NOT NULL,
  `activestatus` boolean DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `districtid` (`districtid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `area_subdistrict`
--

DROP TABLE IF EXISTS `area_subdistrict`;
CREATE TABLE IF NOT EXISTS `area_subdistrict` (
  `id` int(7) NOT NULL,
  `lang` int(1) DEFAULT NULL,
  `subdistrictid` int(7) NOT NULL,
  `subdistrict_name` varchar(100) NOT NULL,
  `districtid` int(3) NOT NULL,
  `activestatus` boolean DEFAULT NULL,
  PRIMARY KEY (`id`)
  UNIQUE KEY `subdistrictid` (`subdistrictid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `area_zone`
--

DROP TABLE IF EXISTS `area_zone`;
CREATE TABLE IF NOT EXISTS `area_zone` (
  `id` int(7) NOT NULL,
  `lang` int(1) DEFAULT NULL,
  `zoneid` int(7) NOT NULL,
  `zone_name` varchar(100) NOT NULL,
  `subdistrictid` int(7) NOT NULL,
  `activestatus` boolean DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `zoneid` (`zoneid`)  
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `balance_business_property`
--

DROP TABLE IF EXISTS `balance_business_property`;
CREATE TABLE IF NOT EXISTS `balance_business_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `subsub` varchar(5) DEFAULT NULL,
  `business` boolean DEFAULT '0',		-- default '0' is business, 
  `subdistrictid` int(7) NOT NULL,
  `zoneid` int(7) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `owed` float DEFAULT NULL,
  `payed` float DEFAULT NULL,
  `fine` float DEFAULT NULL,
  `balance` float DEFAULT NULL, 
  `instalment` int(5) DEFAULT NULL,  
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `balance_investment`
--

DROP TABLE IF EXISTS `balance_investment`;
CREATE TABLE IF NOT EXISTS `balance_investment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `investmentid` int(11) NOT NULL,
  `year` int(5) DEFAULT NULL,
  `owed` float DEFAULT NULL,
  `payed` float DEFAULT NULL,
  `fine` float DEFAULT NULL,
  `balance` float DEFAULT NULL,
  `instalment` int(5) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `balance_land`
--

DROP TABLE IF EXISTS `balance_land`;
CREATE TABLE IF NOT EXISTS `balance_land` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `landid` varchar(50) NOT NULL,
  `year` int(5) DEFAULT NULL,
  `owed` float DEFAULT NULL,
  `payed` float DEFAULT NULL,
  `fine` float DEFAULT NULL,
  `balance` float DEFAULT NULL,  
  `instalment` int(5) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `balance_other`
--

DROP TABLE IF EXISTS `balance_other`;
CREATE TABLE IF NOT EXISTS `balance_other` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) DEFAULT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `subsub` varchar(5) DEFAULT NULL,
  `district_level` boolean DEFAULT NULL,
  `districtid` int(7) DEFAULT NULL,
  `subdistrictid` int(7) DEFAULT NULL,
  `zoneid` int(7) DEFAULT NULL,
  `code` int(11) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `owed` float DEFAULT NULL,
  `payed` float DEFAULT NULL,
  `fine` float DEFAULT NULL,
  `balance` float DEFAULT NULL,
  `instalment` int(5) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `balance_rent`
--

DROP TABLE IF EXISTS `balance_rent`;
CREATE TABLE IF NOT EXISTS `balance_rent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `subsub` varchar(5) DEFAULT NULL,
  `district_level` boolean DEFAULT NULL,
  `districtid` int(7) DEFAULT NULL,
  `subdistrictid` int(7) DEFAULT NULL,
  `zoneid` int(7) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `owed` float DEFAULT NULL,
  `payed` float DEFAULT NULL,
  `fine` float DEFAULT NULL,
  `balance` float DEFAULT NULL,
  `instalment` int(5) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `business`
--

DROP TABLE IF EXISTS `business`;
CREATE TABLE IF NOT EXISTS `business` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,				-- must match the property
  `subupn` varchar(20) DEFAULT NULL,  
  `subsub` varchar(5) DEFAULT NULL,
  `subdistrictid` int(7) NOT NULL,
  `streetname` varchar(50) DEFAULT NULL,
  `housenumber` varchar(30) DEFAULT NULL,
  `town` varchar(50) DEFAULT NULL,
  `locpl` varchar(30) DEFAULT NULL,
  `landmark1` varchar(50) DEFAULT NULL,
  `landmark2` varchar(50) DEFAULT NULL,
  `zoneid` int(7) DEFAULT NULL,
  `locality_code` varchar(15) DEFAULT NULL,
  `da_no` varchar(30) DEFAULT NULL,
  `ownerid` int(12) DEFAULT NULL,
  `business_certif` varchar(30) DEFAULT NULL,
  `business_name` varchar(50) DEFAULT NULL,
  `date_start` int(11) DEFAULT NULL,
  `date_end` int(11) DEFAULT NULL,
  `activestatus` tinyint(1) DEFAULT NULL,
  `business_class` int(11) DEFAULT NULL,		-- class code
  `business_type` int(11) DEFAULT NULL,			-- class type
  `employees` int(11) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `utm_x` int(11) DEFAULT NULL,
  `utm_y` int(11) DEFAULT NULL,
  `area_m2` float DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`upn`,`subupn`,`subsub`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fees_business`
--

DROP TABLE IF EXISTS `fees_business`;
CREATE TABLE IF NOT EXISTS `fees_business` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subdistrictid` int(7) NOT NULL,
  `zoneid` int(7) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `class_code` int(11) DEFAULT NULL,
  `class_title` varchar(100) DEFAULT NULL,
  `type_code` int(11) DEFAULT NULL,
  `type_title` varchar(100) DEFAULT NULL,
  `unit` varchar(30) DEFAULT NULL,  
  `assessed` int(11) DEFAULT NULL,
  `rate` float DEFAULT NULL,
  `rate_impost` float DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fees_other`
--

DROP TABLE IF EXISTS `fees_other`;
CREATE TABLE IF NOT EXISTS `fees_other` (
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
  `unit` varchar(30) DEFAULT NULL,					-- starting from daily
  `assessed` int(11) DEFAULT NULL,
  `rate` float DEFAULT NULL,
  `rate_impost` float DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fees_property`
--

DROP TABLE IF EXISTS `fees_property`;
CREATE TABLE IF NOT EXISTS `fees_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subdistrictid` int(7) NOT NULL,
  `zoneid` int(7) DEFAULT NULL,  
  `year` int(5) DEFAULT NULL,
  `code` int(5) DEFAULT NULL,
  `code_title` varchar(50) DEFAULT NULL,
  `prop_value` int(11) DEFAULT NULL,  
  `unit` varchar(30) DEFAULT NULL,			-- annually
  `assessed` int(11) DEFAULT NULL,
  `rate` float DEFAULT NULL,
  `rate_impost` float DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fees_land`
--

DROP TABLE IF EXISTS `fees_land`;
CREATE TABLE IF NOT EXISTS `fees_land` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `districtid` int(7) NOT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int(5) DEFAULT NULL,
  `code_title` varchar(50) DEFAULT NULL,
  `unit` varchar(30) DEFAULT NULL,			-- annually
  `assessed` int(11) DEFAULT NULL,
  `rate` float DEFAULT NULL,
  `rate_impost` float DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fees_investment`
--

DROP TABLE IF EXISTS `fees_investment`;
CREATE TABLE IF NOT EXISTS `fees_investment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `districtid` int(7) NOT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int(5) DEFAULT NULL,
  `code_title` varchar(50) DEFAULT NULL,
  `unit` varchar(30) DEFAULT NULL,			-- annually
  `assessed` int(11) DEFAULT NULL,
  `rate` float DEFAULT NULL,
  `rate_impost` float DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `fees_rent`
--

DROP TABLE IF EXISTS `fees_rent`;
CREATE TABLE IF NOT EXISTS `fees_rent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `districtid` int(7) NOT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int(5) DEFAULT NULL,
  `code_title` varchar(50) DEFAULT NULL,
  `unit` varchar(30) DEFAULT NULL,			-- annually
  `assessed` int(11) DEFAULT NULL,
  `rate` float DEFAULT NULL,
  `rate_impost` float DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



-- --------------------------------------------------------

--
-- Table structure for table `fines`
--

DROP TABLE IF EXISTS `fines`;
CREATE TABLE IF NOT EXISTS `fines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,		
  `subdistrictid` int(7) DEFAULT NULL,
  `zoneid` int(7) DEFAULT NULL,
  `year` int(5) DEFAULT NULL,
  `code` int(11) NOT NULL,
  `code_title` varchar(100) NOT NULL,
  `fine` float DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `investments`
--

DROP TABLE IF EXISTS `investments`;
CREATE TABLE IF NOT EXISTS `investments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `districtid` int(7) NOT NULL,
  `code` int(11) NOT NULL,
  `investment_name` varchar(50) DEFAULT NULL,
  `investment_value` float DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `lands`
--

DROP TABLE IF EXISTS `lands`;
CREATE TABLE IF NOT EXISTS `lands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `districtid` int(7) NOT NULL,
  `landid` varchar(50) NOT NULL, 
  `land_name` varchar(100) NOT NULL, 
  `contractid` varchar(50) NOT NULL, 
  `contract_name` varchar(100) NOT NULL, 
  `duration` varchar(50) NOT NULL,
  `date_start` int(11) DEFAULT NULL,
  `date_end` int(11) DEFAULT NULL,
  `activestatus` boolean NULL,				-- NULL is active
  `ownerid` int(12) NOT NULL,
  `tenantid` int(12) NOT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `utm_x` int(11) DEFAULT NULL,
  `utm_y` int(11) DEFAULT NULL,
  `area_m2` float DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `other`
--

DROP TABLE IF EXISTS `other`;
CREATE TABLE IF NOT EXISTS `other` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` int(11) NOT NULL,
  `code_name` varchar(100) NOT NULL, 
  `district` boolean DEFAULT NULL, 			-- default NULL district level
  `districtid` int(7) NOT NULL,
  `subdistrictid` int(7) DEFAULT NULL,
  `zoneid` int(7) DEFAULT NULL,
  `town` varchar(50) DEFAULT NULL,
  `locpl` varchar(30) DEFAULT NULL,
  `unit_planning` int(11) DEFAULT NULL,
  `locality_code` varchar(15) DEFAULT NULL,
  `da_no` varchar(30) DEFAULT NULL,
  `landmark1` varchar(50) DEFAULT NULL,
  `landmark2` varchar(50) DEFAULT NULL,
  `ownerid` int(12) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  `utm_x` int(11) DEFAULT NULL,
  `utm_y` int(11) DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `own_owners`
--

DROP TABLE IF EXISTS `own_owners`;
CREATE TABLE IF NOT EXISTS `own_owners` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `ownerid` int(12) NOT NULL,
  `type_code` int(2) NOT NULL,					-- individual, joint, private, company etc
  `type_title` varchar(100) DEFAULT NULL,		-- title of the code
  `name` varchar(100) NOT NULL,					-- owner name and last name
  `parent_name` varchar(100) DEFAULT NULL,
  `dob` date NOT NULL,							-- date of birth or date of establishment
  `subdistrictid` int(7) NOT NULL,
  `address1` varchar(50) DEFAULT NULL,
  `address2` varchar(50) DEFAULT NULL,
  `address3` varchar(50) DEFAULT NULL,
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


-- --------------------------------------------------------

--
-- Table structure for table `own_tenant`
--

DROP TABLE IF EXISTS `own_tenant`;
CREATE TABLE IF NOT EXISTS `own_tenant` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `tenantid` int(12) NOT NULL,
  `type_code` int(2) NOT NULL,					-- individual, joint, private, company etc
  `type_title` varchar(100) DEFAULT NULL,		-- title of the code
  `name` varchar(100) NOT NULL,					-- owner name and last name
  `parent_name` varchar(100) DEFAULT NULL,
  `dob` date NOT NULL,							-- date of birth or date of establishment
  `subdistrictid` int(7) NOT NULL,
  `address1` varchar(50) DEFAULT NULL,
  `address2` varchar(50) DEFAULT NULL,
  `address3` varchar(50) DEFAULT NULL,
  `tel1` varchar(15) DEFAULT NULL,
  `tel2` varchar(15) DEFAULT NULL,
  `tel3` varchar(15) DEFAULT NULL,
  `email1` varchar(50) DEFAULT NULL,
  `email2` varchar(50) DEFAULT NULL,
  `email3` varchar(50) DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
  UNIQUE KEY `tenantid` (`tenantid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `payments_business_property`
--

DROP TABLE IF EXISTS `payments_business_property`;
CREATE TABLE IF NOT EXISTS `payments_business_property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `business` boolean DEFAULT NULL,
  `subdistrictid` int(7) NOT NULL,
  `zoneid` int(7) DEFAULT NULL,
  `date_payment` date DEFAULT NULL,
  `payment` float DEFAULT NULL,
  `instalments` int(5) DEFAULT NULL,
  `instalment_order` int(5) DEFAULT NULL,
  `station_payment` varchar(20) DEFAULT NULL,
  `receipt_payment` varchar(30) DEFAULT NULL,
  `type_payment` varchar(30) DEFAULT NULL,
  `payer` varchar(50) DEFAULT NULL,
  `paid_for` varchar(20) DEFAULT NULL,  
  `username` varchar(30) DEFAULT NULL,
  `demand_notice_no` varchar(30) DEFAULT NULL,
  `demand_notice_sent` date DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `payments_other`
--

DROP TABLE IF EXISTS `payments_other`;
CREATE TABLE IF NOT EXISTS `payments_other` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) DEFAULT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `subdistrictid` int(7) DEFAULT NULL,
  `zoneid` int(7) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_value` float DEFAULT NULL,
  `instalments` int(5) DEFAULT NULL,
  `instalment_order` int(5) DEFAULT NULL,
  `station_payment` varchar(20) DEFAULT NULL,
  `receipt_payment` varchar(30) DEFAULT NULL,
  `type_payment` varchar(30) DEFAULT NULL,
  `payer` varchar(50) DEFAULT NULL,
  `paid_for` varchar(20) DEFAULT NULL,  
  `username` varchar(30) DEFAULT NULL,
  `demand_notice_no` varchar(30) DEFAULT NULL,
  `demand_notice_sent` date DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `payments_land`
--

DROP TABLE IF EXISTS `payments_land`;
CREATE TABLE IF NOT EXISTS `payments_land` (
  `id` int(11) NOT NULL AUTO_INCREMENT,  
  `districtid` int(7) DEFAULT NULL,  
  `landid` varchar(50) NOT NULL, 
  `payment_date` date DEFAULT NULL,
  `payment_value` float DEFAULT NULL,
  `instalments` int(5) DEFAULT NULL,
  `instalment_order` int(5) DEFAULT NULL,
  `station_payment` varchar(20) DEFAULT NULL,
  `receipt_payment` varchar(30) DEFAULT NULL,
  `type_payment` varchar(30) DEFAULT NULL,
  `payer` varchar(50) DEFAULT NULL,
  `paid_for` varchar(20) DEFAULT NULL,  
  `username` varchar(30) DEFAULT NULL,		-- colector
  `demand_notice_no` varchar(30) DEFAULT NULL,
  `demand_notice_sent` date DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `payments_other`
--

DROP TABLE IF EXISTS `payments_other`;
CREATE TABLE IF NOT EXISTS `payments_other` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) DEFAULT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `subdistrictid` int(7) DEFAULT NULL,
  `zoneid` int(7) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_value` float DEFAULT NULL,
  `instalments` int(5) DEFAULT NULL,
  `instalment_order` int(5) DEFAULT NULL,
  `station_payment` varchar(20) DEFAULT NULL,
  `receipt_payment` varchar(30) DEFAULT NULL,
  `type_payment` varchar(30) DEFAULT NULL,
  `payer` varchar(50) DEFAULT NULL,
  `paid_for` varchar(20) DEFAULT NULL,  
  `username` varchar(30) DEFAULT NULL,
  `demand_notice_no` varchar(30) DEFAULT NULL,
  `demand_notice_sent` date DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `payments_other`
--

DROP TABLE IF EXISTS `payments_other`;
CREATE TABLE IF NOT EXISTS `payments_other` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) DEFAULT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `subdistrictid` int(7) DEFAULT NULL,
  `zoneid` int(7) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_value` float DEFAULT NULL,
  `instalments` int(5) DEFAULT NULL,
  `instalment_order` int(5) DEFAULT NULL,
  `station_payment` varchar(20) DEFAULT NULL,
  `receipt_payment` varchar(30) DEFAULT NULL,
  `type_payment` varchar(30) DEFAULT NULL,
  `payer` varchar(50) DEFAULT NULL,
  `paid_for` varchar(20) DEFAULT NULL,  
  `username` varchar(30) DEFAULT NULL,
  `demand_notice_no` varchar(30) DEFAULT NULL,
  `demand_notice_sent` date DEFAULT NULL,
  `comments` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
-- --------------------------------------------------------

--
-- Table structure for table `property`
--

DROP TABLE IF EXISTS `property`;
CREATE TABLE IF NOT EXISTS `property` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `subsub` varchar(5) DEFAULT NULL,
  `subdistrictid` int(7) NOT NULL,
  `zoneid` int(7) DEFAULT NULL,
  `streetname` varchar(50) DEFAULT NULL,
  `housenumber` varchar(30) DEFAULT NULL,
  `floor` int(11) DEFAULT NULL,
  `doornumber` int(4) DEFAULT NULL,
  `rooms` int(11) DEFAULT NULL,
  `town` varchar(50) DEFAULT NULL,
  `locpl` varchar(30) DEFAULT NULL,
  `unit_planning` int(11) DEFAULT NULL,  
  `locality_code` varchar(15) DEFAULT NULL,
  `structurecode` int(11) DEFAULT NULL,
  `ownerid` int(12) DEFAULT NULL,
  `prop_code` int(5) DEFAULT NULL, 
  `prop_descriptor` varchar(50) DEFAULT NULL,
  `property_type` int(2) DEFAULT NULL,
  `property_type_title` varchar(50) DEFAULT NULL,
  `property_use` int(2) DEFAULT NULL,
  `property_use_title` varchar(50) DEFAULT NULL,
  `roofing_type` int(2) DEFAULT NULL,
  `roofing_type_title` varchar(50) DEFAULT NULL,
  `ownership_type` int(2) DEFAULT NULL,
  `ownership_type_title` varchar(50) DEFAULT NULL,
  `constr_material` int(2) DEFAULT NULL,
  `constr_material_title` varchar(50) DEFAULT NULL,
  `storeys` int(3) DEFAULT NULL,
  `persons` int(5) DEFAULT NULL,  
  `prop_value` int(11) DEFAULT NULL,
  `planningpermit` int(11) DEFAULT NULL,
  `planningpermit_no` varchar(30) DEFAULT NULL,
  `buildingpermit` int(11) DEFAULT NULL,
  `buildingpermit_no` varchar(30) DEFAULT NULL,
  `date_start` int(11) DEFAULT NULL,
  `date_end` int(11) DEFAULT NULL,
  `activestatus` boolean DEFAULT NULL,		-- default NULL is active
  `comments` varchar(50) DEFAULT NULL,
  `utm_x` int(11) DEFAULT NULL,
  `utm_y` int(11) DEFAULT NULL,
  `area_m2` float DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`upn`, `subupn`, `subsub`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `rent`
--

DROP TABLE IF EXISTS `rent`;
CREATE TABLE IF NOT EXISTS `rent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `upn` varchar(13) NOT NULL,
  `subupn` varchar(20) DEFAULT NULL,
  `subsub` varchar(5) DEFAULT NULL,
  `ownerid` int(12) DEFAULT NULL,
  `code` int(5) DEFAULT NULL,
  `subdistrictid` int(7) NOT NULL,
  `zoneid` int(7) DEFAULT NULL,
  `town` varchar(50) DEFAULT NULL,
  `locpl` varchar(30) DEFAULT NULL,
  `streetname` varchar(50) DEFAULT NULL,
  `housenumber` varchar(30) DEFAULT NULL,
  `landmark1` varchar(50) DEFAULT NULL,
  `landmark2` varchar(50) DEFAULT NULL,
  `ownerid` int(12) DEFAULT NULL,
  `tenantid` int(12) DEFAULT NULL,
  `date_start` int(11) DEFAULT NULL,
  `date_end` int(11) DEFAULT NULL,
  `activestatus` boolean DEFAULT NULL, 			-- default NULL is let (rented out)
  `comments` varchar(50) DEFAULT NULL,
  `utm_x` int(11) DEFAULT NULL,
  `utm_y` int(11) DEFAULT NULL,
  `area_m2` float DEFAULT NULL,
  `lastentry_person` varchar(10) DEFAULT NULL,
  `lastentry_date` date DEFAULT NULL,
  PRIMARY KEY (`upn`, `subupn`, `subsub`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `usr_role`
--

DROP TABLE IF EXISTS `usr_role`;
CREATE TABLE IF NOT EXISTS `usr_role` (  
  `id` int(3) NOT NULL,				
  `description` varchar(50) NOT NULL
  KEY `id` (`id`,`description`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `usr_users`
--

DROP TABLE IF EXISTS `usr_users`;
CREATE TABLE IF NOT EXISTS `usr_users` (
  `username` varchar(30) NOT NULL,
  `pass` varchar(30) NOT NULL DEFAULT '751ec45015a704a39dc403001c963e97',
  `masterpass` varchar(30) NOT NULL DEFAULT '751ec45015a704a39dc403001c963e97',
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

-- --------------------------------------------------------

--
-- Table structure for table `usr_user_district`
--

DROP TABLE IF EXISTS `usr_user_district`;
CREATE TABLE IF NOT EXISTS `usr_user_district` (
  `username` varchar(30) NOT NULL,
  `regionid` int(3) NOT NULL,
  `districtid` int(3) NOT NULL,
  KEY `username` (`username`,`districtid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `usr_user_subdistrict`
--

DROP TABLE IF EXISTS `usr_user_subdistrict`;
CREATE TABLE IF NOT EXISTS `usr_user_subdistrict` (
  `username` varchar(30) NOT NULL,
  `regionid` int(3) NOT NULL,
  `districtid` int(3) NOT NULL,
  `subdistrictid` int(7) NOT NULL,
  KEY `username` (`username`,`subdistrictid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `usr_user_logs`
--

DROP TABLE IF EXISTS `usr_user_logs`;
CREATE TABLE IF NOT EXISTS `usr_user_logs` (
  `id` int(7) NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL,
  `activity` varchar(5) NOT NULL,
  `time` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `usr_user_region`
--

DROP TABLE IF EXISTS `usr_user_region`;
CREATE TABLE IF NOT EXISTS `usr_user_region` (
  `username` varchar(30) NOT NULL,
  `regionid` int(3) NOT NULL,
  KEY `username` (`username`,`regionid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `usr_user_role`
--

DROP TABLE IF EXISTS `usr_user_role`;
CREATE TABLE IF NOT EXISTS `usr_user_role` (
  `username` varchar(30) NOT NULL,
  `roleid` int(3) NOT NULL,						
  KEY `username` (`username`,`roleid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
