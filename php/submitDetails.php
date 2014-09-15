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

require_once( "../lib/configuration.php"	);

$upn=$_POST['upn'];
$subupn=$_POST['subupn'];
$type = $_POST['ifproperty'];
$today = date("Y/m/d");
//  echo "<pre>";
//  var_dump($_POST);
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
				    if ($_POST['buildPerm']=='yes'){
				    $buildPerm=1;}
				    elseif ($_POST['buildPerm']=='no'){
				    $buildPerm=0;}
				    
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
						'buildingpermit' => $buildPerm,
						'buildingpermit_no' => $_POST['buildPermNo'],
						'excluded' => $excluded,
						'lastentry_person' => $_SESSION['user']['user'],
						'lastentry_date' => $today
						);
					$bind = array(
						":upn" => $upn,
						":subupn" => $subupn
					);
					$result = $pdo->update("property", $update, "upn = :upn AND subupn = :subupn", $bind);

				    
// 					mysql_query(" UPDATE 	`property` 
// 									SET 	`streetname` = '".$_POST['street']."', 
// 											`housenumber` = '".$_POST['Nr_']."',
//  											`locality_code` = '".$_POST['localCode']."',
//  											`owner` = '".$_POST['owner']."',
//  											`owneraddress` = '".$_POST['ownAddress']."',
//  											`owner_tel` = '".$_POST['ownTel']."',
//  											`owner_email` = '".$_POST['ownEmail']."',
//  											`property_use` = '".substr($_POST['propertyType'], 0, strpos($_POST['propertyType'], ':')-1)."',
//  											`buildingpermit` = '".$buildPerm."',
//  											`buildingpermit_no` = '".$_POST['buildPermNo']."',
//  											`excluded` = '".$excluded."',
//  											`lastentry_person` = '".$_SESSION['user']['user']."',
// 											`lastentry_date` = CURDATE()								
// 									WHERE 	`upn` = '".$upn."' AND
// 											`subupn` = '".$subupn."' ");	
				} 
?>

			<h1>Form Submission Result</h1>

				 <table class='demoTbl' border='1' cellpadding='10' cellspacing='2'>
				<tr>
				<td colspan="2" bgcolor="#E6E6E6"><center><strong>Following Information was stored in the Database</strong></center></td>
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
					$q = mysql_query(" SELECT 	* 
										FROM 	`business` 
										WHERE 	`upn` = '".$upn."' AND 
												`subupn` = '".$subupn."' ;");
					$r = mysql_fetch_array($q);					
				if( !empty($r) ) 
				{
								    //use pdo wrapper
				    $update = array(
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
						'lastentry_person' => $_SESSION['user']['user'],
						'lastentry_date' => $today
						);
					$bind = array(
						":upn" => $upn,
						":subupn" => $subupn
					);
					$result = $pdo->update("business", $update, "upn = :upn AND subupn = :subupn", $bind);

				
// 				mysql_query(" UPDATE 	`business` 
// 									SET 	`streetname` = '".$_POST['street']."', 
// 											`housenumber` = '".$_POST['Nr_']."',
// 											`locality_code` = '".$_POST['localCode']."',
// 											`da_no` = '".$_POST['daAssignmentNumber']."',
// 											`business_certif` = '".$_POST['businessCertificate']."',
// 											`employees` = '".$_POST['employees']."',
// 											`business_name` = '".$_POST['businessname']."',
// 											`year_establ` = '".$_POST['yearEstablishment']."',
// 											`owner` = '".$_POST['owner']."',
// 											`owneraddress` = '".$_POST['ownAddress']."',
// 											`owner_tel` = '".$_POST['ownTel']."',
// 											`owner_email` = '".$_POST['ownEmail']."',
// 											`business_class` = '".substr($_POST['businessclass'],0, strpos($_POST['businessclass'],':')-1)."',
//  											`excluded` = '".$excluded."',
//  											`lastentry_person` = '".$_SESSION['user']['user']."',
// 											`lastentry_date` = CURDATE()								
// 									WHERE 	`upn` = '".$upn."' AND
// 											`subupn` = '".$subupn."' ");	
				}

//					return mysql_affected_rows();
					
?>
				<h1>Form Submission Result</h1>

				<table class='demoTbl' border='1' cellpadding='10' cellspacing='2'>
				<tr>
				<td colspan="2" bgcolor="#E6E6E6"><center><strong>Following Information was stored in the Database</strong></center></td>
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