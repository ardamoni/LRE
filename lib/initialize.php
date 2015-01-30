<?php
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(-1);


// Define the core paths
// Define them as absolute paths to make sure that require_once works as expected

// DIRECTORY_SEPARATOR is a PHP pre-defined constant
// (\ for Windows, / for Unix)
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

defined('SITE_ROOT') ? null :
	define('SITE_ROOT', DS.'Applications'.DS.'MAMP'.DS.'htdocs'.DS.'gis'.DS.'OL212'.DS.'LRE');

defined('LIB_PATH') ? null : define('LIB_PATH', SITE_ROOT.DS.'lib');

// load config file first
require_once(LIB_PATH.DS.'config.php');
require_once(LIB_PATH.DS.'configuration.php');
// require_once(LIB_PATH.DS.'System.php');

// load basic functions next so that everything after can use them
require_once(LIB_PATH.DS.'functions.php');

// load core objects
//require_once(LIB_PATH.DS.'session.php');
require_once(LIB_PATH.DS.'database.php');

require_once(LIB_PATH.DS.'database_object.php');

require_once(LIB_PATH.DS.'feefixingClass.php');
require_once(LIB_PATH.DS.'feefixingBClass.php');

//require_once(LIB_PATH.DS."configuration.php");			// Configuration File
require_once(LIB_PATH.DS."User.php");					// User Functions

// load database-related classes
//require_once(LIB_PATH.DS.'user.php');

?>