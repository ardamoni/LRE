<?php

	/*	
	 * 	this file is used to insert the revenue collection into tables
	 */

	// DB connection
	require_once( "../lib/configuration.php" );
	
	echo "start: ", "<br>";
	
	$q1 = mysql_query("SELECT * from `payments_property` WHERE `id` > 1");
		
	$i=1;
	while($BOR = mysql_fetch_array($q1))
	{
		mysql_query("INSERT INTO `property_payments` (	`id`,
														`upn`, 
														`subupn`,
														`payment_date`,
														`payment_value`,
														`station_payment`,
														`receipt_payment`,
														`type_payment`,
														`payer`,
														`paid_for`
														) 
											VALUES 	( 	NULL,
														'".$BOR['upn']."',
														'".$BOR['subupn']."',
														'".$BOR['date_payment']."',
														'".$BOR['payment']."',
														'".$BOR['station_payment']."',
														'".$BOR['receipt_payment']."',
														'".$BOR['type_payment']."',
														'".$BOR['payer']."',
														'".$BOR['paid_for']."'
													)");
		echo $i, ": ", $BOR['upn'], " & ", $BOR['subupn'], "<br>";
		$i++;
	}
	
	// update empty columns
	mysql_query( "UPDATE `property_payments` SET `districtid` = '1840'" );
	
	
	
?>