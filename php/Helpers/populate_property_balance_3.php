<?php
	/*	
	 * 	this file is used to insert the revenue collection into tables
	 *  property_balance
	 *  WARNING !!!
	 *  THIS SCRIPT SHOULD BE EXECUTED ONLY AFTER 
	 *  populate_property_due_2.php
	 */
	 
	
	// DB connection
	require_once( "../../lib/configuration.php" );
	require_once( "../../lib/System.php" );
	require_once( "../../lib/Revenue.php" );
	
	$Data = new Revenue;	
	$System = new System;		
	$year = $System->GetConfiguration("RevenueCollectionYear");

	//
	// CHANGE THIS VALUE for every district 
	//
	$districtID = '130';
	
	// Display
	echo "START", "</br>";
	
	// PROTECTION
	$protection = mysql_query(" SELECT 	`upn` 
								FROM 	`property_balance` 
								WHERE 	`districtid` = '".$districtID."' AND `year` = '".$year."' ");
	
	if( mysql_num_rows($protection) == 0 )
	{	
		echo "Good, no previous data exist in the property_balance table for ";
		echo "districtID ", $districtID, " and year ", $year, ". Script will proceed.", "<br>";
	}
	else 
	{	
		$rp =  mysql_num_rows($protection);
		echo "Stopping and exiting Script because there are previus data @ property_balance for districtID";
		echo $districtID, " and year ", $year , "</br>";
		echo "Counter of Existing rows: ", $rp, "</br>";
		exit();
	}
	
	// get all the data from Property 
	$querry = mysql_query(" SELECT * FROM `property` WHERE `districtid` = '".$districtID."' ");
	
	// Display - Missmatch or number of rows
	$i=1;
	if( mysql_num_rows($querry) == 0 )
	{	
		echo "no rows @ property", "<br>";
	}
	else 
	{	
		$rows =  mysql_num_rows($querry);
		echo "Report from property: districtID: ", $districtID, ", rows: ", $rows, "<br>";
	}
	
	// insert all property UPN, SUBUPN and DISTRICTID into Property_balance
	while($BOR = mysql_fetch_array($querry))
	{
		// property_due
		mysql_query(" INSERT INTO `property_balance` (	`id`,
														`upn`, 
														`subupn`,
														`districtid`,
														`year`,	
														`due`,														
														`payed`,
														`feefi_value`,
														`balance`,
														`instalment`,
														`comments` ) 
											VALUES 	( 	NULL,
														'".$BOR['upn']."',
														'".$BOR['subupn']."',
														'".$BOR['districtid']."',
														'".$year."',
														0,
														0,
														0,
														0, 
														0,
														'ekke - ".date("Y-m-d")."' ) ");
		
		// Display
		//echo $i, ": ", $BOR['upn'], " & ", $BOR['subupn'], " & ", $BOR['districtid'], " & ", $year, "<br>";
		//$i++;		
	}
	
	// UPDATE has two parts: property_balance with values from property_due and property_payments
	// UPDATE using property_due
	$qs1 = mysql_query( "SELECT DISTINCT	(`d`.`upn`) AS `upn`,
											`d`.`subupn` AS `subupn`,
											`d`.`districtid` AS `districtid`,											
											`d`.`year` AS `year`,
											`d`.`rate_value` AS `rate_value`,
											`d`.`rate_impost_value` AS `rate_impost_value`,
											`d`.`feefi_value` AS `feefi_value`											
											
									FROM 	`property_due` `d`,											
											`property_balance` `b`
											
									WHERE 	`d`.`upn` = `b`.`upn` AND
											`d`.`subupn` = `b`.`subupn` AND
											`d`.`districtid` = '".$districtID."' AND
											`d`.`districtid` = `b`.`districtid` AND
											`d`.`year` = '".$year."' AND
											`d`.`year` = `b`.`year` 
											
									ORDER BY `d`.`upn`, `d`.`subupn` ASC " );
	
	if( mysql_num_rows($qs1) == 0 )
	{	
		echo "no rows @ property_due and property_balance: ", "<br>";
	}
	else 
	{	
		$rr1 =  mysql_num_rows($qs1);
		echo "Report from property_due & property_balance: districtID: ", $districtID, ", rows: ", $rr1, "<br>";
	}
	
	$j = 1;
	while( $results = mysql_fetch_array($qs1) )
	{	
		// get previous years balance - this is the entire debt 
		$prev_balance = $Data->getBalanceInfo( $results['upn'], $results['subupn'], $districtID, ($year-1), "property", "balance" );
		// obsolete - 15.07.2014
		//$prev_balance = $Data->getEndBalance( $results['upn'], $results	['subupn'], $districtID, ($year-1) );
		
		// all other values
		$due = $results['rate_value'] + $results['rate_impost_value'] + $results['feefi_value'];		
		$balance = $prev_balance + $due;		
		
		// Property_baance update with value
		mysql_query("	UPDATE 		`property_balance` 
		
						SET 		`due` = '".$due."',									
									`feefi_value` = '".$results['feefi_value']."',
									`balance` = '".$balance."'
									
						WHERE 		`upn` = '".$results['upn']."' AND 
									`subupn` = '".$results['subupn']."' AND
									`districtid` = '".$results['districtid']."' AND
									`year` = '".$year."' ");		
		
		// Display
		//echo $j, ": ", $results['upn'], " & ", $results['subupn'], " & ", $results['districtid'], " & ", $year;
		//echo ": ", $prev_balance, " & ", $due, " & ", $balance, "<br>";
		//$j++;
	}	
	
	// UPDATE using property_payments
	$pppb = mysql_query( "SELECT DISTINCT	(`t`.`upn`) AS `upn`,
											`t`.`subupn` AS `subupn`,
											`t`.`districtid` AS `districtid`,											
											`t`.`payment_value` AS `payment_value`,
											`b`.`balance` AS `balance`
											
									FROM 	`property_payments` `t`,											
											`property_balance` `b`
											
									WHERE 	`t`.`upn` = `b`.`upn` AND
											`t`.`subupn` = `b`.`subupn` AND
											`t`.`districtid` = '".$districtID."' AND
											`t`.`districtid` = `b`.`districtid` AND
											`b`.`year` = '".$year."' AND											
											`t`.`payment_date` > '".$year."'
											
									ORDER BY `t`.`upn`, `t`.`subupn` ASC " );
	
	if( mysql_num_rows($pppb) == 0 )
	{	
		echo "no rows @ property_payments and property_balance", "<br>";
	}
	else 
	{	
		$respppb =  mysql_num_rows($pppb);
		echo "Report from property_payments & property_balance: districtID: ", $districtID, ", rows: ", $respppb, "<br>";	
	}
	
	$k = 1;
	while( $rupdate = mysql_fetch_array($pppb) )
	{	
		// get payments for a particular upn / subupn and year
		$payed = $Data->getAnnualPaymentSum( $rupdate['upn'], $rupdate['subupn'], $year );
		// get previous balance			
		$balance = $rupdate['balance'] - $payed;		
		
		// Property_balance update with value
		mysql_query("	UPDATE 		`property_balance` 
		
						SET 		`payed` = '".$payed."',									
									`balance` = '".$balance."'
									
						WHERE 		`upn` = '".$rupdate['upn']."' AND 
									`subupn` = '".$rupdate['subupn']."' AND
									`districtid` = '".$rupdate['districtid']."' AND
									`year` = '".$year."' ");		
		
		// Display		
		echo $k, ": ", $rupdate['upn'], " & ", $rupdate['subupn'], " & ", $rupdate['districtid'], " & ", $year;
		echo ": ", $payed, " & ", $balance, "<br>";
		$k++;
	}	

?>