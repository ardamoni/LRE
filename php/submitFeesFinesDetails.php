<?php
//    require_once("../lib/initialize.php");

error_reporting(E_ALL);
set_time_limit(0);
ob_start(); // prevent adding duplicate data with refresh (F5)
session_start();

if (!$_SESSION['user']['user']){
	$message = 'Your session has expired. You will be logged off. Please log in again!';
  echo "<script type='text/javascript'>alert('$message');</script>";}

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
// require_once( "../lib/Revenue.php"			);
//
// $Data = new Revenue;
$System = new System;


$upn=$_POST['upn'];
$streetcode=$_POST['streetcode'];
$districtid=$_POST['districtid'];
$address = $_POST['address'];
$addDetails = $_POST['addDetails'];
$today = date("Y/m/d");
$year = date("Y");

	$currentYear = $System->GetConfiguration("RevenueCollectionYear");
	$previousYear =  $currentYear -1;

//  echo "<pre>";
if (isset($addDetails)){
//  var_dump($_POST);
}else{echo 'not set';}
// var_dump($_SESSION);
// echo "</pre>";

$username = $_SESSION['user']['user'];

				if ($addDetails=='')
				{
						//use pdo wrapper
						$update = array(
							'comments' => $_POST['comments'],
							'lastentry_person' => $_SESSION['user']['user'],
							'lastentry_date' => $today
							);
						$bind = array(
							":upn" => $upn,
							":districtid" => $districtid
						);

						$result = $pdo->update("fees_fines", $update, "upn = :upn AND districtid = :districtid", $bind);

				} elseif ($addDetails=='true') {	//we add a new property and need to update property_due, property_balance
// 				echo '<br>inside == true';

				 //use pdo wrapper
				    $insert = array(
						'upn' => $upn,
						'districtid' => $districtid,
						'colzone_id' => $_POST['colzone_id'],
				    	'address' => $_POST['address'],
						'streetcode' => $_POST['streetcode'],
						'comments' => $_POST['comments'],
						'lastentry_person' => $_SESSION['user']['user'],
						'lastentry_date' => $today
						);
					$result = $pdo->insert("fees_fines", $insert);
				}
?>

			<h1>Form Submission Result</h1>

				 <table class='demoTbl' border='1' cellpadding='10' cellspacing='2'>
				<tr>
				<td colspan="2" bgcolor="#E6E6E6"><center><strong>Following Information was stored in the Database for UPN: <?php echo $_POST['upn'] ?></strong></center></td>
				</tr>
				<tr>
				<td colspan="2"><strong>Fees & Fines Location</strong></td>
				</tr>
				<td>District: </td><td><?php echo $_POST['district_name'] ?> </td>
				</tr>
				<tr>
				<td>Address:</td><td><?php echo $_POST['address'] ?> </td>
				</tr>
				<tr>
				<td>Collector Zone</td><td><?php echo $_POST['colzone_id'] ?> </td>
				</tr>
				<tr>
				<td>Comments</td><td><?php echo $_POST['comments'] ?> </td>
				</tr>
				</table>
				<br><br>
				<p><input type="button" a href="javascript:;" onclick="window.close();" class="orange-flat-small" value="Close"></a></p>


<p>&nbsp;</p>
</body>
</html>