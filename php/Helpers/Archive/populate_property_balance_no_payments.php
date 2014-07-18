<?php

	/*	
	 * 	this file is used to insert the revenue collection into tables
	 *  IT IS CRITICAL THAT property_due is populated 
	 *  USE populate_property_due
	 */

	// DB connection
	require_once( "../../lib/configuration.php" );
	require_once( "../../lib/Revenue.php" );
	
	$Data = new Revenue;
	
	echo "start: ";
	
	$q1 = mysql_query( "SELECT 	DISTINCT( `p`.`id` ) AS `id`,
											`p`.`upn` AS `upn`,
											`p`.`subupn` AS `subupn`,
											`p`.`districtid` AS `districtid`,
											`p`.`revenue_balance` AS `revenue_balance`,
											`d`.`year` AS `year`,
											`d`.`rate_value` AS `rate_value`,
											`d`.`rate_impost_value` AS `rate_impost_value`,
											`d`.`feefi_value` AS `feefi_value`
											
									FROM 	`property` `p`, 
											`property_due` `d`	
											
									WHERE 	`p`.`id` > 1 AND 
											`p`.`upn` = `d`.`upn` AND
											`p`.`subupn` = `d`.`subupn` AND
											`p`.`districtid` = `d`.`districtid`
									ORDER BY `p`.`upn`, `p`.`subupn` ASC " );
	
	$rows = mysql_num_rows($q1);
	
		
	echo $rows, " query finished ", "<br>";
	
	$i = 1; $j = 1;
	
	while( $BOR = mysql_fetch_array($q1) )
	{	
		$due = $Data->getAnnualDueSum( $BOR['upn'], $BOR['subupn'], $BOR['year'] );
		$value = $Data->getAnnualPaymentSum( $BOR['upn'], $BOR['subupn'], $BOR['year'] );
// 		$verify = mysql_query( "SELECT 	`upn`, `subupn` 
// 								FROM 	`property_balance` 
// 								WHERE 	`upn` = '".$BOR['upn']."' AND										
// 										`subupn` = '".$BOR['subupn']."' AND
// 										`districtid` = '".$BOR['districtid']."' AND
// 										`year` = '".$BOR['year']."' ");
// 		
		// property_balance must be there when a new property is added				
// 		if( mysql_num_rows($verify) == 0 )
// 		{
			mysql_query("INSERT INTO `property_balance` (	`id`,
															`upn`, 
															`subupn`,
															`districtid`,
															`year`,	
															`due`,														
															`payed`,
															`feefi_value`,
															`balance`,
															`instalment`,
															`comments`
														) 
												VALUES 	( 	NULL,
															'".$BOR['upn']."',
															'".$BOR['subupn']."',
															'".$BOR['districtid']."',
															'".$BOR['year']."',
															'".$due."',
															'".$value."',
															'".$BOR['feefi_value']."',
															'".$BOR['rate_value']."' + '".$BOR['rate_impost_value']."' + 
															'".$due."' - '".$value."',
															NULL,
															'ekke - ".date("Y-m-d")."'														
														)");
// substituted with $due															'".$BOR['rate_value']."' + '".$BOR['rate_impost_value']."',
													
			echo $i, ": ", $BOR['upn'], " & ", $BOR['subupn'], " & ", $BOR['districtid'], " & ", $BOR['year'];
			echo " & ", $value, " & ", $BOR['feefi_value'], " & ", $BOR['rate_value'] + $BOR['rate_impost_value']; 
			echo " & ", $BOR['rate_value'] + $BOR['rate_impost_value'] + $BOR['feefi_value'] - $value, "<br>";
			$i++;
// 		}
// 		else
// 		{
// 			echo "WARNING: ", $BOR['upn'], " & ", $BOR['subupn'], " does NOT exist in property_balance", "<br>";
// 			$j++;
// 		}
	
	}	

?>