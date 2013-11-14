<?php

	/*	
	 * 	this file is used to insert the revenue collection into tables
	 */

	// DB connection
	require_once( "../lib/configuration.php" );
	require_once( "../lib/BusinessDetails.php" );
	
	ob_start(); // prevent adding duplicate data with refresh (F5)
	session_start();
//	$upn = '608-0615-0292';
	$upn = '608-0615-0250';
	$subupn = '608-0615-0250A';
//	$subupn = '';
	
	$currentdate = getdate();
	$currentyear = $currentdate['year'];
	$districtid = '130';
	
	echo $currentyear;


	$Data = new businessDetailsClass;
	
    $fromClass = $Data->getBInfo( $upn, $subupn, $currentyear, $districtid ) ;
    var_dump($fromClass);
    echo "<br><br>";
    echo $fromClass['owner'];
    
//    $fromClass = $Data->getPropertyInfo( $upn, $currentyear ) ;
//    var_dump($fromClass);
    
?>