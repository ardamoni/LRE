<?php

	/*	
	 * 	this file is used to insert the revenue collection into tables
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
										`d`.`feefi_value` AS `feefi_value`,
										`t`.`payment_date` AS `payment_date`,
										`t`.`payment_value` AS `payment_value`
										
								FROM 	`property` `p`, 
										`property_due` `d`,
										`property_payments` `t`	
										
								WHERE 	`p`.`id` > 1 AND 
										`p`.`upn` = `d`.`upn` AND
										`p`.`upn` = `t`.`upn` AND
										`p`.`subupn` = `d`.`subupn` AND
										`p`.`subupn` = `t`.`subupn` AND
										`p`.`districtid` = `d`.`districtid` AND
										`p`.`districtid` = `t`.`districtid` AND										
										`p`.`revenue_balance` > 0 AND
										`t`.`payment_value` > 0 AND 										
										`t`.`payment_date` > '2013' 
								ORDER BY `p`.`upn`, `p`.`subupn` ASC " );
	
	$rows = mysql_num_rows($q1);
	
		
	echo $rows, " query finished ", "<br>";
	
	$i=1;
	
	while( $BOR = mysql_fetch_array($q1) )
	{	
		$value = $Data->getAnnualPaymentSum( $BOR['upn'], $BOR['subupn'], "2013" );
		
		mysql_query("INSERT INTO `property_balance` (	`id`,
														`upn`, 
														`subupn`,
														`districtid`,
														`year`,	
														`due`,														
														`payed`,
														`feefi_value`,
														`balance`
													) 
											VALUES 	( 	NULL,
														'".$BOR['upn']."',
														'".$BOR['subupn']."',
														'".$BOR['districtid']."',
														'".$BOR['year']."',
														'".$BOR['rate_value']."' + '".$BOR['rate_impost_value']."',
														'".$value."',
														'".$BOR['feefi_value']."',
														'".$BOR['rate_value']."' + '".$BOR['rate_impost_value']."' + 
														'".$BOR['feefi_value']."' - '".$value."'														
													)");
													
		echo $i, ": ", $BOR['upn'], " & ", $BOR['subupn'], " & ", $BOR['districtid'], " & ", $BOR['year'];
		echo " & ", $value, " & ", $BOR['feefi_value'], " & ", $BOR['rate_value'] + $BOR['rate_impost_value']; 
		echo " & ", $BOR['rate_value'] + $BOR['rate_impost_value'] + $BOR['feefi_value'] - $value, "<br>";
		$i++;
	}	
	
?>