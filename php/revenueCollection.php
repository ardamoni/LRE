<?php

	/*	
	 * 	this file is used to insert the revenue collection into tables
	 */

	// DB connection
	require_once( "../lib/configuration.php" );
	require_once( "../lib/Revenue.php" );
	require_once( "../lib/System.php" );
	
	ob_start(); // prevent adding duplicate data with refresh (F5)
	session_start();
	
	$Data = new Revenue;
	$System = new System;
	
	// passed from parent
	$upn 			= $_POST["upn"];	
	$subupn 		= $_POST["subupn"];				
	$paymentDate 	= $_POST['paymentdate']; 
	$payedBy 		= $_POST['payedby'];
	$payedValue		= $_POST['payedvalue']; 
	$paymentType	= $_POST['paymenttype'];
	$treceipt		= $_POST['treceipt'];	
	
	$districtid 	= $_SESSION['user']['districtid'];
	$roleid		 	= $_SESSION['user']['roleid'];	
	
	// static values 	
	// TODO change them to dynamic, from the map
	$station = "Station1";		
	
	
	if( !$subupn )
	{		
		$subupn = '';
	}
	
	if( !$paymentDate )
	{		
		$paymentDate = date("Y-m-d");
	}
	else
	{
		$paymentDate = $paymentDate;
	}
	
	$currentYear = $System->GetConfiguration("RevenueCollectionYear");
	
/*	
	echo "upn: ", $upn, ", subupn: ", $subupn, ", payment date: ", $paymentDate, ", paid by: ", $payedBy, ", role: ", $roleid, "<br>"; 
	echo ", paid value: ", $payedValue, ", payment type: ", $paymentType, ", ticket receipt: ", $treceipt, ", districtid: ", $districtid, "<br>";
*/	
	
	/* 	
	 * previous years
	 */
	 
	// DUE
	$revenueDuePrevious = 0.0;
	for( $years = $currentYear-4; $years<$currentYear; $years++ )
	{
		$revenueDuePrevious += $Data->getAnnualDueSum( $upn, $subupn, $years );
	}
	
	// PAYMENTS
	$revenueCollectedPrevious = 0.0;
	for( $years = $currentYear-4; $years<$currentYear; $years++ )
	{
		$revenueCollectedPrevious += $Data->getAnnualPaymentSum( $upn, $subupn, $years );
	}
	
	// BALANCE
	$revenueBalancePrevious = 0.0;
	for( $years = $currentYear-4; $years<$currentYear; $years++ )
	{
		$revenueBalancePrevious += $Data->getPropertyBalanceInfo( $upn, $subupn, $years, "balance" );
	}
	
	
	/* 
	 * current year
	 */
	// DUE, PAYMENT, BALANCE 
	$revenueDue = $Data->getAnnualDueSum( $upn, $subupn, $currentYear );	
	$revenueCollected = $Data->getAnnualPaymentSum( $upn, $subupn, $currentYear );	
	$revenueBalanceOld = $Data->getPropertyBalanceInfo( $upn, $subupn, $currentYear, "balance" );	
	
	// assuring NULL values are converted to 0
	if( !$revenueBalanceOld )
	{
		$revenueBalanceOld = 0;
	}
	
	// calculations
	$revenuePaid = $revenueCollected + $payedValue;
	$revenuePaidTotal = $revenueCollectedPrevious + $revenuePaid;	
	$revenueBalance = $revenueDue - $revenueBalanceOld - $revenuePaid;
	$revenueBalanceTotal =  - ($revenueDuePrevious - $revenueBalancePrevious - $revenueBalance);
	
	
/*
	echo "Revenue due: ", $revenueDue, ", revenue paid: ", $revenuePaid, ", balance: ", $revenueBalance, "<br>";
*/	
	
	// add new rown into payments
	$sql2 = mysql_query( "INSERT INTO `property_payments` ( `id`, 																
															`upn`,
															`subupn`, 
															`districtid`, 
															`payment_date`, 
															`payment_value`,
															`instalments`, 
															`instalment_order`,
															`collectorid`,	
															`station_payment`,
															`receipt_payment`,
															`type_payment`,
															`payer`,
															`paid_for`, 
															`demand_notice_no`, 
															`demand_notice_sent`, 
															`comments`)
													VALUES( NULL, 															
															'".$upn."',
															'".$subupn."',
															'".$districtid."',															
															'".$paymentDate."', 
															'".$payedValue."',
															NULL,
															NULL,
															'".$roleid."',
															'".$station."',
															'".$treceipt."',
															'".$paymentType."',
															'".$payedBy."',
															'',
															NULL,
															NULL,
															NULL
															) " ); 

/// for testing															
//	$sql2 = mysql_query( "INSERT INTO `property_payments`(`id`, `upn`, `subupn`, `districtid`, `payment_date`, `payment_value`, `instalments`, `instalment_order`, `collectorid`, `station_payment`, `receipt_payment`, `type_payment`, `payer`, `paid_for`, `demand_notice_no`, `demand_notice_sent`, `comments`) 
//							VALUES (NULL,'608-0615-0339','', 130, '2013-01-01', 20, NULL, NULL, NULL, '', '', '', '', '', NULL, NULL, NULL) "); 

/*
	if( $sql2 )
	{
		echo $paymentDate, " , ", $currentYear, " Row was added in property_payment", "<br>";
	}
	else
	{
		echo $paymentDate, " , ", $currentYear, "error during property_payment", "<br>";
	}
*/

	// update balance	
	$query = mysql_query(" UPDATE 	`property_balance` 
							SET 	`payed` = '".$revenuePaid."',
									`balance`= '".$revenueBalance."' 
							WHERE 	`upn` = '".$upn."' AND
									`subupn` = '".$subupn."' AND
									`districtid` = '".$districtid."' AND
									`year` = '".$currentYear."' ");								
/*
	if( $query )
	{
		echo $paymentDate, " , ", $currentYear, "Row was modified in property_balance", "<br>";
	}
	else 
	{
		echo $paymentDate, " , ", $currentYear, "error during property_balance", "<br>";
	}
*/
	
	if( $sql2 && $query )
	{			
		// receipt in HTML
		?>			
			<input type="button" onClick="window.print()" value="Print this page"/>			 
		
			<table width = '100%' border = '1' align = 'center' cellpadding = '10' style = 'border: 1px solid #dbdbdb;'>
				<tr>
					<td id = 'layout' bgcolor = '#efefef'> &nbsp; <b><center><?php echo "PAYMENT RECEIPT"; ?></center></td>
				</tr>
			</table>
			<table width = '100%' border = '1'>
				<tr>
					<td>Ticket Receipt:</td>						
					<td><?php echo $treceipt; ?></td>		
				</tr>
				<tr>
					<td>Automatic system Receipt:</td>						
					<td><?php echo $Data->getLastPropertyPaymentInfo( $upn, $subupn, $currentYear, "id" );?></td>	
				</tr>
			</table>
			<table width = '100%' border = '1'>
				<!--UPN, SUBUPN, ADDRESS, OWNER, PRE 2013, (2013): FEE, PAID SO FAR, BALANCE-->
				<tr>
					<td>UPN:</td>						
					<td><?php echo $upn;?></td>		
				</tr>
				<tr>
					<td>SUBUPN:</td>						
					<td>
					<?php echo $subupn;
						/*if( $subupn ) 
							echo $subupn;
						else
							echo "no subupn is specified";
							*/
					?>
					</td>		
				</tr>
				<tr>
					<td>ADDRESS:</td>						
					<td>
					<?php 
						echo 	$Data->getPropertyInfo( $upn, $subupn, $currentYear, "streetname")," ", 
								$Data->getPropertyInfo( $upn, $subupn, "housenumber");
					?>
					</td>		
				</tr>
				<tr>
					<td>OWNER:</td>						
					<td><?php  
							$ownerid = $Data->getPropertyInfo( $upn, $subupn, $currentYear, "ownerid" );
							echo $Data->getOwnerInfo( $ownerid, 'name' );
						?></td>		
				</tr>
				<tr>
					<td>Pre 2013 balance:</td>						
					<td><?php echo number_format($revenueBalancePrevious, 2,'.','');?></td>		
				</tr>
				<tr>
					<td>2013 due:</td>						
					<td><?php echo number_format($revenueDue, 2,'.','');?></td>		
				</tr>
				<tr>
					<td>2013 last payment:</td>						
					<td><?php echo number_format($payedValue, 2,'.','');?></td>		
				</tr>
				<tr>
					<td>2013 total payments:</td>						
					<td><?php echo number_format($revenuePaid, 2,'.','');?></td>		
				</tr>
				<tr>
					<td>2013 balance:</td>						
					<td><?php echo number_format($revenueBalance, 2,'.','');?></td>		
				</tr>
				<tr>
					<td>Overall balance:</td>						
					<td><?php echo number_format($revenueBalanceTotal, 2,'.','');?></td>		
				</tr>
			</table>
			</br>
			<table>
			<!-- receipt in PDF -->
				<tr>
					<td id = 'layout' height = '25'> &nbsp; <img src = '../icons/sign.gif' border = '0'> &nbsp; 
						<a href = 'Reports/ReceiptOfPayment.php?upn=<?php echo $upn;?>&subupn=<?php echo $subupn;?>'><?php echo "Print in PDF"; ?></a></td>
						<!--<a href = 'Reports/ReceiptOfPayment.php?upn=<?php// echo $upn; ?>&subupn=<?php //echo $subupn; ?>'><?php //echo "Receipt of Payment"; ?></a></td>-->
				</tr>
			</table>

		<br />
		
		<?php
	}
	else 
	{
		echo "ERROR";
	}
	
	window.close();
?>