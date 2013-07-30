<?php

	// DB connection
	require_once( "configuration.php"	);

	// upn
	$dataFromJS = $_POST['clickfeature'];
	//$clickupn=$_GET['clickfeature'];
	//$clickupn='574-0600-1620';

	// subupn
	if( $_POST['sub'] == "true" ) 
	{
		$upn = strstr( $dataFromJS, 'UPN: ' );
		$upn = substr( $upn, 9, 13 );
	}
	else
	{
		$upn = $dataFromJS;
	}
	//echo $clickupn.": clickupn<br>".$upn.": upn<br>".$dataFromJS.": data<br>";

	
	$data = array();
	
	// match UPN
	$query = mysql_query( "SELECT * FROM property WHERE upn = '".$upn."'" );	
	//echo $query.": query<br>";
	
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