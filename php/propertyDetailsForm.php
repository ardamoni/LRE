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

$upn = $_GET["upn"];
$subupn = $_GET["subupn"];	
$districtid = $_GET["districtid"];	

$currentdate = getdate();
$currentyear = $currentdate['year'];

//var_dump($_GET);

if (!empty($subupn) && $subupn != "null" ){
//  if (!empty($subupn) || $subupn != "" || $subupn != null || $subupn != NULL || $subupn != "0" || strlen(trim($subupn))==0){
		echo "<h1>Enter property details for UPN: ".$upn." and SubUPN: ".$subupn."</h1>";
	}
	else {
		echo "<h1>Enter property details for UPN: ".$upn."</h1>";
	}

//get the current database entries from property
	$Data = new propertyDetailsClass;
    $r = $Data->getPInfo( $upn, $subupn, $currentyear, $districtid ) ;
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
 		$bpermitno='';
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
$run = "SELECT d1.class, d1.code from `fee_fixing_property` d1 WHERE d1.`districtid`='".$districtid."';";

$query 	= mysql_query($run);

$propertyType	= array();

while ($row = mysql_fetch_array($query)) {
	//check which property code is stored in property
    if ($row['code']==$r['property_use'])
    {
     $propuse = $row['code'].' : '.$row['class'];
    }
	$json 		= $row['code'].' : '.$row['class'];
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
	//get roofing info
	$helpertable = 'hlp_roofing';
	$roofingtext 	= $Data->getHelperText($helpertable);
			for( $i = 0; $i < count($roofingtext); $i++ ) 
			{		
				if ($roofingtext[$i]['code']==$r['roofing'])
				{
				 $roofinguse = $roofingtext[$i]['code'].' : '.$roofingtext[$i]['text'];
				}
			 $json = $roofingtext[$i]['code'].' : '.$roofingtext[$i]['text'];
			 $roofinglist[] = $json; 
			}
	//get ownership info
	$helpertable = 'hlp_property_ownership';
	$ownertext 	= $Data->getHelperText($helpertable);
			for( $i = 0; $i < count($ownertext); $i++ ) 
			{		
				if ($ownertext[$i]['code']==$r['ownership_type'])
				{
				 $owneruse = $ownertext[$i]['code'].' : '.$ownertext[$i]['text'];
				}
			 $json = $ownertext[$i]['code'].' : '.$ownertext[$i]['text'];
			 $ownerlist[] = $json; 
			}
	//$ownership 	= array('To do','No idea','Get info');
	//get construction material info
	$helpertable = 'hlp_construction_material';
	$cmtext 	= $Data->getHelperText($helpertable);
			for( $i = 0; $i < count($cmtext); $i++ ) 
			{		
				if ($cmtext[$i]['code']==$r['constr_material'])
				{
				 $cmuse = $cmtext[$i]['code'].' : '.$cmtext[$i]['text'];
				}
			 $json = $cmtext[$i]['code'].' : '.$cmtext[$i]['text'];
			 $cmlist[] = $json; 
			}
	//$constructMaterial = array('To do','No idea','Get info');
	$structure = array('To do','No idea','Get info');
//end array for select list

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

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL . $endcell . $endrow . $newrow . $newcell . 

		$frm->startTag('p') .    
		// label and text input with optional attributes
	    '<strong>Owner Information</strong>' . $endcell .$newcell . $endcell . $endrow .
		$frm->endTag('p') . PHP_EOL . $endcell . $endrow . $newrow . $newcell . 
		$frm->addLabelFor('owner', 'Owner: '.$endcell) . $newcell. 
		// using html5 required attribute
		$frm->addInput('text', 'owner', $r['owner'], array('id'=>'owner', 'size'=>30, 'required'=>true) ) . 

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL . $endcell . $endrow .

		$frm->startTag('p') . 
		$frm->addLabelFor('ownAddress', $newcell.'Owner\'s adress: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'ownAddress', $r['owneraddress'], array('id'=>'ownAddress', 'size'=>30, 'required'=>true) ) . 

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL . $endcell . $newrow .
		$frm->startTag('p') . 

		$frm->addLabelFor('ownTel', $newcell.'Owner\'s phone: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'ownTel', $r['owner_tel'], array('id'=>'ownTel', 'size'=>30, 'required'=>true) ) . 
	
		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL . $endcell. $endrow .
		$frm->startTag('p') . 

		$frm->addLabelFor('ownEmail', $newcell.'Owner\'s email: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'ownEmail', $r['owner_email'], array('id'=>'ownEmail', 'size'=>30, 'required'=>true) ) . 

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow .

		$frm->startTag('p') . 

		$frm->addLabelFor('regNo', $newcell.'Registration No.: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'regNo', $r['regnumber'], array('id'=>'regNo', 'size'=>30, 'required'=>true) ) . 

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow .

		$frm->startTag('p') . 

		$frm->addLabelFor('planPerm', $newcell.'Planning permit available: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('radio', 'permit', '1', array('id'=>'permit', 'checked'=>$ppermityes)  ) . ' yes ' . PHP_EOL .
		$frm->addInput('radio', 'permit', '0', array('checked'=>$ppermitno)) . ' no' . 

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow .

		$frm->startTag('p') . 

		$frm->addLabelFor('planPermNo', $newcell.'Planning permit No.: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'planPermNo', $r['planningpermit_no'], array('id'=>'planPermNo', 'size'=>30, 'required'=>true) ) . 

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
		$frm->addInput('text', 'buildPermNo', $r['buildingpermit_no'], array('id'=>'buildPermNo', 'size'=>30, 'required'=>true) ) . 

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
		$frm->addInput('text', 'localCode', $r['locality_code'], array('id'=>'localCode', 'size'=>30, 'required'=>true) ) . 

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow . 

		$frm->startTag('label') . $newcell .'Property Type: ' .$endcell .$newcell. 
		// arguments: name, array containing option text/values
		// include values attributes (boolean),
		// optional arguments: selected value, header, additional attributes in associative array
		$frm->addSelectList('propertyUse', $ptypelist, false, $ptypeuse ) .
		$frm->endTag('label') . $endcell. $endrow . 

		$frm->startTag('label') .$newcell.'Structure: ' .$endcell.$newcell. 
		// arguments: name, array containing option text/values
		// include values attributes (boolean),
		// optional arguments: selected value, header, additional attributes in associative array
		$frm->addSelectList('structure', $structure, false, 'To do' ) .
		$frm->endTag('label') . $endcell. $endrow . 

		$frm->startTag('p') . 
		$frm->addLabelFor('rooms', $newcell.'Rooms: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'rooms', $r['rooms'], array('id'=>'rooms', 'size'=>4, 'required'=>true) ) . 
		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow . 

		$frm->startTag('p') . 
		$frm->addLabelFor('yearConstruct', $newcell.'Year of Construction: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'yearConstruct', $r['year_construction'], array('id'=>'yearConstruct', 'size'=>4, 'required'=>true) ) . 
		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow . 

		$frm->startTag('selectlist') . $newcell.'Type of Property Use: ' .$endcell.$newcell. 
		// arguments: name, array containing option text/values
		// include values attributes (boolean),
		// optional arguments: selected value, header, additional attributes in associative array
		$frm->addSelectList('propertyType', $propertyType, false, $propuse, '' ,array('id'=>'propertyType', 'style'=>'width: 200px') ) .
		$frm->endTag('selectlist') . $endcell. $endrow . 

		$frm->startTag('p') . 
		$frm->addLabelFor('personsInBuilding', $newcell.'No. of Persons in Building: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'personsInBuilding', $r['persons'], array('id'=>'personsInBuilding', 'size'=>2, 'required'=>true) ) . 
		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow . 

		$frm->startTag('label') . $newcell.'Type of Roofing: ' .$endcell.$newcell. 
		// arguments: name, array containing option text/values
		// include values attributes (boolean),
		// optional arguments: selected value, header, additional attributes in associative array
		$frm->addSelectList('roofing', $roofinglist, false, $roofinguse ) .
		$frm->endTag('label') . $endcell. $endrow . 

		$frm->startTag('label') . $newcell.'Type of Ownership: ' .$endcell.$newcell. 
		// arguments: name, array containing option text/values
		// include values attributes (boolean),
		// optional arguments: selected value, header, additional attributes in associative array
		$frm->addSelectList('ownership', $ownerlist, false, $owneruse ) .
		$frm->endTag('label') . $endcell. $endrow . 

		$frm->startTag('label') . $newcell.'Type of Construction material: ' .$endcell.$newcell. 
		// arguments: name, array containing option text/values
		// include values attributes (boolean),
		// optional arguments: selected value, header, additional attributes in associative array
		$frm->addSelectList('constructMaterial', $cmlist, false, $cmuse ) .
		$frm->endTag('label') . $endcell. $endrow . 

		$frm->startTag('p') . 
		$frm->addLabelFor('storeys', $newcell.'No. of Storeys: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'storeys', $r['storeys'], array('id'=>'storeys', 'size'=>2, 'required'=>true) ) . 
		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow . 
   
		$frm->startTag('p') .     
		// contain checkbox with label using start/endTag (so no need to add id)
		$frm->startTag('label') . $newcell.'Excluded from rating: ' .$endcell.$newcell.  
		$frm->addInput('checkbox', 'excluded', 'true' ) .
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