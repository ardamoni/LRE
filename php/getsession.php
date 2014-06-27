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
	$qdistrictboundary	= 	mysql_query("SELECT `boundary` FROM `area_district` WHERE `districtid` = '".$_SESSION['user']['districtid']."'");
	$districtboundary	= 	mysql_fetch_array($qdistrictboundary);
	$qnumberOfParcels	= 	mysql_query("SELECT COUNT(distinct `upn`) as `numupn` FROM `KML_from_LUPMIS` WHERE `districtid` = '".$_SESSION['user']['districtid']."'");
	$numberOfParcels	= 	mysql_fetch_array($qnumberOfParcels);
	$qnumberOfProperty	= 	mysql_query("SELECT COUNT(`upn`) as `numupn` FROM `property` WHERE `districtid` = '".$_SESSION['user']['districtid']."'");
	$numberOfProperty	= 	mysql_fetch_array($qnumberOfProperty);
	$qnumberOfBusiness	= 	mysql_query("SELECT COUNT(`upn`) as `numupn` FROM `business` WHERE `districtid` = '".$_SESSION['user']['districtid']."'");
	$numberOfBusiness	= 	mysql_fetch_array($qnumberOfBusiness);
	$qsumPropertyBalance = 	mysql_query("SELECT SUM(d3.balance) as sumpropbal FROM property_balance d3 Where d3.`districtid`='130';");
	$sumPropertyBalance	 = 	mysql_fetch_array($qsumPropertyBalance);

	$json['districtboundary'] 		= $districtboundary['boundary'];
	$json['numberOfParcels'] 		= $numberOfParcels['numupn'];
	$json['numberOfProperty'] 		= $numberOfProperty['numupn'];
	$json['numberOfBusiness'] 		= $numberOfBusiness['numupn'];
	$json['sumPropertyBalance'] 		= $sumPropertyBalance['sumpropbal'];
	
	$data[] 			= $json;
	
header("Content-type: application/json");
echo json_encode($data);
?>