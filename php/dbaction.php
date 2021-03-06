<?php

//-------- loader ---------------------------------------------------------------------

session_start();

// DB connection
	require_once( "../lib/configuration.php"	);
	require_once( "../lib/System.php" );
	require_once( "../lib/Revenue.php"			);
//	require_once( "../lib/BusinessRevenueClass.php"			);
	require_once( "../lib/StatisticsClass.php"			);

	$System = new System;

	$year = $System->GetConfiguration("RevenueCollectionYear");

	$dbaction = $_POST['dbaction'];
	$clickfeature = $_POST['clickfeature'];
	$upn = $_POST['upn'];
	$sub = $_POST['sub'];
	$polygon = $_POST['polygon'];
	$collector = $_POST['collector'];
	$zonecolour = $_POST['zonecolour'];
	$zoneid = $_POST['zoneid'];
	$districtid = $_POST['districtid'];
	$searchupn = $_POST['searchupn'];
	$lpcz = $_POST['lpcz'];
	$propincz = $_POST['propincz'];
	$busincz = $_POST['busincz'];
	$starget = $_POST['starget'];
	$sString = $_POST['sString'];
	$searchlayer = $_POST['searchlayer'];
	$caller = $_POST['caller'];


//	$dbaction = $_GET['action'];
//	$clickfeature = $_GET['clickfeature'];
//	$sub = $_GET['sub'];

  if ($dbaction=='feedUPNinfo'){feedUPNinfo($dbaction,$clickfeature,$sub);}

  if ($dbaction=='feedBusinessinfo'){feedBusinessinfo($dbaction,$clickfeature,$sub);}

  if ($dbaction=='feedFeesFinesinfo'){feedFeesFinesinfo($clickfeature);}

  if ($dbaction=='getlocalplan'){getlocalplan($districtid);}

  if ($dbaction=='getproperty'){getproperty($districtid, $upn);}

  if ($dbaction=='getbusiness'){getbusiness($districtid, $upn);}

  if ($dbaction=='getfees'){getfees($districtid, $upn);}

  if ($dbaction=='getdistrictmap'){getdistrictmap();}

  if ($dbaction=='getregionmap'){getregionmap($year, $caller);}

  if ($dbaction=='insertCZ'){insertCZ($zoneid,$districtid,$polygon,$collector,$zonecolour);}

  if ($dbaction=='getCZ'){getCZ($districtid);}

  if ($dbaction=='deleteCZ'){deleteCZ($zoneid);}

  if ($dbaction=='searchupn'){searchupn($searchupn);}

  if ($dbaction=='updateCZinProp'){updateCZinProp($propincz,$busincz, $lpcz);}

  if ($dbaction=='feedColzoneInfo'){feedColzoneInfo($districtid,$clickfeature);}

  if ($dbaction=='searchOther'){searchOther($districtid, $starget, $sString, $searchlayer);}

//----------end of loader -------------------------------------------------------------------



//-----------------------------------------------------------------------------
		//function feedUPNinfo()
		//retrieves information according to the passed UPN
		//expects clickfeature and sub as $_POST parameters
//-----------------------------------------------------------------------------
function feedUPNinfo($dbaction,$clickfeature,$sub)
{
	$Data = new Revenue;

  	// upn
	$dataFromJS = $clickfeature;
	//echo 'inside feedUPNinfo '.$dbaction.' '.$clickfeature.' '.$sub;
	// sub==true indicates that the hand-over was done with CDATA content
	if( $sub == "true" )
	{
		$upn = strstr( $dataFromJS, 'UPN: ' );
		$upn = substr( $upn, 9, 13 );
	}
	else
	{
		$upn = $dataFromJS;
	}

	//we need the Current Year hence we query for ReveneuCollectionYear in sys_config
	$rsys_config = mysql_query("SELECT * 	FROM	`system_config`");
	$sys_config_content = array();
	//now we put the result into a multi dimensional array
	while ($rasys_config = mysql_fetch_assoc($rsys_config)) { //get the content of our query and store it in an array
		$sys_config_content[] = array ( $rasys_config['variable'] => $rasys_config['value'] );
	};

//	now get the corresponding value out of the multidimensional array
	foreach($sys_config_content as $temp) {
		foreach($temp as $key => $value) {
			if ($key == 'RevenueCollectionYear') {
				$currentYear = (int) $value;
			}
		}
	}


	$data = array();

	// match UPN
//	$query = mysql_query( "SELECT * FROM `property` WHERE `upn` = '".$upn."'" ); //$upn."' AND `year` = '2013' " );
	$query=mysql_query("SELECT d1.`id`, d1.`upn`, d1.`subupn`, d1.`owner`, d1.`streetname`, d1.`housenumber`, d1.`owner`,
							d1.`owneraddress`, d1.`owner_tel`, d1.`owner_email`, d2.`year`, d1.`pay_status`, d1.`property_use`,
							d2.`code`, d2.`class`, d2.`rate`, d1.`districtid`
							from `property` d1, `fee_fixing_property` d2
							WHERE d1.`upn` = '".$upn."'
							AND d1.`districtid`=d2.`districtid`
							AND d1.`property_use`=d2.`code`
							AND d2.`year`='".$currentYear."' ORDER BY d1.`upn`, length(d1.`subupn`), d1.`subupn`;");

	$count = mysql_num_rows($query);
	if ($count>0){
	while( $row = mysql_fetch_assoc( $query ) )
	{
		$json 						= array();

		$json['id'] 				= $row['id'];
		$json['upn'] 				= $row['upn'];
		$json['subupn'] 			= $row['subupn'];
		$json['year']	 			= $currentYear;
		$json['property_use']	 	= $row['property_use'];
		$json['rate']	 			= number_format( $row['rate'],2,'.','' );// $row['rate'];
 		$json['revenue_due'] 		= number_format( $Data->getBalanceInfo( $row['upn'], $row['subupn'], $row['districtid'], $currentYear, "property", "due" ),2,'.','' ); //$row['revenue_due'];
		// Arreas - the last years balance holds all the previous years arreas
		$json['arrears'] 			= number_format($Data->getBalanceInfo( $row['upn'], $row['subupn'], $row['districtid'], $currentYear-1, "property", "balance" ),2,'.','' );
 		$json['revenue_collected'] 	= number_format( $Data->getBalanceInfo( $row['upn'], $row['subupn'], $row['districtid'], $currentYear, "property", "paid" ),2,'.','' ); //$row['revenue_collected'];
 		$json['revenue_balance'] 	= number_format( $Data->getBalanceInfo( $row['upn'], $row['subupn'], $row['districtid'], $currentYear, "property", "balance" ),2,'.','' ); //$json['revenue_due']-$json['revenue_collected'];
 		$json['pay_status'] 		= ($json['revenue_balance'] <=0 ? 9 : 1);// this is an inline if condition //number_format( $row['pay_status'],0,'.','' );
		$json['streetname'] 		= $row['streetname'];
		$json['housenumber'] 		= $row['housenumber'];
		$json['owner'] 				= $row['owner'];
		$json['owneraddress'] 		= $row['owneraddress'];
		$json['owner_tel'] 			= $row['owner_tel'];
		$json['owner_email'] 		= $row['owner_email'];
		$json['business_name'] 		= 'property'; //this is the identifier for the handler to not display the business_name

		$data[] 					= $json;
		//echo $row["upn"];
	}
	}else{
		$query=mysql_query("SELECT d1.`id`, d1.`upn`, d1.`subupn`, d1.`owner`, d1.`streetname`, d1.`housenumber`, d1.`owner`,
							d1.`owneraddress`, d1.`owner_tel`, d1.`owner_email`,d1.`pay_status`, d1.`property_use`, d1.`districtid`
							from `property` d1
							WHERE d1.`upn` = '".$upn."';");

		while( $row = mysql_fetch_assoc( $query ) )
		{
		$json 						= array();
		$json['upn'] 				= $upn;
		$json['subupn'] 			= $row['subupn'];
		$json['rate']	 			= 'undefined';
		$json['property_use']		= $row['property_use'];
		$json['streetname'] 		= $row['streetname'];
		$json['housenumber'] 		= $row['housenumber'];
		$json['owner'] 				= $row['owner'];
		$json['owneraddress'] 		= $row['owneraddress'];
		$json['owner_tel'] 			= $row['owner_tel'];
		$json['owner_email'] 		= $row['owner_email'];
		$json['business_name'] 				= 'property'; //this is the identifier for the handler to not display the business_name
		$data[] 					= $json;
		}
	}

	header("Content-type: application/json");
	echo json_encode($data);
}

//-----------------------------------------------------------------------------
		//function feedBusinessinfo()
		//retrieves information according to the passed UPN
		//expects clickfeature and sub as $_POST parameters
//-----------------------------------------------------------------------------
function feedBusinessinfo($dbaction,$clickfeature,$sub)
{
//	require_once( "../lib/configuration.php"	);

	$Data = new Revenue;
	$System = new System;
	$currentYear = $System->GetConfiguration("RevenueCollectionYear");
  	// upn
	$dataFromJS = $clickfeature;
//echo 'inside feedUPNinfo '.$dbaction.' '.$clickfeature.' '.$sub;
	// sub==true indicates that the hand-over was done with CDATA content
	if( $sub == "true" )
	{
		$upn = strstr( $dataFromJS, 'UPN: ' );
		$upn = substr( $upn, 9, 13 );
	}
	else
	{
		$upn = $dataFromJS;
	}

	$data = array();

	// match UPN
//	$query = mysql_query( "SELECT * FROM business WHERE upn = '".$upn."'" );

// 	$statment = $pdo->query("SELECT d1.`id`, d1.`upn`, d1.`subupn`, d1.`owner`, d1.`streetname`, d1.`housenumber`, d1.`owner`,
// 							d1.`owneraddress`, d1.`owner_tel`, d1.`owner_email`, d1.`business_name`, d2.`year`, d1.`pay_status`, d1.`business_class`,
// 							d2.`code`, d2.`class`, d2.`rate`, d1.`districtid`, d3.`district_name`
// 							from `business` d1, `fee_fixing_business` d2, `area_district` d3
// 							WHERE d1.`upn` = '".$upn."'
// 							AND d1.`districtid`=d2.`districtid`
// 							AND d1.`business_class`=d2.`code`
// 							AND d2.`year`='".$currentYear."'
// 							AND d2.`districtid`=d3.`districtid`;");

	$query=mysql_query("SELECT d1.`id`, d1.`upn`, d1.`subupn`, d1.`owner`, d1.`streetname`, d1.`housenumber`, d1.`owner`,
							d1.`owneraddress`, d1.`owner_tel`, d1.`owner_email`, d1.`business_name`, d2.`year`, d1.`pay_status`, d1.`business_class`,
							d2.`code`, d2.`class`, d2.`rate`, d1.`districtid`
							from `business` d1, `fee_fixing_business` d2
							WHERE d1.`upn` = '".$upn."'
							AND d1.`districtid`=d2.`districtid`
							AND d1.`business_class`=d2.`code`
							AND d2.`year`='".$currentYear."' ORDER BY d1.`upn`, length(d1.`subupn`), d1.`subupn`;");

	$count = mysql_num_rows($query);
	if ($count>0){
 	while( $row = mysql_fetch_assoc( $query ) )
// 	while( $row = $statement->fetch(PDO::FETCH_BOTH))
	{
		$json 						= array();

		$json['id'] 				= $row['id'];
		$json['upn'] 				= $row['upn'];
		$json['subupn'] 			= $row['subupn'];
		$json['year']	 			= $currentYear;
		$json['rate']	 			= number_format( $row['rate'],2,'.','' );// $row['rate'];
 		$json['revenue_due'] 		= number_format( $Data->getBalanceInfo( $row['upn'], $row['subupn'], $row['districtid'], $currentYear, "business", "due" ),2,'.','' ); //$row['revenue_due'];
		// Arreas - the last years balance holds all the previous years arreas
		$json['arrears'] 			= number_format($Data->getBalanceInfo( $row['upn'], $row['subupn'], $row['districtid'], $currentYear-1, "business", "balance" ),2,'.','' );
 		$json['revenue_collected'] 	= number_format( $Data->getBalanceInfo( $row['upn'], $row['subupn'], $row['districtid'], $currentYear, "business", "paid" ),2,'.','' ); //$row['revenue_collected'];
 		$json['revenue_balance'] 	= number_format( $Data->getBalanceInfo( $row['upn'], $row['subupn'], $row['districtid'], $currentYear, "business", "balance" ),2,'.','' );
 		$json['pay_status'] 		= ($json['revenue_balance'] <=0 ? 9 : 1);// this is an inline if condition //number_format( $row['pay_status'],0,'.','' );

// 		$json['pay_status'] 		= $row['pay_status'];
// 		$json['revenue_due'] 		= $row['revenue_due'];
// 		$json['revenue_collected'] 	= $row['revenue_collected'];
// 		$json['revenue_balance'] 	= $row['revenue_balance'];
		$json['streetname'] 		= $row['streetname'];
		$json['housenumber'] 		= $row['housenumber'];
		$json['business_name'] 		= $row['business_name'];
		$json['owner'] 				= $row['owner'];
		$json['owneraddress'] 		= $row['owneraddress'];
		$json['owner_tel'] 			= $row['owner_tel'];
		$json['owner_email'] 		= $row['owner_email'];

		$data[] 					= $json;
		//echo $row["upn"];
	}
		}else{
		$query=mysql_query("SELECT d1.`id`, d1.`upn`, d1.`subupn`, d1.`owner`, d1.`streetname`, d1.`housenumber`, d1.`owner`,
							d1.`owneraddress`, d1.`owner_tel`, d1.`owner_email`, d1.`business_name`, d1.`pay_status`, d1.`business_class`,
							d1.`districtid`
							from `business` d1
							WHERE d1.`upn` = '".$upn."';");
	 	while( $row = mysql_fetch_assoc( $query ) )
	 	{
		$json 						= array();
		$json['upn'] 				= $upn;
		$json['streetname'] 		= $row['streetname'];
		$json['housenumber'] 		= $row['housenumber'];
		$json['business_name'] 		= $row['business_name'];
		$json['rate']	 			= 'undefined';
		$json['owner'] 				= $row['owner'];
		$json['owneraddress'] 		= $row['owneraddress'];
		$json['owner_tel'] 			= $row['owner_tel'];
		$json['owner_email'] 		= $row['owner_email'];
		$json['business_class'] 	= $row['business_class'];
		$data[] 					= $json;
		}
	}
	header("Content-type: application/json");
	echo json_encode($data);
}

//-----------------------------------------------------------------------------
		//function feedFeesFinesinfo()
		//retrieves information according to the passed UPN
		//expects clickfeature and sub as $_POST parameters
//-----------------------------------------------------------------------------
function feedFeesFinesinfo($clickfeature)
{
//	require_once( "../lib/configuration.php"	);

//	$Data = new Revenue;
	$System = new System;
	$currentYear = $System->GetConfiguration("RevenueCollectionYear");
  	// upn
	$upn = $clickfeature;

	$data = array();

	$query=mysql_query("SELECT fees_fines.upn, sum(fees_fines_payments.`payment_value`) AS totalFeesFines, fees_fines.address, fees_fines.colzone_id
						FROM fees_fines INNER JOIN fees_fines_payments ON fees_fines.upn = fees_fines_payments.upn
							WHERE fees_fines.`upn` = '".$upn."'
							AND fees_fines.`districtid`=fees_fines_payments.`districtid`
							AND YEAR(fees_fines_payments.`payment_date`) = '".$currentYear."' ORDER BY fees_fines.`upn`;");

	$count = mysql_num_rows($query);
	if ($count>0){
 	while( $row = mysql_fetch_assoc( $query ) )
// 	while( $row = $statement->fetch(PDO::FETCH_BOTH))
	{
		$json 						= array();

		$json['id'] 				= $row['id'];
		if ($row['upn']){
			$json['upn'] 				= $row['upn'];
		}else{
			$json['upn'] 				= $upn;
		}
		$json['year']	 			= $currentYear;
		if ($row['upn']){
			$json['address'] 			= $row['address'];
		}else{
			$json['address'] 			= 'n.a.';
		}
		if ($row['colzone_id']){
			$json['colzone_id'] 		= $row['colzone_id'];
		}else{
			$json['colzone_id'] 		= 'n.a.';
		}
		if ($row['totalFeesFines']){
			$json['totalFeesFines'] 	= $row['totalFeesFines'];
		}else{
			$json['totalFeesFines'] 	= 0;
		}

		$data[] 					= $json;
		//echo $row["upn"];
	}
	}else{
	}
	header("Content-type: application/json");
	echo json_encode($data);
} //end of feedFeesFinesinfo

//-----------------------------------------------------------------------------
				//function feedColzoneInfo()
				//collects information for the given collector zone
				//expects $_POST $districtid, $clickfeature
//-----------------------------------------------------------------------------
function feedColzoneInfo($districtid, $clickfeature)
{
//
	$System = new System;
	$currentYear = $System->GetConfiguration("RevenueCollectionYear");

	require_once( "../lib/configuration.php"	);

  	// upn
	$zoneid = $clickfeature;
//
	$data = array();
//
	$bind = array(
		":colzone_id" => $zoneid,
		":districtid" => $districtid,
		":year" => $currentYear
	);

		$conn = new PDO(cDsn, cUser, cPass);
		$stmt = $conn->prepare("	SELECT sum(d1.`balance`) as sumbalance, COUNT(*) as rowcount
							FROM `property_balance` d1
							JOIN `KML_from_LUPMIS` d3 ON d1.`upn` = d3.`upn`, `property` d2
							WHERE d1.`upn` = d2.`upn` and d1.`subupn` = d2.`subupn` and d1.`districtid` = :districtid
							and d1.`year`= :year and d2.`colzone_id` = :colzone_id;
						");
		if (!$stmt->execute($bind))
		  throw new Exception('[' . $stmt->errorCode() . ']: ' . $stmt->errorInfo());
		$count = $stmt->rowCount();
		$row = $stmt->fetch(PDO::FETCH_BOTH);
				$json 						= array();
				$json['revbalanceProp'] 				= $row['sumbalance'];
				$json['intersectedProp'] 				= $row['rowcount'];
//
		$stmt = $conn->prepare("	SELECT sum(d1.`balance`) as sumbalance, COUNT(*) as rowcount
							FROM `business_balance` d1
							JOIN `KML_from_LUPMIS` d3 ON d1.`upn` = d3.`upn`, `business` d2
							WHERE d1.`upn` = d2.`upn` and d1.`subupn` = d2.`subupn` and d1.`districtid` = :districtid
							and d1.`year`= :year and d2.`colzone_id` = :colzone_id;
						");
		if (!$stmt->execute($bind))
		  throw new Exception('[' . $stmt->errorCode() . ']: ' . $stmt->errorInfo());
		$count = $stmt->rowCount();
		$row = $stmt->fetch(PDO::FETCH_BOTH);
				$json['revbalanceBus'] 				= $row['sumbalance'];
				$json['intersectedBus'] 				= $row['rowcount'];
				$json['revbalanceTotal'] 				= $json['revbalanceBus']+$json['revbalanceProp'];

		$data[] 					= $json;
//
		header("Content-type: application/json");
		echo json_encode($data);
//
}

//-----------------------------------------------------------------------------
				//function getlocalplan()
				//collects polygon information
				//expects no $_POST parameters
//-----------------------------------------------------------------------------
function getlocalplan($districtid)
{
	// get the polygons out of the database
//	$run = "SELECT DISTINCT d1.UPN, d1.boundary, d1.id, d1.LUPMIS_color, d1.Address, d1.Landuse, d1.ParcelOf, d2.unit_planning from `KML_from_LUPMIS` d1, `property` d2 WHERE d1.`UPN` = d2.`upn` AND d1.`districtid`='".$districtid."' ORDER BY d1.UPN;";
	$run = "SELECT DISTINCT d1.UPN, d1.boundary, d1.id, d1.LUPMIS_color, d1.Address, d1.Landuse, d1.ParcelOf from `KML_from_LUPMIS` d1 WHERE d1.`districtid`='".$districtid."' ORDER BY d1.UPN;";
	$query = mysql_query($run);

	$data 				= array();
//$row = mysql_fetch_assoc($query);
	while ($row = mysql_fetch_assoc($query)) {
	$json 				= array();
	$json['id'] 		= $row['id'];
	$json['upn'] 		= $row['UPN'];
	$json['boundary'] 	= $row['boundary'];
	$json['LUPMIS_color'] 	= $row['LUPMIS_color'];
	$json['Address'] 	= $row['Address'];
	$json['Landuse'] 	= $row['Landuse'];
	$json['ParcelOf'] 	= $row['ParcelOf'];
	$json['unit_planning'] 	= $row['unit_planning'];

	$data[] 			= $json;
	 }//end while
	header("Content-type: application/json");
	echo json_encode($data);
}
//-----------------------------------------------------------------------------
				//function getproperty()
				//collects polygon information
				//expects no $_POST parameters
//-----------------------------------------------------------------------------
function getproperty($districtid, $upn)
{
	$Data = new Revenue;
	$System = new System;
	$currentYear = $System->GetConfiguration("RevenueCollectionYear");


	// get the polygons out of the database
	$subupn = "";
	if (!isset($upn)) {
	$run = "SELECT  d1.`id`, d1.`boundary`, d1.`UPN`, d1.`districtid`, d3.`subupn`, d3.`balance`
	FROM `property_balance` d3
	JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn`
	WHERE d1.`districtid`='".$districtid."' AND d1.`districtid`=d3.`districtid` AND d3.`year`='".$currentYear."';";
	}else{
	$run = "SELECT  d1.`id`, d1.`boundary`, d1.`UPN`, d3.`subupn`, d1.`districtid`, d3.`balance`
	FROM `property_balance` d3
	JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` WHERE d1.`upn`= '".$upn."' AND d1.`districtid`='".$districtid."'
	AND d1.`districtid`=d3.`districtid` AND d3.`year`='".$currentYear."'
	ORDER BY d3.`upn`, d3.`subupn`;";
	}
// 	$run = "SELECT DISTINCT d1.UPN, d1.boundary, d1.id, d2.subupn, d2.pay_status, d3.balance
// 			from `KML_from_LUPMIS` d1, property d2, property_balance d3 WHERE d1.`UPN` = d2.`upn` AND d3.`UPN` = d2.`upn` AND d1.`districtid`='".$districtid."';";
	$query = mysql_query($run);

	$data 				= array();

	$payStatus = 1;
	$payStatus9 = false;
	$payupn="";
	$balanceTotal=-99;

	while ($row = mysql_fetch_assoc($query)) {
		$json 				= array();
		if ($row['balance']>0){
				$payStatus=1;
			}else{
				$payStatus=9;
			}
		if (empty($row['subupn'])) {
			$payStatus = $payStatus;
			$payStatus9=false;
		} else {
			if ($payStatus==9){
				 $payStatus9=true;}

			if ($payStatus9){
//				$balanceTotal = $Data->getBalanceTotal( $row['UPN'], $row['districtid'], $currentYear, "property", "sumbalance");
				if($Data->getBalanceTotal( $row['UPN'], $row['districtid'], $currentYear, "property", "sumbalance") > 0)
				{
				 $payStatus = 5;} else {
				 $payStatus = 9;
				 }
			}
		}
		$payStatus9=false;
		$json['id'] 		= $row['id'];
		$json['upn'] 		= $row['UPN'];
		$json['boundary'] 	= $row['boundary'];
		$json['status'] 	= $payStatus; //$row['pay_status'];
		$json['revenue_balance'] 	= $row['balance'];
//		$json['balanceTotal'] 	= $Data->getBalanceTotal( $row['UPN'], $row['districtid'], $currentYear, "property", "sumbalance");
		$data[] 			= $json;
	 } //end while
	header("Content-type: application/json");
	echo json_encode($data);
}

//-----------------------------------------------------------------------------
				//function getbusiness()
				//collects polygon information
				//expects no $_POST parameters
//-----------------------------------------------------------------------------
function getbusiness($districtid, $upn)
{

	$Data = new Revenue;
	$System = new System;
	$currentYear = $System->GetConfiguration("RevenueCollectionYear");


	// get the polygons out of the database
	$subupn = "";
	if (!isset($upn)) {
	$run = "SELECT  d1.`id`, d1.`boundary`, d1.`UPN`, d3.`subupn`, d1.`districtid`, d3.`balance`
		FROM `business_balance` d3
		JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` WHERE d3.`districtid`='".$districtid."'
		AND d3.`year`='".$currentYear."';";
	}else{
		$run = "SELECT  d1.`id`, d1.`boundary`, d1.`UPN`, d3.`subupn`, d1.`districtid`, d3.`balance`
		FROM `business_balance` d3
		JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` WHERE d1.`upn`= '".$upn."' AND d3.`districtid`='".$districtid."'
		AND d3.`year`='".$currentYear."';";
	}
// 	$run = "SELECT DISTINCT d1.UPN, d1.boundary, d1.id, d2.subupn, d2.pay_status, d3.balance
// 			from `KML_from_LUPMIS` d1, business d2, business_balance d3 WHERE d1.`UPN` = d2.`upn` AND d3.`UPN` = d2.`upn` AND d1.`districtid`='".$districtid."';";

	$query = mysql_query($run);

	$data 				= array();

	$payStatus = 1;
	$payStatus9 = false;
	$payupn="";
	$balanceTotal=-99;


	while ($row = mysql_fetch_assoc($query)) {
	$json 				= array();
	if ($row['balance']>0){
			$payStatus=1;
		}else{
			$payStatus=9;
		}
	if (empty($row['subupn'])) {
//		$payStatus = $row['pay_status'];
		$payStatus = $payStatus;
		$payStatus9=false;
	} else {
//		if ($row['pay_status']==9){
		if ($payStatus==9){
			 $payStatus9=true;}

		if ($payStatus9){
			if($Data->getBalanceTotal( $row['UPN'], $row['districtid'], $currentYear, "business", "sumbalance") > 0)
			{
			 $payStatus = 5;} else {
			 $payStatus = 9;
			 }
		}
	}
	$payStatus9=false;
	$json['id'] 		= $row['id'];
	$json['upn'] 		= $row['UPN'];
	$json['boundary'] 	= $row['boundary'];
	$json['status'] 	= $payStatus; //$row['pay_status'];
	$json['revenue_balance'] 	= $row['balance'];
	$data[] 			= $json;
	 }//end while
	header("Content-type: application/json");
	echo json_encode($data);
}

//-----------------------------------------------------------------------------
				//function getfees()
				//collects polygon information
				//expects no $_POST parameters
//-----------------------------------------------------------------------------
function getfees($districtid, $upn)
{

	$Data = new Revenue;
	$System = new System;
	$currentYear = $System->GetConfiguration("RevenueCollectionYear");


	// get the polygons out of the database
	$subupn = "";
	if (!isset($upn)) {
		$run = "SELECT  d1.`id`, d1.`boundary`, d1.`UPN`,  d1.`districtid`, d1.`colzone_id`, d3.`address`
			FROM `fees_fines` d3
			JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` WHERE d3.`districtid`='".$districtid."';";
		}else{
		$run = "SELECT  d1.`id`, d1.`boundary`, d1.`UPN`,  d1.`districtid`, d1.`colzone_id`, d3.`address`
			FROM `fees_fines` d3
			JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` WHERE d1.`upn` = '".$upn."' WHERE d3.`districtid`='".$districtid."';";
		}

	$query = mysql_query($run);

	$data 				= array();

	while ($row = mysql_fetch_assoc($query)) {
	$json 				= array();
	$json['id'] 		= $row['id'];
	$json['upn'] 		= $row['UPN'];
	$json['boundary'] 	= $row['boundary'];
	$data[] 			= $json;
	 }//end while
	header("Content-type: application/json");
	echo json_encode($data);
}


//-----------------------------------------------------------------------------
				//function getdistrictmap()
				//collects polygon information
				//expects no $_POST parameters
//-----------------------------------------------------------------------------
function getdistrictmap()
{
	// get the polygons out of the database
//	$run = "SELECT * from `KML_from_districts`;";

	$run = "SELECT t1.`id`, t1.`boundary` ,t1.`districtname`, t2.`district_name`, t2.`districtid`, t2.`activestatus`
	from `KML_from_districts` t1, `area_district` t2 WHERE t1.`districtname`=t2.`district_name`;";
//	$run = "SELECT t2.`id`, t2.`boundary` , t2.`district_name`, t2.`activestatus` from `area_district` t2;";
//	$run = "SELECT * from `area_district`;";

	$query = mysql_query($run);
	$affectedrows = mysql_affected_rows();

	$data 				= array();

	while ($row = mysql_fetch_assoc($query)) {
	$json 				= array();
	$json['id'] 		= $row['id'];
	$json['boundary'] 	= $row['boundary'];
	$json['districtname'] 	= $row['district_name'];
	$json['districtid'] 	= $row['districtid'];
	$json['activestatus'] 	= $row['activestatus'];

	$data[] 			= $json;
	 }//end while
	header("Content-type: application/json");
 	echo json_encode($data);
}
//-----------------------------------------------------------------------------
				//function getregionmap()
				//collects polygon information
				//expects no $_POST parameters
//-----------------------------------------------------------------------------
function getregionmap($year, $caller)
{

	$Stats = new StatsData;

	// get the polygons out of the database
//	$run = "SELECT * from `KML_from_regions`;";
	$run = "SELECT * from `area_region`;";
	$query = mysql_query($run);

	$data 				= array();

	while ($row = mysql_fetch_assoc($query)) {
	$json 				= array();
	$json['id'] 		= $row['id'];
	$json['boundary'] 	= $row['boundary'];
	$json['regionname'] 	= $row['region_name'].' '.$caller;
	$json['regionid'] 	= $row['regionid'];

	if (!$caller) {
	$json['NrOfDistricts'] 	= $Stats->getNrDistricts( $row['regionid']);
	$json['TotalPropertyDue'] 	= number_format( $Stats->getTotalPropertyDueForRegion( $row['regionid'], $year),2,'.',',' );
	$json['TotalPropertyExpected'] 	= number_format($Stats->getTotalPropertyExpected(  $row['regionid'], $year),2,'.',',' );
	$json['TotalPropertyPayments'] 	= number_format($Stats->getTotalPropertyPayments(  $row['regionid'], $year),2,'.',',' );
	$json['TotalPropertyBalance'] 	= number_format($Stats->getTotalPropertyDueForRegion( $row['regionid'], $year)-$Stats->getTotalPropertyPayments(  $row['regionid'], $year),2,'.',',' );
	$json['TotalBusinessDue'] 	= number_format( $Stats->getTotalBusinessDueForRegion( $row['regionid'], $year),2,'.',',' );
	$json['TotalBusinessExpected'] 	= number_format($Stats->getTotalBusinessExpected(  $row['regionid'], $year),2,'.',',' );
	$json['TotalBusinessPayments'] 	= number_format($Stats->getTotalBusinessPayments(  $row['regionid'], $year),2,'.',',' );
// 	$json['TotalBusinessBalance'] 	= number_format($json['TotalBusinessExpected']-$json['TotalBusinessPayments'],2,'.',',' );
	$json['TotalBusinessBalance'] 	= number_format($Stats->getTotalBusinessDueForRegion( $row['regionid'], $year)-$Stats->getTotalBusinessPayments(  $row['regionid'], $year),2,'.',',' );
	}
	$data[] 			= $json;
	 }//end while
	header("Content-type: application/json");
	echo json_encode($data);
}
//-----------------------------------------------------------------------------
				//function insertCZ()
				//inserts or updates polygon information into table collectorzones
				//expects  zoneid, polygon, collector, zonecolour as $_POST parameters
//-----------------------------------------------------------------------------
function insertCZ($zoneid,$districtid,$polygon,$collector,$zonecolour)
{
   if (empty($zoneid)){
   	$data 				= array();

	// insert new collector zone
    $run = "INSERT INTO collectorzones (polygon, collectorid, zone_colour, districtid) VALUES ('".$polygon."', '".$collector."', '".$zonecolour."', '".$districtid."');";
	$query = mysql_query($run);

	$run = "SELECT * FROM collectorzones WHERE polygon = '".$polygon."';";
	$query = mysql_query($run);
	$data 				= array();
	$json 				= array();

 			while ($row = mysql_fetch_assoc($query)) {
			$json['text'] 			= 'New collector zone stored in database';
			$json['zoneid'] 		= $row['id'];
			$json['collectorid']	= $row['collectorid'];
			$json['districtid']	= $row['districtid'];
		}

    $data[] 			= $json;

   }else{
	// update existing collector zone

    $run = "UPDATE collectorzones SET polygon='".$polygon."', collectorid='".$collector."', zone_colour='".$zonecolour."', districtid='".$districtid."' WHERE id='".$zoneid."';";
	$query = mysql_query($run);

	$run = "SELECT * FROM collectorzones WHERE polygon = '".$polygon."';";
	$query = mysql_query($run);
	$data 				= array();
//   if (!empty($query)){
	$json 				= array();
		while ($row = mysql_fetch_assoc($query)) {
			$json['text'] 		= 'Collector zone was updated in the database';
			$json['zoneid'] 		= $row['id'];
			$json['collectorid']	= $row['collectorid'];
			$json['districtid']	= $row['districtid'];
		}

    $data[] 			= $json;
//	 }//end if
	}//end else
	header("Content-type: application/json");
	echo json_encode($data);
}

//-----------------------------------------------------------------------------
				//function getCZ()
				//collects the existing polygon information from table collectorzones
				//expects  districtid as $_POST parameters
//-----------------------------------------------------------------------------
function getCZ($districtid)
{
	$run = "SELECT * FROM collectorzones WHERE districtid = '".$districtid."';";
	$query = mysql_query($run);
	$data 				= array();
//   if (!empty($query)){
	$json 				= array();
		while ($row = mysql_fetch_assoc($query)) {
			$json['zoneid'] 				= $row['colzonenr'];
			$json['districtid'] 		= $row['districtid'];
			$json['polygon'] 			= $row['polygon'];
			$json['collectorid'] 		= $row['collectorid'];
			$json['zone_colour'] 		= $row['zone_colour'];

			$data[] 					= $json;
		}

//	 }//end if
//	}//end else
	header("Content-type: application/json");
	echo json_encode($data);
}
//-----------------------------------------------------------------------------
				//function deleteCZ()
				//deletes the record for the selected zoneid from table collectorzones
				//expects  zoneid as $_POST parameters
//-----------------------------------------------------------------------------
function deleteCZ($zoneid)
{
	$run = "DELETE FROM collectorzones WHERE id = '".$zoneid."' LIMIT 1;";

	$query = mysql_query($run);

	$affectedrows = (mysql_affected_rows($con)== 1) ? true : false;
	$data 				= array();
//   if (!empty($query)){
	$json 				= array();
			$json['deleted'] 				= $affectedrows;
			$data[] 					= $json;

	header("Content-type: application/json");
	echo json_encode($data);
}
//-----------------------------------------------------------------------------
				//function searchupn()
				//searches for a upn and returns
//-----------------------------------------------------------------------------
function searchupn($searchupn)
{
	$run = "SELECT * FROM collectorzones WHERE districtid = '".$districtid."';";
	$query = mysql_query($run);
	$data 				= array();
//   if (!empty($query)){
	$json 				= array();
		while ($row = mysql_fetch_assoc($query)) {
			$json['zoneid'] 				= $row['id'];
			$json['districtid'] 		= $row['districtid'];
			$json['polygon'] 			= $row['polygon'];
			$json['collectorid'] 		= $row['collectorid'];
			$json['zone_colour'] 		= $row['zone_colour'];

			$data[] 					= $json;
		}

//	 }//end if
//	}//end else
	header("Content-type: application/json");
	echo json_encode($data);
}

//-----------------------------------------------------------------------------
				//function searchOther()
				//searches for a street or a name and returns the found upns
//-----------------------------------------------------------------------------
function searchOther($districtid, $starget, $sString, $searchlayer)
{

//debug_to_console( "Test:".$starget.' '.$sString.' '.$searchlayer );

  if($searchlayer=='property'){
   if ($starget=='owner'){
	$run = "SELECT * FROM property WHERE districtid = '".$districtid."' AND `owner` LIKE '%".$sString."%';";
	}
   if ($starget=='street'){
	$run = "SELECT * FROM property WHERE districtid = '".$districtid."' AND `streetname` LIKE '%".$sString."%';";
	}
  }
  if($searchlayer=='business'){
   if ($starget=='owner'){
	$run = "SELECT * FROM business WHERE districtid = '".$districtid."' AND `owner` LIKE '%".$sString."%';";
	}
   if ($starget=='street'){
	$run = "SELECT * FROM business WHERE districtid = '".$districtid."' AND `streetname` LIKE '%".$sString."%';";
	}
  }
	$query = mysql_query($run);
	$data 				= array();
//   if (!empty($query)){
	$json 				= array();
		while ($row = mysql_fetch_assoc($query)) {
			$json['upn'] 				= $row['upn'];
			$json['owner'] 				= $row['owner'];
			$json['streetname'] 		= $row['streetname'];
			$data[] 					= $json;
		}

//	 }//end if
//	}//end else
	header("Content-type: application/json");
	echo json_encode($data);
}


//-----------------------------------------------------------------------------
				//function updateCZinProp()
				//updates the colzone_id in property and business to
				//propincz=Property in Collector Zone
				//busincz =Business in Collector Zone
//-----------------------------------------------------------------------------
function updateCZinProp($propincz, $busincz, $lpcz)
{

$json_decodedLP=json_decode($lpcz);
$json_decodedProp=json_decode($propincz);
$json_decodedBus=json_decode($busincz);
//var_dump($json_decoded);

$json 				= array();
$jsonBus 				= array();
$i = 1;
$j = 1;

//the data comes in as a multidimensional array, hence we need to go through the array to identify the values
foreach ($json_decodedLP as $key => $value)
{
    foreach ($value as $k => $val)
    {
    	if ($k=='upn'){
		$json['upn'] =  $val;
		}
    	if ($k=='colzone'){
		$json['colzone'] =  $val;
		}
//        echo "$k | $val <br />";
    }
if (!empty($lpcz)){
    $run = "UPDATE `KML_from_LUPMIS` SET `colzone_id`='".$json['colzone']."' WHERE upn='".$json['upn']."';";
//    $run = "UPDATE `business` SET `colzone_id`='".$json['colzone']."' WHERE upn='".$json['upn']."';";
	$query = mysql_query($run);
//	$json['message'] = 'Done!';
	}else{
		$json['messageLP'] = 'LP Empty!';
	}
	$json['reccountLP'] = $i;
	$json['messageLP'] = 'LP Done!';
	$data[] = $json;
	$i++;

}

$i = 1;

foreach ($json_decodedProp as $key => $value)
{
    foreach ($value as $k => $val)
    {
    	if ($k=='upn'){
		$json['upn'] =  $val;
		}
    	if ($k=='colzone'){
		$json['colzone'] =  $val;
		}
//        echo "$k | $val <br />";
    }
if (!empty($propincz)){
    $run = "UPDATE `property` SET `colzone_id`='".$json['colzone']."' WHERE upn='".$json['upn']."';";
//    $run = "UPDATE `business` SET `colzone_id`='".$json['colzone']."' WHERE upn='".$json['upn']."';";
	$query = mysql_query($run);
//	$json['message'] = 'Done!';
	}else{
		$json['messageProp'] = 'Property Empty!';
	}
	$json['reccountProp'] = $i;
	$json['messageProp'] = 'Property Done!';
	$data[] = $json;
	$i++;

}

foreach ($json_decodedBus as $key => $value)
{
    foreach ($value as $k => $val)
    {
    	if ($k=='upn'){
		$jsonBus['upn'] =  $val;
		}
    	if ($k=='colzone'){
		$jsonBus['colzone'] =  $val;
		}
//        echo "$k | $val <br />";
    }

if (!empty($busincz)){
    $run = "UPDATE `business` SET `colzone_id`='".$jsonBus['colzone']."' WHERE upn='".$jsonBus['upn']."';";
//    $run = "UPDATE `business` SET `colzone_id`='".$json['colzone']."' WHERE upn='".$json['upn']."';";
	$query = mysql_query($run);
//	$json['message'] = 'Done!';
	}else{
		$jsonBus['messageBus'] = 'Business Empty!';
	}
	$jsonBus['reccountProp'] = $i;
	$jsonBus['reccountBus'] = $j;
	$jsonBus['messageProp'] = 'Property Done with ';
	$jsonBus['messageBus'] = 'Business Done with ';
	$data[] = $jsonBus;
	$j++;

}

//	 }//end if
//	}//end else
	header("Content-type: application/json");
	echo json_encode($data);
}

//this is a helper function to get some info to be displayed within the console log
function debug_to_console( $data ) {

    if ( is_array( $data ) )
        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

    echo $output;
}
?>