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
<link rel="stylesheet" href="../css/ex.css" type="text/css" />
<link rel="stylesheet" href="../css/flatbuttons.css" type="text/css" />
<link rel="stylesheet" href="../lib/OpenLayers/theme/default/style.css" type="text/css">
<link rel="stylesheet" href="../css/styles.css" type="text/css">
<style type="text/css">
form.demoForm fieldset {
    width: 900px;
    margin-bottom: 1em;
	border-color:#ffcc00;
}

table.formTblContainer {
	width:100%;
    border-collapse: collapse;
    border-spacing: 0;
    border-color:#ffcc00;
}
/*
table.formTblContainer tr{
    border-color:#ffcc00;
}
table.formTblContainer td{
    border-color:#ffcc00;
}
 */

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

$upn = $_GET["upn"];
$subupn = $_GET["subupn"];
$districtid = $_GET["districtid"];
$addDetails = $_GET["addDetails"];


$username = $_SESSION['user']['name'];

$currentyear = $System->GetConfiguration("RevenueCollectionYear");

//var_dump($_GET);

if (!empty($subupn) && $subupn != "null" ){
//  if (!empty($subupn) || $subupn != "" || $subupn != null || $subupn != NULL || $subupn != "0" || strlen(trim($subupn))==0){
		echo "<h1>Enter property details for UPN: ".$upn." and SubUPN: ".$subupn."</h1>";
	}
	else {
		echo "<h1>Enter property details for UPN: ".$upn."</h1>";
	}

		$Data = new propertyDetailsClass;

//check whether new details are inserted or existing information is updated
if (isset($addDetails)){

		$conn = new PDO(cDsn, cUser, cPass);
		$stmt = $conn->prepare(" SELECT 	*
								FROM 	`KML_from_LUPMIS`
								WHERE 	`districtid` = :districtid AND
										`UPN` = :upn");
		if (!$stmt->execute(array('districtid' => $districtid,
									'upn'=>$upn)))
		  throw new Exception('[' . $stmt->errorCode() . ']: ' . $stmt->errorInfo());
		$localplaninfo = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$count = $stmt->rowCount();

		$r = array();
		$r['streetname'] = $localplaninfo[0]['Address'];
		$r['housenumber'] = '';
		$r['owner'] = '';
		$r['owneraddress'] = '';
		$r['owner_tel'] = '';
		$r['owner_email'] = '';
		$r['buildingpermit_no'] = '';
		$r['locality_code'] = '';
		$r["property_use"] = '';
		$r["prop_value"] = '';
		$r["comments"] = '';
		$r["excluded"] = '';
		$r["colzone_id"] = $localplaninfo[0]['colzone_id'];
   } else {
	//get the current database entries from property
   		$r = $Data->getPInfo( $upn, $subupn, $currentyear, $districtid ) ;
    } //end if (isset($addDetails)){
//var_dump($r);

 //check Planning Permit
 if (empty($r['planningpermit']))
 	{
 		$ppermityes='';
 		$ppermitno='';
 	} else {
 		if ($r['planningpermit']==1)
 		{ 	$ppermityes='1';
 			$ppermitno='0';}
 		else
 		{ 	$ppermityes='0';
 			$ppermitno='1';}
 	}

 //check Building Permit
 if (empty($r['buildingpermit']))
 	{
 		$bpermityes='';
 		$bpermitno='1';
 	} else {
 		if ($r['buildingpermit']==1)
 		{ 	$bpermityes='1';
 			$bpermitno='0';}
 		else
 		{ 	$bpermityes='0';
 			$bpermitno='1';}
 	}

 //var_dump($r);
//end get current db entries

//get the properyType from feefixing for the drop-down list
$run = "SELECT d1.class, d1.code, d1.rate from `fee_fixing_property` d1 WHERE d1.`districtid`='".$districtid."' AND d1.`year`='".$currentyear."' ORDER BY d1.`code`;";

$query 	= mysql_query($run);

$propertyType	= array();

while ($row = mysql_fetch_array($query)) {
	//check which property code is stored in property
    if ($row['code']==$r['property_use'])
    {
     $propuse = $row['code'].' : '.$row['class'].' : '.$row['rate'];
    }
	$json 		= $row['code'].' : '.$row['class'].' : '.$row['rate'];
	//put the list into the drop down list
	$propertyType[] 			= $json;
 }//end while


// arrays for select list
	//get property type info
	$helpertable = 'hlp_property_type';
	$ptypetext 	= $Data->getHelperText($helpertable);
			for( $i = 0; $i < count($ptypetext); $i++ )
			{
				if ($ptypetext[$i]['code']==$r['roofing'])
				{
				 $ptypeuse = $ptypetext[$i]['code'].' : '.$ptypetext[$i]['text'];
				}
			 $json = $ptypetext[$i]['code'].' : '.$ptypetext[$i]['text'];
			 $ptypelist[] = $json;
			}

//the coming information was taken out from the survey. However, we keep it here (commented out) if we want to include it later
// 	//get roofing info
// 	$helpertable = 'hlp_roofing';
// 	$roofingtext 	= $Data->getHelperText($helpertable);
// 			for( $i = 0; $i < count($roofingtext); $i++ )
// 			{
// 				if ($roofingtext[$i]['code']==$r['roofing'])
// 				{
// 				 $roofinguse = $roofingtext[$i]['code'].' : '.$roofingtext[$i]['text'];
// 				}
// 			 $json = $roofingtext[$i]['code'].' : '.$roofingtext[$i]['text'];
// 			 $roofinglist[] = $json;
// 			}
// 	//get ownership info
// 	$helpertable = 'hlp_property_ownership';
// 	$ownertext 	= $Data->getHelperText($helpertable);
// 			for( $i = 0; $i < count($ownertext); $i++ )
// 			{
// 				if ($ownertext[$i]['code']==$r['ownership_type'])
// 				{
// 				 $owneruse = $ownertext[$i]['code'].' : '.$ownertext[$i]['text'];
// 				}
// 			 $json = $ownertext[$i]['code'].' : '.$ownertext[$i]['text'];
// 			 $ownerlist[] = $json;
// 			}
// 	//$ownership 	= array('To do','No idea','Get info');
// 	//get construction material info
// 	$helpertable = 'hlp_construction_material';
// 	$cmtext 	= $Data->getHelperText($helpertable);
// 			for( $i = 0; $i < count($cmtext); $i++ )
// 			{
// 				if ($cmtext[$i]['code']==$r['constr_material'])
// 				{
// 				 $cmuse = $cmtext[$i]['code'].' : '.$cmtext[$i]['text'];
// 				}
// 			 $json = $cmtext[$i]['code'].' : '.$cmtext[$i]['text'];
// 			 $cmlist[] = $json;
// 			}
// 	//$constructMaterial = array('To do','No idea','Get info');
// 	$structure = array('To do','No idea','Get info');
// //end array for select list

$newcell = "<td>";
$endcell = "</td>";
$newrow = "<tr>";
$endrow= "</tr>";
// create instance of HTML_Form
$frm = new HTML_Form();
// using $frmStr to concatenate long string of form elements
// startForm arguments: action, method, id, optional attributes added in associative array
$frmStr = $frm->startForm('submitDetails.php', 'post', 'demoForm',
            array('class'=>'demoForm', 'onsubmit'=>'return checkBeforeSubmit(this)') ) . PHP_EOL .

    // fieldset and legend elements
    $frm->startTag('fieldset') . PHP_EOL .

    "<table class='formTblContainer'><tr><td>" .
		"<table class='formTbl' >" . $newrow . $newcell .


	   // wrap form elements in paragraphs
   	    '<strong>Property Location</strong>' . $endcell . $newcell . $endcell .$endrow .
		$frm->startTag('p') .
		// label and text input with optional attributes
		$frm->addLabelFor('street', $newcell.'Street name: '.$endcell) . $newcell.
		// using html5 required attribute
		$frm->addInput('text', 'street', $r['streetname'], array('id'=>'street', 'size'=>30, 'required'=>true) ).
		$frm->startTag('p') . PHP_EOL . $endcell . $endrow .
		// label and text input with optional attributes
		$frm->addLabelFor('housenumber', $newcell.'Housenumber: '.$endcell) . $newcell.
		// using html5 required attribute
		$frm->addInput('text', 'Nr.', $r['housenumber'], array('id'=>'housenumber', 'size'=>10, 'required'=>true) ) .
		$frm->startTag('p') . PHP_EOL . $endcell . $endrow .
		// label and text input with optional attributes
		$frm->addLabelFor('streetcode', $newcell.'Streetcode: '.$endcell) . $newcell.
		// using html5 required attribute
		$frm->addInput('text', 'streetcode', $r['locpl'], array('id'=>'streetcode', 'size'=>10, 'required'=>false) ) .

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL . $endcell . $endrow . $newrow . $newcell .

		$frm->startTag('p') .
		// label and text input with optional attributes
	    '<strong>Owner Information</strong>' . $endcell .$newcell . $endcell . $endrow .
		$frm->endTag('p') . PHP_EOL . $endcell . $endrow . $newrow . $newcell .
		$frm->addLabelFor('owner', 'Name: '.$endcell) . $newcell.
		// using html5 required attribute
		$frm->addInput('text', 'owner', $r['owner'], array('id'=>'owner', 'size'=>30, 'required'=>true) ) .

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL . $endcell . $endrow .

		$frm->startTag('p') .
		$frm->addLabelFor('ownAddress', $newcell.'Address: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'ownAddress', $r['owneraddress'], array('id'=>'ownAddress', 'size'=>30, 'required'=>false) ) .

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL . $endcell . $newrow .
		$frm->startTag('p') .

		$frm->addLabelFor('ownTel', $newcell.'Phone: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'ownTel', $r['owner_tel'], array('id'=>'ownTel', 'size'=>30, 'required'=>false) ) .

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL . $endcell. $endrow .
		$frm->startTag('p') .

		$frm->addLabelFor('ownEmail', $newcell.'Email: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'ownEmail', $r['owner_email'], array('id'=>'ownEmail', 'size'=>30, 'required'=>false) ) .

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow .

		$frm->startTag('p') .

		$frm->addLabelFor('buildPerm', $newcell.'Building permit available: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('radio', 'buildPerm', 'yes', array('id'=>'buildPerm', 'checked'=>$bpermityes)  ) . ' yes ' . PHP_EOL .
		$frm->addInput('radio', 'buildPerm', 'no', array('checked'=>$bpermitno)) . ' no' .

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow .

		$frm->startTag('p') .

		$frm->addLabelFor('buildPermNo', $newcell.'Building permit No.: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'buildPermNo', $r['buildingpermit_no'], array('id'=>'buildPermNo', 'size'=>30, 'required'=>false) ) .

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow .

		"</table>" .
    $newcell .
    		"<table width='10px'>" .
    			$newrow .
    		    $newcell .
			    $endcell .
			    $endrow .
			"</table>" .
    $endcell .
    $newcell .
		"<table class='formTbl'>" . $newrow . $newcell . //border='1' cellpadding='10' cellspacing='1' bgcolor='#FFFFFF'

		$frm->startTag('p') .
		'<strong>Property Information</strong>' . $endcell .$newcell . $endcell . $endrow .
		$frm->addLabelFor('localCode', $newcell.'Locality code: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'localCode', $r['locality_code'], array('id'=>'localCode', 'size'=>30, 'required'=>false) ) .

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow .

		$frm->startTag('selectlist') . $newcell.'Type of Property Use: ' .$endcell.$newcell.
		// arguments: name, array containing option text/values
		// include values attributes (boolean),
		// optional arguments: selected value, header, additional attributes in associative array
		$frm->addSelectList('propertyType', $propertyType, false, $propuse, '' ,array('id'=>'propertyType', 'style'=>'width: 200px', 'required'=>true) ) .
		$frm->endTag('selectlist') . $endcell. $endrow .

   		$frm->startTag('p') .

		$frm->addLabelFor('prop_value', $newcell.'Value: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'prop_value', $r['prop_value'], array('id'=>'prop_value', 'size'=>30, 'required'=>false) ) .

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow .

   		$frm->startTag('p') .

		$frm->addLabelFor('rooms', $newcell.'No of Rooms: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'rooms', $r['rooms'], array('id'=>'rooms', 'size'=>30, 'required'=>false) ) .

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow .

		$frm->startTag('p') .
		// contain checkbox with label using start/endTag (so no need to add id)
		$frm->startTag('label') . $newcell.'Excluded from rating: ' .$endcell.$newcell.
		$frm->addInput('checkbox', 'excluded', 'true' ) .
		$frm->addInput('hidden', 'ifproperty', 'property', array('id'=>'property', 'size'=>30, 'required'=>true) ) .
		$frm->addInput('hidden', 'upn', $upn, array('id'=>'upn', 'size'=>30, 'required'=>true) ) .
		$frm->addInput('hidden', 'subupn', $subupn, array('id'=>'subupn', 'size'=>30, 'required'=>true) ) .
		$frm->addInput('hidden', 'districtid', $districtid, array('id'=>'districtid', 'size'=>30, 'required'=>true) ) .
		$frm->addInput('hidden', 'username', $username, array('id'=>'username', 'size'=>30, 'required'=>true) ) .
		$frm->addInput('hidden', 'addDetails', $addDetails, array('id'=>'addDetails', 'size'=>30, 'required'=>true) ) .
		$frm->addInput('hidden', 'colzone_id', $r["colzone_id"], array('id'=>'colzone_id', 'size'=>30, 'required'=>true) ) .
// wouldn't need to pass label to endTag
		$frm->endTag('label') . $endcell. $endrow . "</td></tr>" .
		"</table>" .
		$endcell .
		$endrow .
	"</table>" .

    $frm->addEmptyTag('br') . PHP_EOL .

    $frm->startTag('p') .
    $frm->addLabelFor('comments', 'Comments: ') .
    $frm->addEmptyTag('br') . PHP_EOL .
    // using html5 placeholder attribute
    $frm->addTextArea('comments', 6, 40, $r['comments'],
            array('id'=>'comments', 'placeholder'=>'Enter any other information.') ) .

//    $frm->endTag('p') . PHP_EOL .

/*    $frm->startTag('p') .
    $frm->addLabelFor('comments', $newcell.'Your comments: ') .$endcell.$newcell.
//    $frm->addEmptyTag('br') . PHP_EOL .
    // using html5 placeholder attribute
    $frm->addTextArea('comments', 6, 40, '',
            array('id'=>'comments', 'placeholder'=>'We would love to hear your comments.') ) .

    $frm->endTag() . PHP_EOL .$endcell. $endrow . "</td></tr>" . "</table>" . "</table>" .
*/

    $frm->endForm();

//     $frm->startTag('submit') .
//     $frm->addInput('submit', 'submit', 'Submit') .
//     $frm->endTag('submit') . PHP_EOL .
//     $frm->endTag('fieldset') . PHP_EOL .


// finally, output the long string
echo $frmStr;

//and here comes the SUBMIT button
if ($_SESSION['user']['roleid'] < 100) {
echo '<input type="submit" id="Submit" name="Submit" value="Submit"  class="orange-flat-small"/>';
echo '<br><br>';
}
echo '<p><input type="button" a href="javascript:;" onclick="window.close();" class="orange-flat-small" value="Cancel"></a></p>';


?>

</body>
</html>