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
	$json['userrole']			= $_SESSION['user']['roleid'];
	
		// collect the information to be displayed in the UI
	$qdistrictboundary	= 	mysql_query("SELECT `boundary` FROM `area_district` WHERE `districtid` = '".$_SESSION['user']['districtid']."'");
	$districtboundary	= 	mysql_fetch_array($qdistrictboundary);
	$qnumberOfParcels	= 	mysql_query("SELECT COUNT(distinct `upn`) as `numupn` FROM `KML_from_LUPMIS` WHERE `districtid` = '".$_SESSION['user']['districtid']."'");
	$numberOfParcels	= 	mysql_fetch_array($qnumberOfParcels);
	$qnumberOfProperty	= 	mysql_query("SELECT COUNT(`upn`) as `numupn` FROM `property` WHERE `districtid` = '".$_SESSION['user']['districtid']."'");
	$numberOfProperty	= 	mysql_fetch_array($qnumberOfProperty);
	$qnumberOfProperty_valued	= 	mysql_query("SELECT COUNT(`upn`) as `numupn` FROM `property` WHERE `districtid` = '".$_SESSION['user']['districtid']."' AND `value_prop`>0");
	$numberOfProperty_valued	= 	mysql_fetch_array($qnumberOfProperty_valued);
	$qnumberOfBusiness	= 	mysql_query("SELECT COUNT(`upn`) as `numupn` FROM `business` WHERE `districtid` = '".$_SESSION['user']['districtid']."'");
	$numberOfBusiness	= 	mysql_fetch_array($qnumberOfBusiness);
// 	$qsumPropertyBalance = 	mysql_query("SELECT SUM(d3.balance) as sumpropbal FROM property_balance d3 JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` Where d3.`districtid`='".$_SESSION['user']['districtid']."';");
// 	$sumPropertyBalance	 = 	mysql_fetch_array($qsumPropertyBalance);
// 	$qsumPropertyPaid 	= 	mysql_query("SELECT SUM(d3.payment_value) as sumproppaid FROM property_payments d3 JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` Where d3.`districtid`='".$_SESSION['user']['districtid']."';");
// 	$sumPropertyPaid	= 	mysql_fetch_array($qsumPropertyPaid);
// 	$qsumPropertyDue 	= 	mysql_query("SELECT SUM(d3.feefi_value) as sumpropdue FROM property_due d3 JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` Where d3.`districtid`='".$_SESSION['user']['districtid']."';");
// 	$sumPropertyDue	 	= 	mysql_fetch_array($qsumPropertyDue);

	$json['districtboundary'] 		= $districtboundary['boundary'];
	$json['numberOfParcels'] 		= $numberOfParcels['numupn'];
	$json['numberOfProperty'] 		= $numberOfProperty['numupn'];
	$json['numberOfProperty_valued'] 		= $numberOfProperty_valued['numupn'];
	$json['numberOfBusiness'] 		= $numberOfBusiness['numupn'];
// 	$json['sumPropertyBalance'] 	= $sumPropertyBalance['sumpropbal'];
// 	$json['sumPropertyPaid'] 		= $sumPropertyPaid['sumproppaid'];
// 	$json['sumPropertyDue'] 		= $sumPropertyDue['sumpropdue'];
	
	$data[] 			= $json;
	
header("Content-type: application/json");
echo json_encode($data);
?>