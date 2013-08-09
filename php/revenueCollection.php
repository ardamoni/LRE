<?php

	/*	
	 * 	this file is used to insert the revenue collection into tables
	 */

	// DB connection
	require_once( "configuration.php" );
	require_once( "../lib/Revenue.php" );
	
	$Data = new Revenue;
	
	// passed from parent
	$upn 		= $_POST["upn"];	
	$subupn 	= $_POST["subupn"];
		
	// Get values from form 			
	$paymentDate 	= $_POST['paymentdate']; 
	$payedBy 		= $_POST['payedby'];
	$payedValue		= $_POST['payedvalue']; 
	$paymentType	= $_POST['paymenttype']; 
	
	if( !$paymentDate )
	{		
		$paymentDate = date("Y-m-d");
	}
	else
	{
		$paymentDate = $paymentDate;
	}
	
	// get values from table
	//$year = date("Y");
	$revenueDue = $Data->getPropertyInfo( $upn, $subupn, /*$year,*/ "revenue_due");
	$revenueCollected = $Data->getPropertyInfo( $upn, $subupn, /*$year,*/ "revenue_collected");
	$revenueBalanceOld = $Data->getPropertyInfo( $upn, $subupn, /*$year,*/ "revenue_balance");	
	
	// assuring NULL values are 0
	if( !$revenueBalanceOld )
	{
		$revenueBalanceOld = 0;
	}
	
	// calculations
	$revenueCollected = $revenueCollected + $payedValue;
	$revenueBalance = $revenueBalanceOld + $revenueCollected;	
	
	// static values 
	// TODO change them to dynamic, from the map
	$station = "Station1";
	$receipt = "Receipt1";	
	$collectorID = 100;
		
	// Insert data into tables 	
	$sql1 = mysql_query( "INSERT INTO `property` (  `id`, 
													`upn`, 
													`subupn` )
											VALUES(  NULL, 
													'".$upn."', 
													'".$subupn."' ) ");	
	
	
	

	$sql2 = mysql_query( "INSERT INTO `payments_property` ( `id`, 	
															`id_property`,
															`upn`,
															`subupn`, 
															`balance_new`, 
															`balance_old`, 
															`date_payment`,
															`payment`,	
															`station_payment`,
															`receipt_payment`,
															`type_payment`,
															`payer`,
															`collector_id`)
															VALUES( NULL, 
															'1', 
															'".$upn."',
															'".$subupn."',
															'".$revenueBalance."',
															'".$revenueBalanceOld."',
															'".$paymentDate."', 
															'".$payedValue."',
															'".$station."',
															'".$receipt."',
															'".$paymentType."',
															'".$payedBy."',
															'".$collectorID."') " ); 
																
		// TEST 
		if($sql1)
		{
			echo "Successful</br>Print";
		}
		else 
		{
			echo "ERROR";
		}
		
		// TEST 
		if($sql2)
		{
			echo "Successful<BR>";
		}
		else 
		{
			echo "ERROR";
		}
			
	

	
	


?>