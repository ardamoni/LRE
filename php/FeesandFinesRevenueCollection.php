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

 	var_dump($_POST);

	// passed from parent
	$upn 			= $_POST["upn"];
	$subupn 		= $_POST["subupn"];
	$paymentDate 	= $_POST['paymentdate'];
	$paidBy 		= $_POST['paidby'];
	$paidValue		= $_POST['paidvalue'];
	$paymentType	= $_POST['paymenttype'];
	$treceipt		= $_POST['treceipt'];
	$type		 	= $_POST['ifproperty'];
	$feeficode		= $_POST['feeficode'];

	$districtid 	= $_SESSION['user']['districtid'];
	$roleid		 	= $_SESSION['user']['roleid'];
	$userName		= $_SESSION['user']['name'];

	// static values
	// TODO change them to dynamic, from the map
//	$station = "Station1";
	$station = $userName;


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
	$feeficode		.= ' '.$Data->getFeeFixingInfo( $districtid, substr($feeficode,0,strlen($code)-1), $currentYear, $type, "class" );
	$feeficode		.= ', '.$Data->getFeeFixingInfo( $districtid, substr($feeficode,0,strlen($code)-1), $currentYear, $type, "category" );
	$feeficode		= substr($feeficode,0,40).'...';

	// add new rown into fees_fines_payments
		//we add a new property and need to update property_due, property_balance
// 				echo '<br>inside == true';

				 //use pdo wrapper
				    $insert = array(
						'upn' => $upn,
						'districtid' => $districtid,
						'feefi_code' => $_POST['feeficode'],
				    	'payment_date' => $paymentDate,
						'payment_value' => $paidValue,
						'station_payment' => $station,
						'receipt_payment' => $treceipt,
						'type_payment' => $paymentType,
						'comments' => $_POST['comments'],
						'lastentry_person' => $_SESSION['user']['user'],
						'lastentry_date' => $paymentDate
						);

					$result = $pdo->insert("fees_fines_payments", $insert);


	// show it all on the screen
if( $result )
	{
		// receipt in HTML
		?>
			<link rel="stylesheet" href="../css/flatbuttons.css" type="text/css">

			<input type="button" onClick="window.print()" class="orange-flat-small" value="Print this page"/>

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
					<td>Fee or Fine:</td>
					<td>
					<?php //echo $subupn;
							echo $feeficode;

					?>
					</td>
				</tr>
				<tr>
					<td>ADDRESS:</td>
					<td>
					<?php
						echo $Data->getBasicInfo( $upn, $subupn, $districtid, $type, "address" ), " ";
					?>
					</td>
				</tr>
				<tr>
					<td>Paid value:</td>
					<td><?php echo number_format($paidValue, 2,'.','');?></td>
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
		<p><input type="button" a href="javascript:;" onclick="window.close();" class="orange-flat-small" value="Close"></a></p>

		<?php
	}
	else
	{
		echo "ERROR";
	}

	window.close();
?>