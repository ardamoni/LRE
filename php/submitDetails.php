<?php
//    require_once("../lib/initialize.php");

error_reporting(E_ALL);
set_time_limit(0);
ob_start(); // prevent adding duplicate data with refresh (F5)
session_start();

date_default_timezone_set('Europe/London');

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Update Details Information</title>
<link rel="stylesheet" href="../css/ex.css" type="text/css" />
<link rel="stylesheet" href="../css/flatbuttons.css" type="text/css" />
<link rel="stylesheet" href="../css/error.css" type="text/css" />
<link rel="stylesheet" href="../lib/OpenLayers/theme/default/style.css" type="text/css" />
<link rel="stylesheet" href="../style.css" type="text/css" />
<style type="text/css">

table.demoTbl {
    border-collapse: collapse;
    border-spacing: 0;
   	border-color:#ffcc00;
}

table.demoTbl tr {
	border: 1px solid #ccc;
	border-color:#ffcc00;
	font-size:1em;
	padding:2px;
/* //	width: 2em; */
}

table.demoTbl td{
	font-size:1em;
	text-align:left;
	padding:5px;
	left:5px;
/* //	width: 100%; */
	border: 1px solid #ccc;
	border-color:#ffcc00;
}

</style>

</head>
<body>

<?php

require_once( "../lib/System.php"			);
require_once( "../lib/configuration.php"	);
require_once( "../lib/Revenue.php"			);

$Data = new Revenue;
$System = new System;

// var_dump($_POST);
$upn=$_POST['upn'];
$subupn=$_POST['subupn'];
$districtid=$_POST['districtid'];
$type = $_POST['ifproperty'];
$addDetails = $_POST['addDetails'];
$today = date("Y/m/d");
$year = date("Y");

	$currentYear = $System->GetConfiguration("RevenueCollectionYear");
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
 	$revenuePaid = $revenueCollected + $revenueCollectedPrevious;  // current year
 	$revenueBalance = $revenueBalancePrevious+$revenueBalanceOld - $revenuePaid;



//  echo "<pre>";
if (isset($addDetails)){
//  var_dump($_POST);
}else{echo 'not set';}
// var_dump($_SESSION);
// echo "</pre>";

$username = $_SESSION['user']['user'];
 if ($_POST['excluded']){
				    $excluded=1;}
				    else {
				    $excluded=0;}


		switch( $type )
			{
				case "property":

				$feefi_code = substr($_POST['propertyType'], 0, strpos($_POST['propertyType'], ':')-1);
				$feefi_value = $Data->getFeeFixingInfo( $districtid, $feefi_code, $year, "property", "rate" );
				$rate_impost_value = 0;
				$rate_value = 0;
				$due = $feefi_value;
				$balance = $due;
// debug echo '<br> ffc '.$feefi_code.' - ffv'.
				$feefi_value.' - riv '.
				$rate_impost_value.' - rv '.
				$rate_value.' - d '.
				$due.' - b'.
				$balance;

				if ($_POST['prop_value']!= ''){
					$rate_impost_value = $Data->getFeeFixingInfo( $districtid, $feefi_code, $year, "property", "rate_impost" );
					$rate_value = ($_POST['prop_value']*$rate_impost_value);
					if ($rate_impost_value!='' && $rate_value>0){
						$due = $rate_value;
						$balance = $due;
					}
				}
// debug echo '<br> ffc '.$feefi_code.' - ffv'.
// 				$feefi_value.' - riv '.
// 				$rate_impost_value.' - rv '.
// 				$rate_value.' - d '.
// 				$due.' - b'.
// 				$balance;


				if ($_POST['buildPerm']=='yes'){
					$buildPerm=1;}
				elseif ($_POST['buildPerm']=='no'){
					$buildPerm=0;
				}
				//normal update of existing details
				if ($addDetails=='')
				{
					$q = mysql_query(" SELECT 	*
										FROM 	`property`
										WHERE 	`upn` = '".$upn."' AND
												`subupn` = '".$subupn."' ;");

					$count = mysql_num_rows($q);
					$r = mysql_fetch_array($q);
 					if($r === FALSE) {
 					    die(mysql_error()); // TODO: better error handling
 					}

					if( !empty($r) )
					{
							 	/*
						 * previous year
						 */
						// DUE, PAYMENT, BALANCE
						$revenueBalancePrevious = $Data->getBalanceInfo( $upn, $subupn, $districtid, $previousYear, $type, "balance" );
						$revenueCollectedPrevious = $Data->getBalanceInfo( $upn, $subupn, $districtid, $previousYear, $type, "paid" );
						$revenuePaymentCurrentYear = $Data->getLastPaymentInfo( $upn, $subupn, $districtid, $currentYear, $type, "payment_value" );
						$revenuePaymentPreviousYear = $Data->getLastPaymentInfo( $upn, $subupn, $districtid, $previousYear, $type, "payment_value" );

						/*
						 * current year
						 */
						// DUE, PAYMENT, BALANCE
						$revenueCollected = $Data->getBalanceInfo( $upn, $subupn, $districtid, $currentYear, $type, "paid" );

						// calculations
						$revenuePaid = $revenueCollected + $revenueCollectedPrevious;  // current year
						$revenueBalance = $revenueBalancePrevious + $due - $revenueCollected;

// //check with BusinessRevenueCollection it is more complex than thought
// // I guess it is solved, but further tests will show
// 						if ($revenuePaid > 0){
// 						  $balance = $revenueBalance;
// 						  }
//

						$paid = $Data->getSumPaymentInfo( $upn, $subupn, $districtid, $year, $type = "property" );
						if ($paid == NULL || $paid == '') {
						 $paid = 0;
						}
//check with PropertyRevenueCollection it is more complex than thought

							$balance = $revenueBalancePrevious + $due - $paid;

// 						  $due = $balance;

	// debug
// 	echo '<br> ffc '.$feefi_code.
				' - ffv'.$feefi_value.
				' - riv '.$rate_impost_value.
				' - rv '.$rate_value.
				' - d '.$due.
				' - bo'.$revenueBalancePrevious.
				' - revbalOld'.$revenueBalanceOld.
				' - revbalance'.$revenueBalance.
				' - balance'.$balance.
				' - revColPrev'.$revenueCollectedPrevious.
				' - revCol'.$revenueCollected.
				' - paid'.$paid.
				' - paymentCurrentY'.$revenuePaymentCurrentYear.
				' - paymentPreviousY'.$revenuePaymentPreviousYear  ;


						//use pdo wrapper
						$update = array(
							'streetname' => $_POST['street'],
							'housenumber' => $_POST['Nr_'],
							'locality_code' => $_POST['localCode'],
							'owner' => $_POST['owner'],
							'owneraddress' => $_POST['ownAddress'],
							'owner_tel' => $_POST['ownTel'],
							'owner_email' => $_POST['ownEmail'],
							'property_use' => substr($_POST['propertyType'], 0, strpos($_POST['propertyType'], ':')-1),
							'prop_value' => $_POST['prop_value'],
							'buildingpermit' => $buildPerm,
							'buildingpermit_no' => $_POST['buildPermNo'],
							'feefi_code' => $feefi_code,
							'feefi_unit' => 'y',
							'feefi_value' => $feefi_value,
							'rate_impost_value' => $rate_impost_value,
							'rate_value' => $rate_value,
							'due' => $due,
							'paid' => $revenueCollected,
							'balance' => $balance,
							'excluded' => $excluded,
							'lastentry_person' => $_SESSION['user']['user'],
							'lastentry_date' => $today
							);
						$bind = array(
							":upn" => $upn,
							":subupn" => $subupn
						);
						$result = $pdo->update("property", $update, "upn = :upn AND subupn = :subupn", $bind);

						$bind = array(
							":upn" => $upn,
							":subupn" => $subupn,
							":districtid" => $districtid,
							":year" => $currentYear
						);
						$result = $pdo->update("property_due", $update, "upn = :upn AND subupn = :subupn AND districtid = :districtid AND year = :year", $bind);
						$result = $pdo->update("property_balance", $update, "upn = :upn AND subupn = :subupn  AND districtid = :districtid AND year = :year", $bind);

					}
				} elseif ($addDetails=='true') {	//we add a new property and need to update property_due, property_balance
// 				echo '<br>inside == true';
					$st = $pdo->prepare(" SELECT 	*
										FROM 	`property`
										WHERE 	`upn` = '".$upn."';");
				    $st->execute();
					$count = $st->rowCount();

				 if ($count==0)		//this is indeed a new property
				 	{
 	 					$subupn='';
 	 				}
 	 			elseif ($count==1)  //we have one property already, but no subupn
 	 				{
 						$subupn=$upn.chr(65);
						//use pdo wrapper
						$update = array(
							'subupn' => $subupn
							);
						$bind = array(
							":upn" => $upn,
						);
						$result = $pdo->update("property", $update, "upn = :upn", $bind);
						$result = $pdo->update("property_due", $update, "upn = :upn", $bind);
						$result = $pdo->update("property_balance", $update, "upn = :upn", $bind);

 						$subupn=$upn.chr(65+$count);

 	 				}
 	 			elseif ($count>1)	//more than one property and more than one subupn
 	 				{
 						$subupn=$upn.chr(65+$count);
					}

				//}
				 //use pdo wrapper
				    $insert = array(
						'upn' => $upn,
						'subupn' => $subupn,
						'districtid' => $districtid,
						'year' => $year,
						'pay_status' => 1,
						'colzone_id' => $r['colzone_id'],
				    	'streetname' => $_POST['street'],
						'housenumber' => $_POST['Nr_'],
						'locality_code' => $_POST['localCode'],
						'owner' => $_POST['owner'],
						'owneraddress' => $_POST['ownAddress'],
						'owner_tel' => $_POST['ownTel'],
						'owner_email' => $_POST['ownEmail'],
						'property_use' => $feefi_code,
						'prop_value' => $_POST['prop_value'],
						'buildingpermit' => $buildPerm,
						'buildingpermit_no' => $_POST['buildPermNo'],
						'feefi_code' => $feefi_code,
						'feefi_unit' => 'y',
						'feefi_value' => $feefi_value,
						'rate_impost_value' => $rate_impost_value,
						'rate_value' => $rate_value,
						'due' => $due,
						'paid' => $revenueCollected,
						'balance' => $balance,
						'comments' => $_POST['comments'],
						'lastentry_person' => $_SESSION['user']['user'],
						'lastentry_date' => $today
						);
					$result = $pdo->insert("property", $insert);
					$result = $pdo->insert("property_due", $insert);
					$result = $pdo->insert("property_balance", $insert);
				}
?>

			<h1>Form Submission Result</h1>

				 <table class='demoTbl' border='1' cellpadding='10' cellspacing='2'>
				<tr>
				<td colspan="2" bgcolor="#E6E6E6"><center><strong>Following Information was stored in the Database for UPN: <?php echo $_POST['upn'] ?></strong></center></td>
				</tr>
				<tr>
				<td colspan="2"><strong>Property Location</strong></td>
				</tr>
				<td>Street name</td><td><?php echo $_POST['street'] ?> </td>
				</tr>
				<tr>
				<td>Housenumber</td><td><?php echo $_POST['Nr_'] ?> </td>
				</tr>
				<tr>
				<td>Streetcode</td><td><?php echo $_POST['streetcode'] ?> </td>
				</tr>
				<tr>
				<td colspan="2"><strong>Owner Information</strong></td>
				</tr>
				<tr>
				<td>Name</td><td><?php echo $_POST['owner'] ?> </td>
				</tr>
				<tr>
				<td>Address</td><td><?php echo $_POST['ownAddress'] ?> </td>
				</tr>
				<tr>
				<td>Phone</td><td><?php echo $_POST['ownTel'] ?> </td>
				</tr>
				<tr>
				<td>Email</td><td><?php echo $_POST['ownEmail'] ?> </td>
				</tr>
				<tr>
				<td>Building permit available</td><td><?php echo $_POST['buildPerm'] ?> </td>
				</tr>
				<tr>
				<td>Building permit No.</td><td><?php echo $_POST['buildPermNo'] ?> </td>
				</tr>
				<tr>
				<td colspan="2"><strong>Property Information</strong></td>
				</tr>
				<tr>
				<td>Locality code</td><td><?php echo $_POST['localCode'] ?> </td>
				</tr>
				<tr>
				<td>Type of Property Use</td><td><?php echo $_POST['propertyType'] ?> </td>
				</tr>
				<tr>
				<td>Value</td><td><?php echo $_POST['prop_value'] ?> </td>
				</tr>
				<tr>
				<td>Excluded from rating</td><td><?php echo $_POST['excluded'] ?> </td>
				</tr>
				<tr>
				<td>Comments</td><td><?php echo $_POST['comments'] ?> </td>
				</tr>
				</table>
				<br><br>
				<p><input type="button" a href="javascript:;" onclick="window.close();" class="orange-flat-small" value="Close"></a></p>


<?php

//					echo  mysql_affected_rows();
				break;

				case "business":

					$feefi_code = substr($_POST['businessclass'], 0, strpos($_POST['businessclass'], ':')-1);
					$feefi_value = $Data->getFeeFixingInfo( $districtid, $feefi_code, $year, "business", "rate" );
					$due = $feefi_value;
					$balance = $due;

				//normal update of existing details
				if ($addDetails=='')
				{
					$q = mysql_query(" SELECT 	*
										FROM 	`business`
										WHERE 	`upn` = '".$upn."' AND
												`subupn` = '".$subupn."' ;");

					$count = mysql_num_rows($q);
					$r = mysql_fetch_array($q);
 					if($r === FALSE) {
 					    die(mysql_error()); // TODO: better error handling
 					}

					if( !empty($r) )
					{
// 						$paid = $Data->getSumPaymentInfo( $upn, $subupn, $districtid, $year, $type = "business" );
// 						if ($paid == NULL || $paid == '') {
// 						 $paid = 0;
// 						}
							 	/*
 	 * previous year
 	 */
 	// DUE, PAYMENT, BALANCE
 	$revenueBalancePrevious = $Data->getBalanceInfo( $upn, $subupn, $districtid, $previousYear, $type, "balance" );
 	$revenueCollectedPrevious = $Data->getBalanceInfo( $upn, $subupn, $districtid, $previousYear, $type, "paid" );

 	/*
 	 * current year
 	 */
 	// DUE, PAYMENT, BALANCE
 	$revenueCollected = $Data->getBalanceInfo( $upn, $subupn, $districtid, $currentYear, $type, "paid" );

 	// calculations
 	$revenuePaid = $revenueCollected + $revenueCollectedPrevious;  // current year
 	$revenueBalance = $revenueBalancePrevious + $due - $revenueCollected;

	// debug	echo '<br> ffc '.$feefi_code.' - ffv'.
					$feefi_value.' - riv '.
					$rate_impost_value.' - rv '.
					$rate_value.' - d '.
					$due.' - bo'.
					$revenueBalancePrevious.' - bal'.
				 	$revenueBalanceOld.' - balance'.
				 	$revenueBalance.' - po'.
					$revenueCollectedPrevious.' - p'.
					$revenueCollected  ;

//check with BusinessRevenueCollection it is more complex than thought
// I guess it is solved, but further tests will show
						if ($revenuePaid > 0){
						  $balance = $revenueBalance;
						  }

						//use pdo wrapper
						$update = array(
							'streetname' => $_POST['street'],
							'housenumber' => $_POST['Nr_'],
							'owner' => $_POST['owner'],
							'owneraddress' => $_POST['ownAddress'],
							'owner_tel' => $_POST['ownTel'],
							'owner_email' => $_POST['ownEmail'],
							'locality_code' => $_POST['localCode'],
							'business_name' => $_POST['businessname'],
							'business_class' => substr($_POST['businessclass'],0, strpos($_POST['businessclass'],':')-1),
							'da_no' => $_POST['daAssignmentNumber'],
							'business_certif' => $_POST['businessCertificate'],
							'employees' => $_POST['employees'],
							'year_establ' => $_POST['yearEstablishment'],
							'excluded' => $excluded,
							'feefi_code' => $feefi_code,
							'feefi_unit' => 'y',
							'feefi_value' => $feefi_value,
							'bo_impost_value' => $rate_impost_value,
							'bo_value' => $rate_value,
							'due' => $due,
							'paid' => $revenueCollected,
							'balance' => $balance,
							'comments' => $_POST['comments'],
							'lastentry_person' => $_SESSION['user']['user'],
							'lastentry_date' => $today
						);
						$bind = array(
							":upn" => $upn,
							":subupn" => $subupn
						);
						$result = $pdo->update("business", $update, "upn = :upn AND subupn = :subupn", $bind);

						$bind = array(
							":upn" => $upn,
							":subupn" => $subupn,
							":districtid" => $districtid,
							":year" => $currentYear

						);
						$result = $pdo->update("business_due", $update, "upn = :upn AND subupn = :subupn AND districtid = :districtid AND year = :year", $bind);
						$result = $pdo->update("business_balance", $update, "upn = :upn AND subupn = :subupn  AND districtid = :districtid AND year = :year", $bind);

					}
				} elseif ($addDetails=='true') {
					$st = $pdo->prepare(" SELECT 	*
					FROM 	`business`
					WHERE 	`upn` = '".$upn."';");
				    $st->execute();
					$count = $st->rowCount();

				 if ($count==0)
				 	{
 	 					$subupn='';
 	 				}
 	 			elseif ($count==1)
 	 				{
 						$subupn=$upn.'/'.$count;
						//use pdo wrapper
						$update = array(
							'subupn' => $subupn
							);
						$bind = array(
							":upn" => $upn,
						);
						$result = $pdo->update("business", $update, "upn = :upn", $bind);
						$result = $pdo->update("business_due", $update, "upn = :upn", $bind);
						$result = $pdo->update("business_balance", $update, "upn = :upn", $bind);

 						$subupn=$upn.'/'.($count+1);

 	 				}
 	 			elseif ($count>1)
 	 				{
 						$subupn=$upn.'/'.($count+1);
					}

				 //use pdo wrapper
				    $insert = array(
						'upn' => $upn,
						'subupn' => $subupn,
						'districtid' => $districtid,
						'year' => $year,
						'pay_status' => 1,
						'colzone_id' => $r['colzone_id'],
				    	'streetname' => $_POST['street'],
						'housenumber' => $_POST['Nr_'],
						'locality_code' => $_POST['localCode'],
						'da_no' => $_POST['daAssignmentNumber'],
						'business_certif' => $_POST['businessCertificate'],
						'employees' => $_POST['employees'],
						'business_name' => $_POST['businessname'],
						'year_establ' => $_POST['yearEstablishment'],
						'owner' => $_POST['owner'],
						'owneraddress' => $_POST['ownAddress'],
						'owner_tel' => $_POST['ownTel'],
						'owner_email' => $_POST['ownEmail'],
						'business_class' => substr($_POST['businessclass'],0, strpos($_POST['businessclass'],':')-1),
						'excluded' => $excluded,
						'feefi_code' => $feefi_code,
						'feefi_unit' => 'y',
						'feefi_value' => $feefi_value,
						'bo_impost_value' => $rate_impost_value,
						'bo_value' => $rate_value,
						'due' => $due,
						'paid' => $revenueCollected,
						'balance' => $balance,
						'comments' => $_POST['comments'],
						'lastentry_person' => $_SESSION['user']['user'],
						'lastentry_date' => $today
						);
					$result = $pdo->insert("business", $insert);
					$result = $pdo->insert("business_due", $insert);
					$result = $pdo->insert("business_balance", $insert);

				}

//					return mysql_affected_rows();

?>
				<h1>Form Submission Result</h1>

				<table class='demoTbl' border='1' cellpadding='10' cellspacing='2'>
				<tr>
				<td colspan="2" bgcolor="#E6E6E6"><center><strong>Following Information was stored in the Database for UPN: <?php echo $_POST['upn'] ?></strong></center></td>
				</tr>
				<tr>
				<td colspan="2"><strong>Business Location</strong></td>
				</tr>
				<td>Street name</td><td><?php echo $_POST['street'] ?> </td>
				</tr>
				<tr>
				<td>Housenumber</td><td><?php echo $_POST['Nr_'] ?> </td>
				</tr>
				<tr>
				<td>Streetcode</td><td><?php echo $_POST['streetcode'] ?> </td>
				</tr>
				<tr>
				<td colspan="2"><strong>Owner Information</strong></td>
				</tr>
				<tr>
				<td>Name</td><td><?php echo $_POST['owner'] ?> </td>
				</tr>
				<tr>
				<td>Address</td><td><?php echo $_POST['ownAddress'] ?> </td>
				</tr>
				<tr>
				<td>Phone</td><td><?php echo $_POST['ownTel'] ?> </td>
				</tr>
				<tr>
				<td>Email</td><td><?php echo $_POST['ownEmail'] ?> </td>
				</tr>
				<tr>
				<td>Locality code</td><td><?php echo $_POST['localCode'] ?> </td>
				</tr>
				<tr>
				<td colspan="2"><strong>Business Information</strong></td>
				</tr>
				<tr>
				<td>Business name</td><td><?php echo $_POST['businessname'] ?> </td>
				</tr>
				<tr>
				<td>Business class</td><td><?php echo $_POST['businessclass'] ?> </td>
				</tr>
				<tr>
				<td>DA assigned number</td><td><?php echo $_POST['daAssignmentNumber'] ?> </td>
				</tr>
				<tr>
				<td>Business certificate</td><td><?php echo $_POST['businessCertificate'] ?> </td>
				</tr>
				<tr>
				<td>Employees</td><td><?php echo $_POST['employees'] ?> </td>
				</tr>
				<tr>
				<td>Year of establishment</td><td><?php echo $_POST['yearEstablishment'] ?> </td>
				</tr>
				<tr>
				<td>Comments</td><td><?php echo $_POST['comments'] ?> </td>
				</tr>
				</table>
				<br><br>
				<p><input type="button" a href="javascript:;" onclick="window.close();" class="orange-flat-small" value="Close"></a></p>
<?php

				break;

				default:
					echo "Your type of entity 'property' or 'business' is not set!";
			}
?>

<p>&nbsp;</p>
</body>
</html>