<?php
//    require_once("../lib/initialize.php");
session_start();

error_reporting(E_ALL);
set_time_limit(0);
ob_start(); // prevent adding duplicate data with refresh (F5)

date_default_timezone_set('Europe/London');

// var_dump($_SESSION);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Property Details Form</title>
<!-- <link rel="stylesheet" href="../css/ex.css" type="text/css" /> -->
<link rel="stylesheet" href="../css/flatbuttons.css" type="text/css" />
<link rel="stylesheet" href="../lib/OpenLayers/theme/default/style.css" type="text/css">
<link rel="stylesheet" href="../style.css" type="text/css">
<style type="text/css">
form.demoForm fieldset {
    width: 680px;
    margin-bottom: 1em;
	border-color:#ffcc00;
}

table.formTblContainer {
	width:100%;
    border-collapse: collapse;
    border-spacing: 0;
    border-color:#ffcc00;
}

table.formTbl {
    border-collapse: collapse;
    border-spacing: 0;
    border-color:#ffcc00;
}
table.formTbl tr  {
	border: 1px solid #ccc;
	border-color:#ffcc00;
	width: 300px;
	height: 3em;
	font-size:1em;
	text-align:left;
}
table.formTbl td  {
	width: 300px;
	font-size:1em;
	text-align:left;
	padding:5px;
}

form.demoForm p {font-size:1em;}

form.demoForm submit {
	font-size:1em;
	float:right;}

form.demoForm lable {font-size:0.5em;}

form.demoForm selectlist {width:50px;}

</style>


<script type="text/javascript">
function checkBeforeSubmit(frm) {
    // JavaScript validation here
    // return false if error(s)

    //alert('This is just a demo form with no place to go.');
    //return false;

    return true; // to submit
}

</script>
</head>
<body>


<?php
require_once('../lib/html_form.class.php');
require_once( "../lib/configuration.php"	);
require_once( "../lib/PropertyDetails.php"	);
require_once( 	"../lib/System.php"			);

$System = new System;
$Data = new propertyDetailsClass;

$upn = $_GET["upn"];
$subupn = $_GET["subupn"];
$districtid = $_GET["districtid"];
$addDetails = $_GET["addDetails"];


$username = $_SESSION['user']['name'];

$currentyear = $System->GetConfiguration("RevenueCollectionYear");

var_dump($_GET);

	echo "<h1>Enter fees & fines details for UPN: ".$upn."</h1>";

//check whether new details are inserted or existing information is updated
if (isset($addDetails)){
	 $r = $Data->getLPInfo( $upn);
	 $r['streetcode']='n.a.';
	 $address = $r['Address'];
// 		$r = array();
// 		$r['streetname'] = '';
// 		$r['housenumber'] = '';
// 		$r['owner'] = '';
// 		$r['owneraddress'] = '';
// 		$r['owner_tel'] = '';
// 		$r['owner_email'] = '';
// 		$r['buildingpermit_no'] = '';
// 		$r['locality_code'] = '';
// 		$r["property_use"] = '';
// 		$r["prop_value"] = '';
// 		$r["comments"] = '';
// 		$r["excluded"] = '';
   } else {
	//get the current database entries from property
   		$r = $Data->getFFInfo( $upn) ;
	 $address = $r['address'];
    } //end if (isset($addDetails)){
//var_dump($r);

$newcell = "<td>";
$endcell = "</td>";
$newrow = "<tr>";
$endrow= "</tr>";
// create instance of HTML_Form
$frm = new HTML_Form();
// using $frmStr to concatenate long string of form elements
// startForm arguments: action, method, id, optional attributes added in associative array
$frmStr = $frm->startForm('submitFeesFinesDetails.php', 'post', 'demoForm',
            array('class'=>'demoForm', 'onsubmit'=>'return checkBeforeSubmit(this)') ) . PHP_EOL .

    // fieldset and legend elements
    $frm->startTag('fieldset') . PHP_EOL .

//    "<table class='formTblContainer'><tr><td>" .
		"<center><table class='formTbl' >" . $newrow . $newcell .


	   // wrap form elements in paragraphs
   	    '<strong>Property Location</strong>' . $endcell . $newcell . $endcell .$endrow .
		$frm->startTag('p') .
		// label and text input with optional attributes
		$frm->addLabelFor('district', $newcell.'District: '.$endcell) . $newcell.
		'<strong>'.$r['district_name'].'</strong>'. PHP_EOL .$endcell.$endrow.
		$frm->addLabelFor('address', $newcell.'Address: '.$endcell) . $newcell.
		'<strong>'.$address.'</strong>'. PHP_EOL .$endcell.$endrow.
		$frm->addLabelFor('colzone_id', $newcell.'Collector Zone: '.$endcell) . $newcell.
		// using html5 required attribute
		'<strong>'.$r['colzone_id'].'</strong>'. PHP_EOL .
		$endcell . $endrow .
		$frm->addInput('hidden', 'addDetails', $addDetails, array('id'=>'addDetails', 'size'=>30, 'required'=>true) ) .
		$frm->addInput('hidden', 'upn', $upn, array('id'=>'upn', 'size'=>30, 'required'=>true) ) .
		$frm->addInput('hidden', 'address', $address, array('id'=>'address', 'size'=>30, 'required'=>true) ) .
		$frm->addInput('hidden', 'districtid', $r['districtid'], array('id'=>'districtid', 'size'=>30, 'required'=>true) ) .
		$frm->addInput('hidden', 'district_name', $r['district_name'], array('id'=>'district_name', 'size'=>30, 'required'=>true) ) .
		$frm->addInput('hidden', 'streetcode', $r['streetcode'], array('id'=>'streetcode', 'size'=>30, 'required'=>true) ) .
		$frm->addInput('hidden', 'colzone_id', $r['colzone_id'], array('id'=>'colzone_id', 'size'=>30, 'required'=>true) ) .

		"</table></center>" .

    $frm->addEmptyTag('br') . PHP_EOL .

    $frm->startTag('p') .
    $frm->addLabelFor('comments', 'Comments: ') .
    $frm->addEmptyTag('br') . PHP_EOL . '<center>'.
    // using html5 placeholder attribute
    $frm->addTextArea('comments', 6, 90, $r['comments'],
            array('id'=>'comments', 'placeholder'=>'Enter any other information.') ) .' </center>'.
    $frm->endForm();

// finally, output the long string
echo $frmStr;
echo '<script>document.getElementById("comments").focus();</script>';
//and here comes the SUBMIT button
if ($_SESSION['user']['roleid'] < 100) {
echo '<input type="submit" id="Submit" name="Submit" value="Submit"  class="orange-flat-small"/>';
echo '<br><br>';
}
echo '<p><input type="button" a href="javascript:;" onclick="window.close();" class="orange-flat-small" value="Cancel"></a></p>';

// echo "</td></tr></table>";
?>

</body>
</html>