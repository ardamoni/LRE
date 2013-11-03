<?php

	/*	
	 * 	this file is used to insert the revenue collection into tables
	 */

	// DB connection
	require_once( "../lib/configuration.php" );
	require_once( "../lib/PropertyDetails.php" );
	
	ob_start(); // prevent adding duplicate data with refresh (F5)
	session_start();
	$upn = '608-0615-0292';
//	$upn = '608-0615-0315';
//	$subupn = '608-0615-0315B';
	$subupn = '';
	
	$currentdate = getdate();
	$currentyear = $currentdate['year'];
	$districtid = '130';
	
	echo $currentyear;


	$Data = new propertyDetailsClass;
    $fromClass = $Data->getPInfo( $upn, $subupn, $currentyear, $districtid ) ;
    var_dump($fromClass);
    echo "<br><br>";
    echo $fromClass['owner'];
    
//    $fromClass = $Data->getPropertyInfo( $upn, $currentyear ) ;
//    var_dump($fromClass);
    
?>
