<?php

	/*	
	 * 	this file is used to insert the revenue collection into tables
	 */

	// DB connection
	require_once( "../../lib/configuration.php" );
	
	echo "start: ", "<br>";
	
	$q1 = mysql_query("SELECT * from `property` WHERE `id` > 1 LIMIT 3000");
		
	$i=1;
	while($BOR = mysql_fetch_array($q1))
	{
		mysql_query("INSERT INTO `property_due` (	`id`,
													`upn`, 
													`subupn`
												) 
										VALUES 	( 	NULL,
													'".$BOR['upn']."',
													'".$BOR['subupn']."'
												)");
												
		echo $i, ": ", $BOR['upn'], " & ", $BOR['subupn'], "<br>";
		$i++;
	}
	
	// update empty columns
	mysql_query( "UPDATE 	`property_due` SET 
							`districtid` = '1840',
							`year` = '2013',
							`prop_value` = '10000',
							`unit` = 'annually',
							`rate_value` = '100',
							`rate_impost_value` = '10', 
							`feefi_code` = '10010011',
							`feefi_value` = '11.11' " );	
?>