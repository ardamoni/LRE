<?php

	// DB connection
	require_once( "../lib/configuration.php"	);


// get the polygons out of the database 
$run = "SELECT DISTINCT d1.UPN, d1.boundary, d1.id, d2.pay_status from `KML_from_LUPMIS` d1, property d2 WHERE d1.`UPN` = d2.`upn`;";
$query = mysql_query($run);

$data 				= array();

while ($row = mysql_fetch_assoc($query)) {
$json 				= array();
$json['id'] 		= $row['id'];
$json['upn'] 		= $row['UPN'];
$json['boundary'] 	= $row['boundary'];
$json['status'] 	= $row['pay_status'];
$data[] 			= $json;
 }//end while
header("Content-type: application/json");
echo json_encode($data);
mysql_close($con);
?>