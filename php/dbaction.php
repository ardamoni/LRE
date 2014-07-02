<?php

//-------- loader ---------------------------------------------------------------------

	// DB connection
	require_once( "../lib/configuration.php"	);
	require_once( "../lib/System.php" );
	require_once( "../lib/Revenue.php"			);
	require_once( "../lib/BusinessRevenueClass.php"			);
	require_once( "../lib/StatisticsClass.php"			);

	$System = new System;
	
	$year = $System->GetConfiguration("RevenueCollectionYear");
	
	$dbaction = $_POST['dbaction'];
	$clickfeature = $_POST['clickfeature'];
	$sub = $_POST['sub'];
	$polygon = $_POST['polygon'];
	$collector = $_POST['collector'];
	$zonecolour = $_POST['zonecolour'];
	$zoneid = $_POST['zoneid'];
	$districtid = $_POST['districtid'];
	$searchupn = $_POST['searchupn'];
	$propincz = $_POST['propincz'];	
	$busincz = $_POST['busincz'];	

//	$dbaction = $_GET['action'];
//	$clickfeature = $_GET['clickfeature'];
//	$sub = $_GET['sub'];

  if ($dbaction=='feedUPNinfo'){feedUPNinfo($dbaction,$clickfeature,$sub);}

  if ($dbaction=='feedBusinessinfo'){feedBusinessinfo($dbaction,$clickfeature,$sub);}

  if ($dbaction=='getlocalplan'){getlocalplan($districtid);}

  if ($dbaction=='getproperty'){getpropertypoly($districtid);}

  if ($dbaction=='getbusiness'){getbusiness($districtid);}

  if ($dbaction=='getdistrictmap'){getdistrictmap();}

  if ($dbaction=='getregionmap'){getregionmap($year);}

  if ($dbaction=='insertCZ'){insertCZ($zoneid,$districtid,$polygon,$collector,$zonecolour);}

  if ($dbaction=='getCZ'){getCZ($districtid);}

  if ($dbaction=='deleteCZ'){deleteCZ($zoneid);}

  if ($dbaction=='searchupn'){searchupn($searchupn);}
  
  if ($dbaction=='updateCZinProp'){updateCZinProp($propincz,$busincz);}

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
							d2.`code`, d2.`class`, d2.`rate`, d1.`districtid`, d3.`district_name` 
							from `property` d1, `fee_fixing_property` d2, `area_district` d3 
							WHERE d1.`upn` = '".$upn."' 
							AND d1.`districtid`=d2.`districtid` 
							AND d1.`property_use`=d2.`code` 
							AND d2.`year`='".$currentYear."' 
							AND d2.`districtid`=d3.`districtid`;");	
							
	while( $row = mysql_fetch_assoc( $query ) ) 
	{		
		$json 						= array();
		
		$json['id'] 				= $row['id'];
		$json['upn'] 				= $row['upn'];
		$json['subupn'] 			= $row['subupn'];
		$json['year']	 			= $row['year'];
		$json['property_use']	 	= $row['property_use'];
		$json['rate']	 			= number_format( $row['rate'],2,'.','' );// $row['rate'];
 		$json['pay_status'] 		= number_format( $row['pay_status'],0,'.','' );
 		$json['revenue_due'] 		= number_format( $Data->getAnnualDueSum( $row['upn'], $row['subupn'], $currentYear ),2,'.','' ); //$row['revenue_due'];
 		$json['revenue_collected'] 	= number_format( $Data->getAnnualPaymentSum( $row['upn'], $row['subupn'], $currentYear ),2,'.','' ); //$row['revenue_collected'];
// 		$json['revenue_balance'] 	= $json['revenue_due']-$json['revenue_collected'];
 		$json['revenue_balance'] 	= number_format( $Data->getAnnualBalance( $row['upn'], $row['subupn'], $currentYear ),2,'.','' ); //$json['revenue_due']-$json['revenue_collected'];
		$json['streetname'] 		= $row['streetname'];
		$json['housenumber'] 		= $row['housenumber'];
		$json['owner'] 				= $row['owner'];
		$json['owneraddress'] 		= $row['owneraddress'];
		$json['owner_tel'] 			= $row['owner_tel'];
		$json['owner_email'] 		= $row['owner_email'];
		$json['business_name'] 				= 'property'; //this is the identifier for the handler to not display the business_name

		$data[] 					= $json;
		//echo $row["upn"];
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

	$Data = new BusinessRevenue;
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
							d2.`code`, d2.`class`, d2.`rate`, d1.`districtid`, d3.`district_name` 
							from `business` d1, `fee_fixing_business` d2, `area_district` d3 
							WHERE d1.`upn` = '".$upn."' 
							AND d1.`districtid`=d2.`districtid` 
							AND d1.`business_class`=d2.`code` 
							AND d2.`year`='".$currentYear."' 
							AND d2.`districtid`=d3.`districtid`;");	

 	while( $row = mysql_fetch_assoc( $query ) ) 	
// 	while( $row = $statement->fetch(PDO::FETCH_BOTH)) 
	{
		$json 						= array();
		
		$json['id'] 				= $row['id'];
		$json['upn'] 				= $row['upn'];
		$json['subupn'] 			= $row['subupn'];
		$json['rate']	 			= number_format( $row['rate'],2,'.','' );// $row['rate'];
 		$json['pay_status'] 		= number_format( $row['pay_status'],0,'.','' );
 		$json['revenue_due'] 		= number_format( $Data->getAnnualDueSum( $row['upn'], $row['subupn'], $currentYear ),2,'.','' ); //$row['revenue_due'];
 		$json['revenue_collected'] 	= number_format( $Data->getAnnualPaymentSum( $row['upn'], $row['subupn'], $currentYear ),2,'.','' ); //$row['revenue_collected'];
// 		$json['revenue_balance'] 	= $json['revenue_due']-$json['revenue_collected'];
 		$json['revenue_balance'] 	= number_format( $Data->getAnnualBalance( $row['upn'], $row['subupn'], $currentYear ),2,'.','' ); //$json['revenue_due']-$json['revenue_collected'];
		
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
	
	header("Content-type: application/json");
	echo json_encode($data);
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
				//function getpropertypoly() 
				//collects polygon information 
				//expects no $_POST parameters
//-----------------------------------------------------------------------------
function getpropertypoly($districtid) 
{
	$Data = new Revenue;

	// get the polygons out of the database 
	$subupn = "";
	$run = "SELECT  d1.`id`, d1.`boundary`, d1.`UPN`, d3.`subupn`, d3.`balance`
	FROM `property_balance` d3
	JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` AND d1.`districtid`='".$districtid."' AND d1.`districtid`=d3.`districtid`;";

// 	$run = "SELECT DISTINCT d1.UPN, d1.boundary, d1.id, d2.subupn, d2.pay_status, d3.balance 
// 			from `KML_from_LUPMIS` d1, property d2, property_balance d3 WHERE d1.`UPN` = d2.`upn` AND d3.`UPN` = d2.`upn` AND d1.`districtid`='".$districtid."';";
	$query = mysql_query($run);

	$data 				= array();

	$payStatus = 1;
	$payStatus9 = false;
	$payupn="";

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
			 $payStatus = 5;} else {
			 $payStatus = 1;}
	}
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
				//function getbusiness() 
				//collects polygon information 
				//expects no $_POST parameters
//-----------------------------------------------------------------------------
function getbusiness($districtid) 
{
//	$Data = new BusinessRevenue;

	// get the polygons out of the database 
	$subupn = "";
	$run = "SELECT  d1.`id`, d1.`boundary`, d1.`UPN`, d3.`subupn`, d3.`balance`
		FROM `business_balance` d3
		JOIN `KML_from_LUPMIS` d1 ON d3.`upn` = d1.`upn` WHERE d3.`districtid`='".$districtid."';";

// 	$run = "SELECT DISTINCT d1.UPN, d1.boundary, d1.id, d2.subupn, d2.pay_status, d3.balance 
// 			from `KML_from_LUPMIS` d1, business d2, business_balance d3 WHERE d1.`UPN` = d2.`upn` AND d3.`UPN` = d2.`upn` AND d1.`districtid`='".$districtid."';";

	$query = mysql_query($run);

	$data 				= array();

	$payStatus = 1;
	$payStatus9 = false;
	$payupn="";

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
			 $payStatus = 5;} else {
			 $payStatus = 1;}
	}
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
				//function getdistrictmap() 
				//collects polygon information 
				//expects no $_POST parameters
//-----------------------------------------------------------------------------
function getdistrictmap() 
{
	// get the polygons out of the database 
//	$run = "SELECT * from `KML_from_districts`;";
	
	$run = "SELECT t1.`id`, t1.`boundary` ,t1.`districtname`, t2.`district_name`, t2.`activestatus` from `KML_from_districts` t1, `area_district` t2 WHERE t1.`districtname`=t2.`district_name`;";
	$query = mysql_query($run);

	$data 				= array();

	while ($row = mysql_fetch_assoc($query)) {
	$json 				= array();
	$json['id'] 		= $row['id'];
	$json['boundary'] 	= $row['boundary'];
	$json['districtname'] 	= $row['districtname'];
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
function getregionmap($year) 
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
	$json['regionname'] 	= $row['region_name'];
	$json['regionid'] 	= $row['regionid'];
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
				//function updateCZinProp() 
				//updates the colzone_id in property to 
//-----------------------------------------------------------------------------
function updateCZinProp($propincz) 
{

$json_decoded=json_decode($propincz);
$json_decodedBus=json_decode($busincz);
//var_dump($json_decoded);

$json 				= array();
$jsonBus 				= array();

//the data comes in as a multidimensional array, hence we need to go through the array to identify the values
foreach ($json_decoded as $key => $value)
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

if (!empty($propincz)){
    $run = "UPDATE `property` SET `colzone_id`='".$json['colzone']."' WHERE upn='".$json['upn']."';";
//    $run = "UPDATE `business` SET `colzone_id`='".$json['colzone']."' WHERE upn='".$json['upn']."';";
	$query = mysql_query($run);  
//	$json['message'] = 'Done!';
	}else{
		$json['message'] = 'Property Empty!';
	}
	$json['message'] = 'Property Done!';
	$data[] = $json;	

}
if (!empty($busincz)){
    $run = "UPDATE `business` SET `colzone_id`='".$jsonBus['colzone']."' WHERE upn='".$jsonBus['upn']."';";
//    $run = "UPDATE `business` SET `colzone_id`='".$json['colzone']."' WHERE upn='".$json['upn']."';";
	$query = mysql_query($run);  
//	$json['message'] = 'Done!';
	}else{
		$json['message'] = 'Business Empty!';
	}
	$json['message'] = 'Business Done!';
	$data[] = $json;	

}

//	 }//end if
//	}//end else 
	header("Content-type: application/json");
	echo json_encode($data);
}

?>