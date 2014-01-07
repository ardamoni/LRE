<?php

//-------- loader ---------------------------------------------------------------------

	// DB connection
	require_once( "../lib/configuration.php"	);
	require_once( "../lib/System.php" );

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
	
//	$dbaction = $_GET['action'];
//	$clickfeature = $_GET['clickfeature'];
//	$sub = $_GET['sub'];

  if ($dbaction=='feedUPNinfo'){feedUPNinfo($dbaction,$clickfeature,$sub);}

  if ($dbaction=='feedBusinessinfo'){feedBusinessinfo($dbaction,$clickfeature,$sub);}

  if ($dbaction=='getlocalplan'){getlocalplan($districtid);}

  if ($dbaction=='getproperty'){getpropertypoly($districtid);}

  if ($dbaction=='getbusiness'){getbusiness($districtid);}

  if ($dbaction=='getdistrictmap'){getdistrictmap();}

  if ($dbaction=='insertCZ'){insertCZ($zoneid,$districtid,$polygon,$collector,$zonecolour);}

  if ($dbaction=='getCZ'){getCZ($districtid);}

  if ($dbaction=='deleteCZ'){deleteCZ($zoneid);}

  if ($dbaction=='searchupn'){searchupn($searchupn);}

//----------end of loader -------------------------------------------------------------------



//-----------------------------------------------------------------------------
		//function feedUPNinfo() 
		//retrieves information according to the passed UPN
		//expects clickfeature and sub as $_POST parameters
//-----------------------------------------------------------------------------
function feedUPNinfo($dbaction,$clickfeature,$sub)
{
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
	$query = mysql_query( "SELECT * FROM `property` WHERE `upn` = '".$upn."'" ); //$upn."' AND `year` = '2013' " );	
	
	while( $row = mysql_fetch_assoc( $query ) ) 
	{		
		$json 						= array();
		
		$json['id'] 				= $row['id'];
		$json['upn'] 				= $row['upn'];
		$json['subupn'] 			= $row['subupn'];
		$json['year']	 			= $row['year'];
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
	$query = mysql_query( "SELECT * FROM business WHERE upn = '".$upn."'" );	
	
	while( $row = mysql_fetch_assoc( $query ) ) 
	{
		$json 						= array();
		
		$json['id'] 				= $row['id'];
		$json['upn'] 				= $row['upn'];
		$json['subupn'] 			= $row['subupn'];
		$json['pay_status'] 		= $row['pay_status'];
		$json['revenue_due'] 		= $row['revenue_due'];
		$json['revenue_collected'] 	= $row['revenue_collected'];
		$json['revenue_balance'] 	= $row['revenue_balance'];
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
	$run = "SELECT DISTINCT d1.UPN, d1.boundary, d1.id, d1.LUPMIS_color, d1.Address, d1.Landuse, d1.ParcelOf, d2.unit_planning from `KML_from_LUPMIS` d1, `property` d2 WHERE d1.`UPN` = d2.`upn` AND d1.`districtid`='".$districtid."' ORDER BY d1.UPN;";
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
	// get the polygons out of the database 
	$run = "SELECT d1.UPN, d1.boundary, d1.id, d2.pay_status, d2.revenue_balance from `KML_from_LUPMIS` d1, property d2 WHERE d1.`UPN` = d2.`upn` AND d1.`districtid`='".$districtid."';";
	$query = mysql_query($run);

	$data 				= array();

	while ($row = mysql_fetch_assoc($query)) {
	$json 				= array();
	$json['id'] 		= $row['id'];
	$json['upn'] 		= $row['UPN'];
	$json['boundary'] 	= $row['boundary'];
	$json['status'] 	= $row['pay_status'];
	$json['revenue_balance'] 	= $row['revenue_balance'];
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
	// get the polygons out of the database 
	$run = "SELECT d1.UPN, d1.boundary, d1.id, d2.pay_status, d2.revenue_balance from `KML_from_LUPMIS` d1, business d2 WHERE d1.`UPN` = d2.`upn` AND d1.`districtid`='".$districtid."';";
	$query = mysql_query($run);

	$data 				= array();

	while ($row = mysql_fetch_assoc($query)) {
	$json 				= array();
	$json['id'] 		= $row['id'];
	$json['upn'] 		= $row['UPN'];
	$json['boundary'] 	= $row['boundary'];
	$json['status'] 	= $row['pay_status'];
	$json['revenue_balance'] 	= $row['revenue_balance'];
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
	$run = "SELECT * from `KML_from_districts`;";
	$query = mysql_query($run);

	$data 				= array();

	while ($row = mysql_fetch_assoc($query)) {
	$json 				= array();
	$json['id'] 		= $row['id'];
	$json['boundary'] 	= $row['boundary'];
	$json['districtname'] 	= $row['districtname'];
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

?>