<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Property Details Form</title>
<link rel="stylesheet" href="../css/ex.css" type="text/css" />
<link rel="stylesheet" href="../lib/OpenLayers/theme/default/style.css" type="text/css">
<link rel="stylesheet" href="../style.css" type="text/css">
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

table.formTbl {
    border-collapse: collapse;
    border-spacing: 0;
    border-color:#ffcc00;
}
table.formTbl tr td {
	border: 1px solid #ccc;
	border-color:#ffcc00;
	width: 300px;
	height: 3em;
	font-size:1em;
	text-align:left;
	padding:5px;
}

form.demoForm p {font-size:0.875em;}

form.demoForm submit {
	font-size:1em;
	float:right;}

form.demoForm lable {font-size:0.5em;}

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

$upn = $_GET["upn"];
$subupn = $_GET["subupn"];	
if (empty($subupn)){
	echo "<h1>Enter property details for UPN: ".$upn."</h1>";
}
else {
	echo "<h1>Enter property details for UPN: ".$upn." and SubUPN: ".$subupn."</h1>";
}

//get the properyType from feefixing for the drop-down list
$run = "SELECT d1.class from `fee_fixing_property` d1 WHERE d1.`districtid` = '130';"; //ND d1.`districtid`='".$districtid."';";

$query 	= mysql_query($run);

$propertyType	= array();

while ($row = mysql_fetch_array($query)) {
	$json 		= $row['class'];
	$propertyType[] 			= $json;
 }//end while

// arrays for select list 
// propertyUse is currently !! Hardcoded !!, but we would need a helper table to get it from
$propertyUse = array('Artisan production', 'Commercial', 'Fuel station', 'Hotel / restaurant', 'JHS', 'Market', 'Parks', 'Place of worship', 'Police station', 'Post office', 'Public administration', 'Residential', 'Residential high density');
$roofing 	= array('Metal','Shingle','Concrete', 'Gras');
$ownership 	= array('To do','No idea','Get info');
$constructMaterial = array('To do','No idea','Get info');
$structure = array('To do','No idea','Get info');

$newcell = "<td>";
$endcell = "</td>";
$newrow = "<tr>";
$endrow= "</tr>";

// create instance of HTML_Form
$frm = new HTML_Form();

// using $frmStr to concatenate long string of form elements
// startForm arguments: action, method, id, optional attributes added in associative array
$frmStr = $frm->startForm('propertyDetails.php', 'post', 'demoForm',
            array('class'=>'demoForm', 'onsubmit'=>'return checkBeforeSubmit(this)') ) . PHP_EOL .
    
    // fieldset and legend elements
    $frm->startTag('fieldset') . PHP_EOL .
//    $frm->startTag('legend') . 'Detail data for UPN: INSERT $GET UPN' . $frm->endTag() . PHP_EOL .
    "<table class='formTblContainer'><tr><td>" .
    "<table class='formTbl' border='1' cellpadding='10' cellspacing='1' bgcolor='#FFFFFF'>" . $newrow . $newcell .

   
   // wrap form elements in paragraphs 
    $frm->startTag('p') .    
    // label and text input with optional attributes
    $frm->addLabelFor('owner', 'Owner: '.$endcell) . $newcell. 
    // using html5 required attribute
    $frm->addInput('text', 'owner', '', array('id'=>'owner', 'size'=>16, 'required'=>true) ) . 

    // endTag remembers startTag (but you can pass tag if nesting or for clarity)
    $frm->endTag('p') . PHP_EOL . $endcell . $endrow .

    $frm->startTag('p') . 
    $frm->addLabelFor('ownAddress', $newcell.'Owner\'s adress: '.$endcell) .$newcell.
    // using html5 required attribute
    $frm->addInput('text', 'ownAddress', '', array('id'=>'ownAddress', 'size'=>16, 'required'=>true) ) . 

    // endTag remembers startTag (but you can pass tag if nesting or for clarity)
    $frm->endTag('p') . PHP_EOL . $endcell . $newrow .
    $frm->startTag('p') . 

    $frm->addLabelFor('ownTel', $newcell.'Owner\'s phone: '.$endcell) .$newcell.
    // using html5 required attribute
    $frm->addInput('text', 'ownTel', '', array('id'=>'ownTel', 'size'=>16, 'required'=>true) ) . 
    
    // endTag remembers startTag (but you can pass tag if nesting or for clarity)
    $frm->endTag('p') . PHP_EOL . $endcell. $endrow .
    $frm->startTag('p') . 

    $frm->addLabelFor('ownEmail', $newcell.'Owner\'s email: '.$endcell) .$newcell.
    // using html5 required attribute
    $frm->addInput('text', 'ownEmail', '', array('id'=>'ownEmail', 'size'=>16, 'required'=>true) ) . 

    // endTag remembers startTag (but you can pass tag if nesting or for clarity)
    $frm->endTag('p') . PHP_EOL .  $endcell. $endrow .

    $frm->startTag('p') . 

    $frm->addLabelFor('regNo', $newcell.'Registration No.: '.$endcell) .$newcell.
    // using html5 required attribute
    $frm->addInput('text', 'regNo', '', array('id'=>'regNo', 'size'=>16, 'required'=>true) ) . 

    // endTag remembers startTag (but you can pass tag if nesting or for clarity)
    $frm->endTag('p') . PHP_EOL .  $endcell. $endrow .

    $frm->startTag('p') . 

    $frm->addLabelFor('planPerm', $newcell.'Planning permit available: '.$endcell) .$newcell.
    // using html5 required attribute
    $frm->addInput('radio', 'permit', 'yes', array('id'=>'permit')  ) . ' yes ' . PHP_EOL .
    $frm->addInput('radio', 'permit', 'no') . ' no' . 

    // endTag remembers startTag (but you can pass tag if nesting or for clarity)
    $frm->endTag('p') . PHP_EOL .  $endcell. $endrow .

    $frm->startTag('p') . 

    $frm->addLabelFor('planPermNo', $newcell.'Planning permit No.: '.$endcell) .$newcell.
    // using html5 required attribute
    $frm->addInput('text', 'planPermNo', '', array('id'=>'planPermNo', 'size'=>16, 'required'=>true) ) . 

    // endTag remembers startTag (but you can pass tag if nesting or for clarity)
    $frm->endTag('p') . PHP_EOL .  $endcell. $endrow .

    $frm->startTag('p') . 

    $frm->addLabelFor('buildPerm', $newcell.'Building permit available: '.$endcell) .$newcell.
    // using html5 required attribute
    $frm->addInput('radio', 'buildPerm', 'yes', array('id'=>'buildPerm')  ) . ' yes ' . PHP_EOL .
    $frm->addInput('radio', 'buildPerm', 'no') . ' no' . 

    // endTag remembers startTag (but you can pass tag if nesting or for clarity)
    $frm->endTag('p') . PHP_EOL .  $endcell. $endrow .

    $frm->startTag('p') . 

    $frm->addLabelFor('buildPermNo', $newcell.'Building permit No.: '.$endcell) .$newcell.
    // using html5 required attribute
    $frm->addInput('text', 'buildPermNo', '', array('id'=>'buildPermNo', 'size'=>16, 'required'=>true) ) . 

    // endTag remembers startTag (but you can pass tag if nesting or for clarity)
    $frm->endTag('p') . PHP_EOL .  $endcell. $endrow .

    $frm->startTag('p') . 
    $frm->addLabelFor('localCode', $newcell.'Locality code: '.$endcell) .$newcell.
    // using html5 required attribute
    $frm->addInput('text', 'localCode', '', array('id'=>'localCode', 'size'=>16, 'required'=>true) ) . 

    // endTag remembers startTag (but you can pass tag if nesting or for clarity)
    $frm->endTag('p') . PHP_EOL .  $endcell. $endrow . 

    $frm->startTag('label') . $newcell .'Property use: ' .$endcell .$newcell. 
    // arguments: name, array containing option text/values
    // include values attributes (boolean),
    // optional arguments: selected value, header, additional attributes in associative array
    $frm->addSelectList('propertyUse', $propertyUse, false, 'Residential high density' ) .
    $frm->endTag('label') . $endcell. $endrow . 
    
    "</table>" . 
    $newcell .
    $newcell .
    "<table class='formTbl' border='1' cellpadding='10' cellspacing='1' bgcolor='#FFFFFF'>" . $newrow . $newcell .
    

    $frm->startTag('label') .'Structure: ' .$endcell.$newcell. 
    // arguments: name, array containing option text/values
    // include values attributes (boolean),
    // optional arguments: selected value, header, additional attributes in associative array
    $frm->addSelectList('structure', $structure, false, 'To do' ) .
    $frm->endTag('label') . $endcell. $endrow . 

    $frm->startTag('p') . 
    $frm->addLabelFor('rooms', $newcell.'Rooms: '.$endcell) .$newcell.
    // using html5 required attribute
    $frm->addInput('text', 'rooms', '', array('id'=>'rooms', 'size'=>4, 'required'=>true) ) . 
    // endTag remembers startTag (but you can pass tag if nesting or for clarity)
    $frm->endTag('p') . PHP_EOL .  $endcell. $endrow . 

    $frm->startTag('p') . 
    $frm->addLabelFor('yearConstruct', $newcell.'Year of Construction: '.$endcell) .$newcell.
    // using html5 required attribute
    $frm->addInput('text', 'yearConstruct', '', array('id'=>'yearConstruct', 'size'=>4, 'required'=>true) ) . 
    // endTag remembers startTag (but you can pass tag if nesting or for clarity)
    $frm->endTag('p') . PHP_EOL .  $endcell. $endrow . 

    $frm->startTag('label') . $newcell.'Type of Property: ' .$endcell.$newcell. 
    // arguments: name, array containing option text/values
    // include values attributes (boolean),
    // optional arguments: selected value, header, additional attributes in associative array
    $frm->addSelectList('propertyType', $propertyType, false, 'Residential - Class 1 Cement/Burnt Bricks' ) .
    $frm->endTag('label') . $endcell. $endrow . 

    $frm->startTag('p') . 
    $frm->addLabelFor('personsInBuilding', $newcell.'No. of Persons in Building: '.$endcell) .$newcell.
    // using html5 required attribute
    $frm->addInput('text', 'personsInBuilding', '', array('id'=>'personsInBuilding', 'size'=>2, 'required'=>true) ) . 
    // endTag remembers startTag (but you can pass tag if nesting or for clarity)
    $frm->endTag('p') . PHP_EOL .  $endcell. $endrow . 

    $frm->startTag('label') . $newcell.'Type of Roofing: ' .$endcell.$newcell. 
    // arguments: name, array containing option text/values
    // include values attributes (boolean),
    // optional arguments: selected value, header, additional attributes in associative array
    $frm->addSelectList('roofing', $roofing, false, 'Metal' ) .
    $frm->endTag('label') . $endcell. $endrow . 

    $frm->startTag('label') . $newcell.'Type of Ownership: ' .$endcell.$newcell. 
    // arguments: name, array containing option text/values
    // include values attributes (boolean),
    // optional arguments: selected value, header, additional attributes in associative array
    $frm->addSelectList('ownership', $ownership, false, 'To do' ) .
    $frm->endTag('label') . $endcell. $endrow . 

    $frm->startTag('label') . $newcell.'Type of Construction material: ' .$endcell.$newcell. 
    // arguments: name, array containing option text/values
    // include values attributes (boolean),
    // optional arguments: selected value, header, additional attributes in associative array
    $frm->addSelectList('constructMaterial', $constructMaterial, false, 'To do' ) .
    $frm->endTag('label') . $endcell. $endrow . 

    $frm->startTag('p') . 
    $frm->addLabelFor('storeys', $newcell.'No. of Storeys: '.$endcell) .$newcell.
    // using html5 required attribute
    $frm->addInput('text', 'storeys', '', array('id'=>'storeys', 'size'=>2, 'required'=>true) ) . 
    // endTag remembers startTag (but you can pass tag if nesting or for clarity)
    $frm->endTag('p') . PHP_EOL .  $endcell. $endrow . 
   
    $frm->startTag('p') .     
    // contain checkbox with label using start/endTag (so no need to add id)
    $frm->startTag('label') . $newcell.'Excluded from rating: ' .$endcell.$newcell.  
    $frm->addInput('checkbox', 'excluded', 'true' ) .
    // wouldn't need to pass label to endTag
    $frm->endTag('label') . $endcell. $endrow . "</td></tr>" . "</table>" . "</table>" .
    
    $frm->startTag('p') . 
    $frm->addLabelFor('comments', 'Comments: ') .
    $frm->addEmptyTag('br') . PHP_EOL .
    // using html5 placeholder attribute
    $frm->addTextArea('comments', 6, 40, '',
            array('id'=>'comments', 'placeholder'=>'Enter any other information.') ) . 
    
//    $frm->endTag() . PHP_EOL . 

/*    $frm->startTag('p') . 
    $frm->addLabelFor('comments', $newcell.'Your comments: ') .$endcell.$newcell. 
//    $frm->addEmptyTag('br') . PHP_EOL .
    // using html5 placeholder attribute
    $frm->addTextArea('comments', 6, 40, '',
            array('id'=>'comments', 'placeholder'=>'We would love to hear your comments.') ) . 
    
    $frm->endTag() . PHP_EOL .$endcell. $endrow . "</td></tr>" . "</table>" . "</table>" .
*/
    $frm->startTag('submit') .    
    $frm->addInput('submit', 'submit', 'Submit') .
    $frm->endTag('submit') . PHP_EOL .
    $frm->endTag('fieldset') . PHP_EOL .
    
    $frm->endForm();

// finally, output the long string
echo $frmStr;


?>

</body>
</html>