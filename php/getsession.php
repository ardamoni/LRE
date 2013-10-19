<?php
	// DB connection
	require_once( "../lib/configuration.php"	);

session_start();

if (isset($_SESSION['user']['name']))
	$_SESSION['user']['name']=$_SESSION['user']['name'];
else
	$_SESSION['user']['name']=$_SESSION['user']['user'];

	$data 						= array();
	$json 						= array();

	$json['username'] 				= $_SESSION['user']['name'];
	$json['userdistrict'] 		= $_SESSION['user']['districtid'];
	$json['userdistrictname'] 		= $_SESSION['user']['districtname'];
	
		// user district name
	$qdistrictboundary	= 	mysql_query("SELECT `boundary` FROM `area_district` WHERE `districtid` = '".$_SESSION['user']['districtid']."'");
	$districtboundary	= 	mysql_fetch_array($qdistrictboundary);

	$json['districtboundary'] 		= $districtboundary['boundary'];
	
	$data[] 			= $json;
	
header("Content-type: application/json");
echo json_encode($data);
?>