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
<link rel="stylesheet" href="css/ex.css" type="text/css" />
</head>
<body>

<h1>Form Submission Result</h1>

<?php

	require_once( "../lib/configuration.php"	);

$upn=$_POST['upn'];
$subupn=$_POST['subupn'];
$type = $_POST['ifproperty'];

echo "<pre>";
var_dump($_POST);
var_dump($_SESSION);
echo "</pre>";

$username = $_SESSION['user']['user'];

		switch( $type ) 
			{
				case "property":
					$q = mysql_query(" SELECT 	* 
										FROM 	`property` 
										WHERE 	`upn` = '".$upn."' AND 
												`subupn` = '".$subupn."' ;");

					$r = mysql_fetch_array($q);
					if($r === FALSE) {
					    die(mysql_error()); // TODO: better error handling
					}
				if( !empty($r) ) 
				{
					mysql_query(" UPDATE 	`property` 
									SET 	`streetname` = '".$_POST['street']."', 
											`housenumber` = '".$_POST['Nr_']."',
 											`locality_code` = '".$_POST['localCode']."',
 											`owner` = '".$_POST['owner']."',
 											`owneraddress` = '".$_POST['ownAddress']."',
 											`owner_tel` = '".$_POST['ownTel']."',
 											`owner_email` = '".$_POST['ownEmail']."',
 											`property_use` = '".substr($_POST['propertyType'], 0, strpos($_POST['propertyType'], ':')-1)."',
 											`buildingpermit` = '".$_POST['buildPerm']."',
 											`buildingpermit_no` = '".$_POST['buildPermNo']."',
 											`lastentry_person` = '".$_SESSION['user']['user']."',
											`lastentry_date` = CURDATE()								
									WHERE 	`upn` = '".$upn."' AND
											`subupn` = '".$subupn."' ");	
				} 

					return  mysql_affected_rows();
				break;
				
				case "business":
					$q = mysql_query(" SELECT 	* 
										FROM 	`business` 
										WHERE 	`upn` = '".$upn."' AND 
												`subupn` = '".$subupn."' ;");
					$r = mysql_fetch_array($q);					
				if( !empty($r) ) 
				{
					mysql_query(" UPDATE 	`business` 
									SET 	`streetname` = '".$_POST['street']."', 
											`housenumber` = '".$_POST['Nr_']."',
											`locality_code` = '".$_POST['localCode']."',
											`da_no` = '".$_POST['daAssignmentNumber']."',
											`business_certif` = '".$_POST['businessCertificate']."',
											`employees` = '".$_POST['employees']."',
											`business_name` = '".$_POST['businessname']."',
											`year_establ` = '".$_POST['yearEstablishment']."',
											`owner` = '".$_POST['owner']."',
											`owneraddress` = '".$_POST['ownAddress']."',
											`owner_tel` = '".$_POST['ownTel']."',
											`owner_email` = '".$_POST['ownEmail']."',
											`business_class` = '".substr($_POST['businessclass'],0, strpos($_POST['businessclass'],':')-1)."',
											`lastentry_person` = '".$_SESSION['user']['name']."',
											`lastentry_date` = '".gmdate(DATE_RFC822)."'								
									WHERE 	`upn` = '".$upn."' AND
											`subupn` = '".$subupn."' ");	
				}

					return mysql_affected_rows();
				break;
			 
				default:
					return "Your type of entity is not set!";
			}			


?>

<p>&nbsp;</p>
</body>
</html>