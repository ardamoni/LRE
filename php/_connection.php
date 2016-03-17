<?php
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(E_ALL);
	
	// test to see the changes
	// remove this lines, 
	// if history shows
	// Arben 
	// 31.07.13 13:45 clean-up Ekke
	
	// DB connection
	require_once( "../lib/configuration.php" );
	require_once( "../lib/System.php" );

	$System = new System;
	
	// upn
	$dataFromJS = $_POST['clickfeature'];

	// sub==true indicates that the hand-over was done with CDATA content
	if( $_POST['sub'] == "true" ) 
	{
		$upn = strstr( $dataFromJS, 'UPN: ' );
		$upn = substr( $upn, 9, 13 );
	}
	else
	{
		$upn = $dataFromJS;
	}
	
	$data = array();
	
	$year = $System->GetConfiguration("RevenueCollectionYear");
	
	echo $year.'<br>'.'<br>'.'<br>';
	
	// match UPN
// 	$query = mysql_query( "SELECT * FROM property WHERE upn = '".$upn."' AND year = '".$year."' ");	
	$query = mysql_query( "SELECT * FROM property WHERE year = '".$year."' ");	
	
	while( $row = mysql_fetch_assoc( $query ) ) 
	{
		$json 						= array();
		
		$json['id'] 				= $row['id'];
		$json['upn'] 				= $row['upn'];
		$json['subupn'] 			= $row['subupn'];
		$json['year'] 				= $row['year'];
		$json['pay_status'] 		= $row['pay_status'];
		$json['revenue_due'] 		= $row['revenue_due'];
		$json['revenue_collected'] 	= $row['revenue_collected'];
		$json['revenue_balance'] 	= $row['revenue_balance'];
		$json['streetname'] 		= $row['streetname'];
		$json['housenumber'] 		= $row['housenumber'];
		$json['owner'] 				= $row['owner'];
		$json['owneraddress'] 		= $row['owneraddress'];
		$json['owner_tel'] 			= $row['owner_tel'];
		$json['owner_email'] 		= $row['owner_email'];
		
		$data[] 					= $json;
		//echo $row["upn"];
	}
	
	header("Content-type: application/json");
	echo json_encode($data);
	
?>