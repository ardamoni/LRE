<?php

	/*	
	 * 	this file is used to insert the revenue collection into tables
	 */

	// DB connection
	require_once( "../lib/configuration.php" );
	require_once( "../lib/Revenue.php" );
	
	$Data = new Revenue;
	
	// passed from parent
	$upn 			= $_POST["upn"];	
	$subupn 		= $_POST["subupn"];				
	$paymentDate 	= $_POST['paymentdate']; 
	$payedBy 		= $_POST['payedby'];
	$payedValue		= $_POST['payedvalue']; 
	$paymentType	= $_POST['paymenttype'];
	$treceipt		= $_POST['treceipt'];	
	
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
	/*$sql1 = mysql_query( "INSERT INTO `property` (  `id`, 
													`upn`, 
													`subupn` )
											VALUES(  NULL, 
													'".$upn."', 
													'".$subupn."' ) ");	
	*/
	
	

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
		if($sql2)
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
						<td>Manual Receipt:</td>						
						<td><?php echo "TODO: enter a field in payment, pass it here";?></td>		
					</tr>
					<tr>
						<td>Automatic system Receipt:</td>						
						<td><?php echo $Data->getPropertyPaymentsInfo( $upn, $subupn, "id");?></td>	
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
						<td><?php echo $subupn;?></td>		
					</tr>
					<tr>
						<td>ADDRESS:</td>						
						<td><?php echo $Data->getPropertyInfo( $upn, $subupn, "streetname")," ", $Data->getPropertyInfo( $upn, $subupn, "housenumber");?></td>		
					</tr>
					<tr>
						<td>OWNER:</td>						
						<td><?php echo $Data->getPropertyInfo( $upn, $subupn, "owner");?></td>		
					</tr>
					<tr>
						<td>Pre 2013:</td>						
						<td><?php echo $Data->getPropertyPaymentsInfo( $upn, $subupn, "Later");?></td>		
					</tr>
					<tr>
						<td>2013 due:</td>						
						<td><?php echo $Data->getPropertyPaymentsInfo( $upn, $subupn, "balance_old");?></td>		
					</tr>
					<tr>
						<td>2013 last payment:</td>						
						<td><?php echo $Data->getPropertyPaymentsInfo( $upn, $subupn, "payment");?></td>		
					</tr>
					<tr>
						<td>2013 total payments:</td>						
						<td><?php echo $Data->getPropertyPaymentsInfo( $upn, $subupn, "payment");?></td>		
					</tr>
					<tr>
						<td>2013 balance:</td>						
						<td><?php echo $Data->getPropertyPaymentsInfo( $upn, $subupn, "balance_new");?></td>		
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