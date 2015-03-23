<?php
	session_start();

	// DB connection
//	require_once( "../lib/configuration.php"	);
	require_once( 	"../lib/System.php"			);

 	$System = new System;

 	$currentYear 	= $System->GetConfiguration("RevenueCollectionYear");


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
	$qsumPropertyDue_feefi 	= 	mysql_query("SELECT SUM(d3.feefi_value) as sumpropdue_feefi FROM property_due d3 JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` Where d3.`districtid`='".$_SESSION['user']['districtid']."' AND d3.rate_value=0 AND d3.year='".$currentYear."';");
	$sumPropertyDue_feefi	 	= 	mysql_fetch_array($qsumPropertyDue_feefi);
	$qsumPropertyDue_valued 	= 	mysql_query("SELECT SUM(d3.rate_value) as sumpropdue_valued FROM property_due d3 JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` Where d3.`districtid`='".$_SESSION['user']['districtid']."' AND d3.rate_value>0 AND d3.year='".$currentYear."';");
	$sumPropertyDue_valued	 	= 	mysql_fetch_array($qsumPropertyDue_valued);

	$qsumBusinessBalance = 	mysql_query("SELECT SUM(d3.balance) as sumbusbal FROM business_balance d3 JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` Where d3.`districtid`='".$_SESSION['user']['districtid']."';");
	$sumBusinessBalance	 = 	mysql_fetch_array($qsumBusinessBalance);
	$qsumBusinessPaid 	= 	mysql_query("SELECT SUM(d3.payment_value) as sumbuspaid FROM business_payments d3 JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` Where d3.`districtid`='".$_SESSION['user']['districtid']."';");
	$sumBusinessPaid	= 	mysql_fetch_array($qsumBusinessPaid);
	$qsumBusinessDue 	= 	mysql_query("SELECT SUM(d3.feefi_value) as sumbusdue FROM business_due d3 JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` Where d3.`districtid`='".$_SESSION['user']['districtid']."'AND d3.year='".$currentYear."';");
	$sumBusinessDue	 	= 	mysql_fetch_array($qsumBusinessDue);

	$json['sumPropertyBalance'] 	= $sumPropertyBalance['sumpropbal'];
	$json['sumPropertyPaid'] 		= $sumPropertyPaid['sumproppaid'];
	$json['sumPropertyDue'] 		= $sumPropertyDue_feefi['sumpropdue_feefi']+$sumPropertyDue_valued['sumpropdue_valued'];

	$json['sumBusinessBalance'] 	= $sumBusinessBalance['sumbusbal'];
	$json['sumBusinessPaid'] 		= $sumBusinessPaid['sumbuspaid'];
	$json['sumBusinessDue'] 		= $sumBusinessDue['sumbusdue'];
	$json['currentYear'] 			= $currentYear;

	$data[] 			= $json;

header("Content-type: application/json");
echo json_encode($data);
?>