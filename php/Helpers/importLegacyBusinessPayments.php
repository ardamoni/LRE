<?php
/*
 * 	this file is used to insert legacy payments into _balance and _payments
 */

	// libraries
	require_once( "../../lib/configuration.php" );
	require_once( "../../lib/Revenue.php" );
	require_once( "../../lib/System.php" );

	ob_start(); // prevent adding duplicate data with refresh (F5) - NOT WORKING !!!
	session_start();

class TableRows extends RecursiveIteratorIterator {
     function __construct($it) {
         parent::__construct($it, self::LEAVES_ONLY);
     }

     function current() {
         return "<td style='width: 150px; border: 1px solid black;'>" . parent::current(). "</td>";
     }

     function beginChildren() {
         echo "<tr>";
     }

     function endChildren() {
         echo "</tr>" . "\n";
     }
}
	$Data = new Revenue;
	$System = new System;

$st = $pdo->prepare("SELECT * from `rev_bibiani`.`payments_business` d1 where d1.`payment` <0 AND d1.upn IN (select upn from `revenue`.`kml_from_LUPMIS` where `districtid` = 126); ");
if (!$st->execute()){
  throw new Exception('[' . $st->errorCode() . ']: ' . $st->errorInfo());
  exit("Execute did not work");
  }else{
// 	$r = $st->fetchAll(PDO::FETCH_ASSOC);
	$count = $st->rowCount();
  	echo $count. ' records are processed';
	echo "<table class='demoTbl' border='1' cellpadding='10' cellspacing='1' bgcolor='#FFFFFF'>
			<tr'>";
			for ($i = 0; $i < $st->columnCount(); $i++) {
				$col = $st->getColumnMeta($i);
			//    echo '<th style="width:'.$col['len'].'em">'.$col['name'].'</th>';
				echo '<th width="500em">'.$col['name'].'</th>';
			}
			echo '</tr>';

	while( $r = $st->fetch(PDO::FETCH_BOTH))
	{
	 echo "<tr>";
	 for ($x=0; $x<$st->columnCount(); $x++)
  		{
		  echo "<td>" . $r[$x] . "</td>";
	// Send output to browser immediately
    flush();
    	    }
	  echo "</tr>";


//   	    foreach(new TableRows(new RecursiveArrayIterator($r)) as $k=>$v) {
//          echo $v;
//      	}
$conn = null;

// 	var_dump($r);
//
// 	// passed from parent
 	$upn 			= $r["upn"];
	$subupn 		= $r["subupn"];
	$paymentDate 	= $r['date_payment'];
	$paidBy 		= $r['payer'];
	$paidValue		= $r['payment']*-1; //payment is -30 in the original data
	$paymentType	= $r['type_payment'];
	$treceipt		= $r['receipt_payment'];
	$type		 	= 'business';

	$districtid 	= '126';  //$_SESSION['user']['districtid'];
	$roleid		 	= '0'; //$_SESSION['user']['roleid'];
	$userName		= $r['lastentry_person']; //'importLegacyPropertyPayments'; //$_SESSION['user']['name'];
//
// 	// static values
// 	// TODO change them to dynamic, from the map
// //	$station = "Station1";
	$station = $userName;
//
	if( $subupn == "" || $subupn == NULL || $subupn == 'null' || $subupn == "0" || $subupn == " - " )
	{
		$subupn = "";
	}
//
	if( !$paymentDate )
	{
		$paymentDate = date("Y-m-d");
	}
	else
	{
		$paymentDate = $paymentDate;
	}
//
	$currentYear = '2014'; //$System->GetConfiguration("RevenueCollectionYear");
	$previousYear =  $currentYear -1;

 	/*
 	 * previous year
 	 */
 	// DUE, PAYMENT, BALANCE
 	$revenueDuePrevious = $Data->getBalanceInfo( $upn, $subupn, $districtid, $previousYear, $type, "due" );
 	$revenueCollectedPrevious = $Data->getBalanceInfo( $upn, $subupn, $districtid, $previousYear, $type, "paid" );
 	$revenueBalancePrevious = $Data->getBalanceInfo( $upn, $subupn, $districtid, $previousYear, $type, "balance" );

 	/*
 	 * current year
 	 */
 	// DUE, PAYMENT, BALANCE
 	$revenueDue = $Data->getBalanceInfo( $upn, $subupn, $districtid, $currentYear, $type, "due" );
 	$revenueCollected = $Data->getBalanceInfo( $upn, $subupn, $districtid, $currentYear, $type, "paid" );
 	$revenueBalanceOld = $Data->getBalanceInfo( $upn, $subupn, $districtid, $currentYear, $type, "balance" );

 	// assuring NULL values are converted to 0
 	if( !$revenueBalanceOld )
 	{
 		$revenueBalanceOld = 0;
 	}

 	// calculations
 	$revenuePaid = $revenueCollected + $paidValue;  // current year
 	$revenueBalance = $revenueBalanceOld - $paidValue;


 	// display for testing
//  	echo "upn: ", $upn, ", subupn: ", $subupn, ", payment date: ", $paymentDate, ", paid by: ", $paidBy, ", role: ", $roleid, "<br>";
//  	echo ", paid value: ", $paidValue, ", payment type: ", $paymentType, ", ticket receipt: ", $treceipt, ", districtid: ", $districtid, "<br>";
//  	echo "revenueDuePrevious: ",  $revenueDuePrevious, ", revenueCollectedPrevious: ",  $revenueCollectedPrevious, ", revenueBalancePrevious: ",  $revenueBalancePrevious, "<br>";
//  	echo "revenueDue: ",  $revenueDue, ", revenueCollected: ",  $revenueCollected, ", revenueBalanceOld: ",  $revenueBalanceOld, "<br>";
//  	echo "revenuePaid: ",  $revenuePaid, ", revenueBalance: ",  $revenueBalance, "<br>";

 	// add new rown into property_payments
 	$sql2 = mysql_query( "INSERT INTO `business_payments` ( `id`,
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
 															'".$paidValue."',
 															NULL,
 															NULL,
 															'".$roleid."',
 															'".$station."',
 															'".$treceipt."',
 															'".$paymentType."',
 															'".$paidBy."',
 															'',
 															NULL,
 															NULL,
 															NULL
 															) " );


 	// update property_balance
 	$query = mysql_query(" UPDATE 	`business_balance`

 							SET 	`paid` = '".$revenuePaid."',
 									`balance`= '".$revenueBalance."',
 									`lastentry_person`= '".$userName."',
 									`lastentry_date`= '".$paymentDate."',
 									`comments`= '".$revenueBalance."'

 							WHERE 	`upn` = '".$upn."' AND
 									`subupn` = '".$subupn."' AND
 									`districtid` = '".$districtid."' AND
 									`year` = '".$currentYear."' ");


 	// update property's status	- needed for showing payments: green (9), debt (1)
 	if ($revenueBalance>0) { $payStatus = 1; } else { $payStatus = 9; }

 	$query12 = mysql_query(" UPDATE 	`business`

 							SET 	`pay_status` = '".$payStatus."',
 									`lastentry_person`= '".$userName."',
 									`lastentry_date`= '".$paymentDate."'

 							WHERE 	`upn` = '".$upn."' AND
 									`subupn` = '".$subupn."' AND
 									`districtid` = '".$districtid."' ");

 	//
 	// FINISHED UPDATING THE DB

	  } //end while
echo "</table>";
} //end if execute())
?>