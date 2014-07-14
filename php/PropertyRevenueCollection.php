<?php

	/*	
	 * 	this file is used to insert the revenue collection into tables
	 */

	// libraries
	require_once( "../lib/configuration.php" );
	require_once( "../lib/Revenue.php" );
	require_once( "../lib/System.php" );
	
	ob_start(); // prevent adding duplicate data with refresh (F5) - NOT WORKING !!!
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
	$type		 	= $_POST['ifproperty'];	
	
	$districtid 	= $_SESSION['user']['districtid'];
	$roleid		 	= $_SESSION['user']['roleid'];	
	$userName		= $_SESSION['user']['name'];	
	
	// static values 	
	// TODO change them to dynamic, from the map
	$station = "Station1";		
	
	if( $subupn == "" || $subupn == NULL || $subupn == 'null' || $subupn == "0" || $subupn = " - " )
	{ 
		$subupn = "";
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
	$previousYear =  $currentYear -1;

	/* 
	 * previous year
	 */
	// DUE, PAYMENT, BALANCE 
	$revenueDuePrevious = $Data->getBalanceInfo( $upn, $subupn, $districtid, $previousYear, $type, "due" );
	$revenueCollectedPrevious = $Data->getBalanceInfo( $upn, $subupn, $districtid, $previousYear, $type, "payed" );
	$revenueBalancePrevious = $Data->getBalanceInfo( $upn, $subupn, $districtid, $previousYear, $type, "balance" );
	
	/* 
	 * current year
	 */
	// DUE, PAYMENT, BALANCE 
	$revenueDue = $Data->getBalanceInfo( $upn, $subupn, $districtid, $currentYear, $type, "due" );
	$revenueCollected = $Data->getBalanceInfo( $upn, $subupn, $districtid, $currentYear, $type, "payed" );
	$revenueBalanceOld = $Data->getBalanceInfo( $upn, $subupn, $districtid, $currentYear, $type, "balance" );
	
	// assuring NULL values are converted to 0
	if( !$revenueBalanceOld )
	{
		$revenueBalanceOld = 0;
	}
	
	// calculations
	$revenuePaid = $revenueCollected + $payedValue;  // current year
	$revenueBalance = $revenueBalanceOld - $payedValue;

/*	
	// display for testing
	echo "upn: ", $upn, ", subupn: ", $subupn, ", payment date: ", $paymentDate, ", paid by: ", $payedBy, ", role: ", $roleid, "<br>"; 
	echo ", paid value: ", $payedValue, ", payment type: ", $paymentType, ", ticket receipt: ", $treceipt, ", districtid: ", $districtid, "<br>";
	echo "revenueDuePrevious: ",  $revenueDuePrevious, ", revenueCollectedPrevious: ",  $revenueCollectedPrevious, ", revenueBalancePrevious: ",  $revenueBalancePrevious, "<br>";
	echo "revenueDue: ",  $revenueDue, ", revenueCollected: ",  $revenueCollected, ", revenueBalanceOld: ",  $revenueBalanceOld, "<br>";
	echo "revenuePaid: ",  $revenuePaid, ", revenueBalance: ",  $revenueBalance, "<br>";
*/	
	// add new rown into property_payments
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


	// update property_balance	
	$query = mysql_query(" UPDATE 	`property_balance` 
	
							SET 	`payed` = '".$revenuePaid."',
									`balance`= '".$revenueBalance."',
									`comments`= '".$revenueBalance."'
									
							WHERE 	`upn` = '".$upn."' AND
									`subupn` = '".$subupn."' AND
									`districtid` = '".$districtid."' AND
									`year` = '".$currentYear."' ");								

									
	// update property's status	- needed for showing payments: green (9), debt (1)
	if ($revenueBalance>0) { $payStatus = 1; } else { $payStatus = 9; }
	
	$query12 = mysql_query(" UPDATE 	`property` 

							SET 	`pay_status` = '".$payStatus."',
									`lastentry_person`= '".$userName."',
									`lastentry_date`= '".$paymentDate."'									
								
							WHERE 	`upn` = '".$upn."' AND
									`subupn` = '".$subupn."' AND
									`districtid` = '".$districtid."' ");								

	//
	// FINISHED UPDATING THE DB
	//
	
	// show it all on the screen
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
					<td>Automatic System Receipt:</td>						
					<td><?php echo $Data->getLastPaymentInfo( $upn, $subupn, $districtid, $currentYear, $type, "id" );?></td>	
				</tr>
				<tr>
					<td>Date:</td>						
					<td><?php echo date("d-m-Y @ h:i:sa");?></td>	
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
					<?php //echo $subupn;
						if( $subupn == "" ) 
							echo " - ";
						else
							echo $subupn;
							
					?>
					</td>		
				</tr>
				<tr>
					<td>ADDRESS:</td>						
					<td>
					<?php 
						echo $Data->getBasicInfo( $upn, $subupn, $districtid, $type, "streetname" ), " ";
						echo $Data->getBasicInfo( $upn, $subupn, $districtid, $type, "housenumber" );
					?>
					</td>		
				</tr>
				<tr>
					<td>OWNER:</td>						
					<td><?php echo $Data->getBasicInfo( $upn, $subupn, $districtid, $type, "owner" ); ?>
					</td>		
				</tr>
				<tr>
					<td>Old <?php echo $currentYear;?> balance: *</td>						
					<td><?php echo number_format($revenueBalanceOld, 2,'.','');?></td>		
				</tr>
				<tr>
					<td>Paid value:</td>						
					<td><?php echo number_format($payedValue, 2,'.','');?></td>		
				</tr>
				<tr>
					<td>New <?php echo $currentYear; ?> balance: *</td>						
					<td><?php echo number_format($revenueBalance, 2,'.','');?></td>		
				</tr>

			</table>
			</br>
			* negative value indicates credit  
			</br></br>
			<table>
			<!-- receipt in PDF -->
				<tr>
					<td id = 'layout' height = '25'> &nbsp; <img src = '../icons/sign.gif' border = '0'> &nbsp; 
						<a href = 'Reports/ReceiptOfPropertyPayment.php?upn=<?php echo $upn;?>&subupn=<?php echo $subupn;?>&districtid=<?php echo $districtid;?>'><?php echo "Print in PDF"; ?></a></td>
						<!--<a href = 'Reports/ReceiptOfPropertyPayment.php?upn=<?php// echo $upn; ?>&subupn=<?php //echo $subupn; ?>'><?php //echo "Receipt of Payment"; ?></a></td>-->
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