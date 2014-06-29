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
	
		// collect the information to be displayed in the UI
	$qsumPropertyBalance = 	mysql_query("SELECT SUM(d3.balance) as sumpropbal FROM property_balance d3 JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` Where d3.`districtid`='".$_SESSION['user']['districtid']."';");
	$sumPropertyBalance	 = 	mysql_fetch_array($qsumPropertyBalance);
	$qsumPropertyPaid 	= 	mysql_query("SELECT SUM(d3.payment_value) as sumproppaid FROM property_payments d3 JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` Where d3.`districtid`='".$_SESSION['user']['districtid']."';");
	$sumPropertyPaid	= 	mysql_fetch_array($qsumPropertyPaid);
	$qsumPropertyDue 	= 	mysql_query("SELECT SUM(d3.feefi_value) as sumpropdue FROM property_due d3 JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` Where d3.`districtid`='".$_SESSION['user']['districtid']."';");
	$sumPropertyDue	 	= 	mysql_fetch_array($qsumPropertyDue);

	$json['sumPropertyBalance'] 	= $sumPropertyBalance['sumpropbal'];
	$json['sumPropertyPaid'] 		= $sumPropertyPaid['sumproppaid'];
	$json['sumPropertyDue'] 		= $sumPropertyDue['sumpropdue'];
	
	$data[] 			= $json;
	
header("Content-type: application/json");
echo json_encode($data);
?>