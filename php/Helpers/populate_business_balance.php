<?php
	/*	
	 * 	this file is used to insert the revenue collection 
	 *	into table  business_balance
	 *  WARNING !!!
	 *  THIS SCRIPT SHOULD BE EXECUTED ONLY AFTER 
	 *  populate_business_due_2.php
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
	$districtID = $_GET['districtid'];
//	$districtID = 'ABC';
	
	// Display
	echo "START", "</br>";
	
	// PROTECTION
	$protection = mysql_query(" SELECT 	`upn` 
								FROM 	`business_balance` 
								WHERE 	`districtid` = '".$districtID."' AND `year` = '".$year."' ");
	
	$rp =  mysql_num_rows($protection);
	if( $rp == 0 )
	{	
		echo "Good, no previous data exist in the business_balance table for ";
		echo "districtID ", $districtID, " and year ", $year, ". Script will proceed.", "<br>";
	}
	else 
	{	
		echo "Stopping and exiting Script because there are previus data @ business_balance for districtID";
		echo $districtID, " and year ", $year , "</br>";
		echo "Counter of Existing rows: ", $rp, "</br>";
		exit();
	}
	
	// get all the data from Business 
	$querry = mysql_query(" SELECT * FROM `business` WHERE `districtid` = '".$districtID."' ");
	
	// Display - Missmatch or number of rows
	$i=1;
	if( mysql_num_rows($querry) == 0 )
	{	
		echo "no rows @ business", "<br>";
	}
	else 
	{	
		$rows =  mysql_num_rows($querry);
		echo "Report from business: districtID: ", $districtID, ", rows: ", $rows, "<br>";
	}
	
	// insert all business UPN, SUBUPN and DISTRICTID into table business_balance
	while($BOR = mysql_fetch_array($querry))
	{
		mysql_query(" INSERT INTO `business_balance` (	`id`,
														`upn`, 
														`subupn`,
														`districtid`,
														`year`,	
														`due`,														
														`paid`,
														`feefi_value`,
														`balance`,
														`instalment`,
														`comments`,
														`lastentry_person`,
														`lastentry_date` ) 
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
														'auto by populate_business_balance',
														'script',
														'".date("Y-m-d")."' ) ");
		
		// Display
		//echo $i, ": ", $BOR['upn'], " & ", $BOR['subupn'], " & ", $BOR['districtid'], " & ", $year, "<br>";
		//$i++;		
	}
	
	// UPDATE has two parts: business_balance with values from business_due and business_payments
	
	// UPDATE Part 1 - business_balance using business_due
	$qs1 = mysql_query( "SELECT DISTINCT	(`d`.`upn`) AS `upn`,
											`d`.`subupn` AS `subupn`,
											`d`.`districtid` AS `districtid`,											
											`d`.`year` AS `year`,											
											`d`.`bo_value` AS `bo_value`,
											`d`.`bo_impost_value` AS `bo_impost_value`,
											`d`.`feefi_value` AS `feefi_value`											
											
									FROM 	`business_due` `d`,											
											`business_balance` `b`
											
									WHERE 	`d`.`upn` = `b`.`upn` AND
											`d`.`subupn` = `b`.`subupn` AND
											`d`.`districtid` = '".$districtID."' AND
											`d`.`districtid` = `b`.`districtid` AND
											`d`.`year` = '".$year."' AND
											`d`.`year` = `b`.`year` 
											
									ORDER BY `d`.`upn`, `d`.`subupn` ASC " );
									
	$rr1 =  mysql_num_rows($qs1);
	if( $rr1 == 0 )
	{	
		echo "no rows @ business_due and business_balance: ", "<br>";
	}
	else 
	{			
		echo "Report from business_due & business_balance: districtID: ", $districtID, ", rows: ", $rr1, "<br>";
	}
	
	$j = 1;
	echo "Number of upn / subupn that have no due or previous balance", "<br>";
	while( $results = mysql_fetch_array($qs1) )
	{	
		// get previous year's balance - this is the entire debt 
		$prev_balance = $Data->getBalanceInfo( $results['upn'], $results['subupn'], $districtID, ($year-1), "business", "balance" );
		
		// all other values
		$due = $results['bo_value'] * $results['bo_impost_value'] + $results['feefi_value'];		
		$balance = $prev_balance + $due;		
		
		if( $due == 0 || $balance == 0 ) {
			echo $j, ": ", $results['upn'], " & ", $results['subupn'], " & ", $results['districtid'], " & ", $year;
			echo ": ", $prev_balance, " & ", $due, " & ", $balance, "<br>";
			$j++;
		}
		// business_balance update with value
		mysql_query("	UPDATE 		`business_balance` 
		
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
	
	// UPDATE business_balance using business_payments
	$pppb = mysql_query( "SELECT DISTINCT	(`t`.`upn`) AS `upn`,
											`t`.`subupn` AS `subupn`,
											`t`.`districtid` AS `districtid`,											
											`t`.`payment_value` AS `payment_value`,
											`b`.`balance` AS `balance`
											
									FROM 	`business_payments` `t`,											
											`business_balance` `b`
											
									WHERE 	`t`.`upn` = `b`.`upn` AND
											`t`.`subupn` = `b`.`subupn` AND
											`t`.`districtid` = '".$districtID."' AND
											`t`.`districtid` = `b`.`districtid` AND
											`b`.`year` = '".$year."' AND											
											`t`.`payment_date` > '".$year."'
											
									ORDER BY `t`.`upn`, `t`.`subupn` ASC " );
									
	$respppb =  mysql_num_rows($pppb);
	if( $respppb == 0 )
	{	
		echo "no rows @ business_payments and business_balance", "<br>";
	}
	else 
	{	
		echo "Report from business_payments & business_balance: districtID: ", $districtID, ", rows: ", $respppb, "<br>";	
	}
	
	$k = 1;
	echo "number, upn, subupn, districtid, year, paid, balance", "<br>";	
	while( $rupdate = mysql_fetch_array($pppb) )
	{	
		// get payments for a particular upn / subupn and year
		$paid = $Data->getSumPaymentInfo( $rupdate['upn'], $rupdate['subupn'], $districtID, $year, "business" );
		
		// get previous balance			
		$balance = $rupdate['balance'] - $paid;		
		
		// business_balance update with value
		mysql_query("	UPDATE 		`business_balance` 
		
						SET 		`paid` = '".$paid."',									
									`balance` = '".$balance."'
									
						WHERE 		`upn` = '".$rupdate['upn']."' AND 
									`subupn` = '".$rupdate['subupn']."' AND
									`districtid` = '".$rupdate['districtid']."' AND
									`year` = '".$year."' ");		
		
		// Display		
		echo $k, ": ", $rupdate['upn'], " & ", $rupdate['subupn'], " & ", $rupdate['districtid'], " & ", $year;
		echo ": ", $paid, " & ", $balance, "<br>";
		$k++;
	}	

?>