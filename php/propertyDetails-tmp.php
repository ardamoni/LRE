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
// require_once('../lib/scanDataPClass.php');
// require_once('../lib/scanDataBClass.php');

echo "<pre>";
var_dump($_POST);
echo "</pre>";

// global $sdBusiness;
// global $sdProperty;

$upn=$_POST['upn'];
$subupn=$_POST['subupn'];

$funnyChar = array('|'=>'1','I'=>'1','S'=>'5','O'=>'0','s'=>'5','o'=>'0','i'=>'1','D'=>'0', ' '=>'');

if ($_POST['ifproperty']=='1'){	    
	$targetTable = $sdProperty->tell_table_name();
}elseif ($_POST['ifproperty']=='0'){
	$targetTable = $sdBusiness->tell_table_name();
}

    echo $sdProperty->find_by_upn_subupn($upn, $subupn);
	//upload property data
	if ($_POST['ifproperty']=='1'){	    
		$sdProperty->streetname=$_POST['street'];
		$sdProperty->housenumber=$_POST['Nr_'];
		$sdProperty->locality_code=$_POST['localCode'];
		$sdProperty->owner=$_POST['owner'];
		$sdProperty->owneraddress=$_POST['ownAddress'];
		$sdProperty->owner_tel=$_POST['ownTel'];
		$sdProperty->owner_email=$_POST['ownEmail'];
		$sdProperty->property_use=substr($_POST['propertyType'], 0, strpos($_POST['propertyType'], ':')-2);
		$sdProperty->buildingpermit=$_POST['buildPerm'];
		$sdProperty->buildingpermit_no=$_POST['buildPermNo'];
		$sdProperty->lastentry_person=$_SESSION['user']['name'];
		$sdProperty->lastentry_date=gmdate(DATE_RFC822);
//		$sdProperty->comments='Uploaded by: '.$_SESSION['user']['name'].' - at: '.gmdate(DATE_RFC822).' - Comment: '.$cellTemp['J'];
//Ascii Character 65=A to 90=Z
		$sdProperty->save(); 
		unset($sdProperty->id);
	}
	//upload business data
	elseif ($_POST['ifproperty']=='0'){
		$sdBusiness->streetname=$_POST['street'];
		$sdBusiness->housenumber=$_POST['Nr_'];
		$sdBusiness->locality_code=$_POST['localCode'];
		$sdBusiness->da_no=$_POST['daAssignmentNumber'];
		$sdBusiness->business_certif=$_POST['businessCertificate'];
		$sdBusiness->employees=$_POST['employees'];
		$sdBusiness->business_name=$_POST['businessname'];
		$sdBusiness->year_establ=$_POST['yearEstablishment'];
		$sdBusiness->owner=$_POST['owner'];
		$sdBusiness->owneraddress=$_POST['ownAddress'];
		$sdBusiness->owner_tel=$_POST['ownTel'];
		$sdBusiness->owner_email=$_POST['ownEmail'];
		$sdBusiness->business_class=substr($_POST['businessclass'],0, strpos($_POST['businessclass'],':')-2);
		$sdProperty->lastentry_person=$_SESSION['user']['name'];
		$sdProperty->lastentry_date=gmdate(DATE_RFC822);
//		$sdBusiness->comments='Uploaded by: '.$_SESSION['user']['name'].' - at: '.gmdate(DATE_RFC822).' - Comment: '.$cellTemp['J'];

			$sdBusiness->save();

 			unset($sdBusiness->id);
// 		//show table on screen
// 		foreach ($cellTemp as $key => $value){
// 			  echo "<td>" . $value . "</td>";
// 			  }
// 	   echo "</tr>";
	} //end elseif
// 	} //end if !empty(upn)
// }	// end function

/*
array(13) {
  ["street"]=>
  string(10) "AMPEM LANE"
  ["Nr_"]=>
  string(1) "5"
  ["streetcode"]=>
  string(50) "Uploaded by: Ekkehardt Roth - at: Thu, 03 Apr 14 0"
  ["owner"]=>
  string(12) "SARAH AGGREY"
  ["ownAddress"]=>
  string(0) ""
  ["ownTel"]=>
  string(0) ""
  ["ownEmail"]=>
  string(0) ""
  ["buildPerm"]=>
  string(2) "no"
  ["buildPermNo"]=>
  string(0) ""
  ["localCode"]=>
  string(9) "PHV-A-007"
  ["propertyType"]=>
  string(47) "102 : Residential - Class 2 Cement/Burnt Bricks"
  ["comments"]=>
  string(50) "Uploaded by: Ekkehardt Roth - at: Thu, 03 Apr 14 0"
  ["submit"]=>
  string(6) "Submit"
  lastentryperson
  lastentrydate
}*/
?>

<p>&nbsp;</p>
</body>
</html>