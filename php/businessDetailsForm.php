<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Business Details Form</title>
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
require_once( "../lib/BusinessDetails.php"	);

$upn = $_GET["upn"];
$subupn = $_GET["subupn"];	
$districtid = $_GET["districtid"];	

$currentdate = getdate();
$currentyear = $currentdate['year'];

//var_dump($_GET);

if (!empty($subupn) && $subupn != "null" ){
//  if (!empty($subupn) || $subupn != "" || $subupn != null || $subupn != NULL || $subupn != "0" || strlen(trim($subupn))==0){
		echo "<h1>Enter business details for UPN: ".$upn." and SubUPN: ".$subupn."</h1>";
	}
	else {
		echo "<h1>Enter business details for UPN: ".$upn."</h1>";
	}

//get the current database entries from property
	$Data = new businessDetailsClass;
    $r = $Data->getBInfo( $upn, $subupn, $currentyear, $districtid ) ;
    
//var_dump($r);  
    

//get the properyType from feefixing for the drop-down list
$run = "SELECT d1.class, d1.code from `fee_fixing_business` d1 WHERE d1.`districtid`='".$districtid."';";

$query 	= mysql_query($run);

$businessType	= array();

while ($row = mysql_fetch_array($query)) {
	//check which property code is stored in property
    if ($row['code']==$r['business_class'])
    {
     $bususe = $row['code'].' : '.$row['class'];
    }
	$json 		= $row['code'].' : '.$row['class'];
	//put the list into the drop down list
	$businessType[] 			= $json;
 }//end while
 

$newcell = "<td>";
$endcell = "</td>";
$newrow = "<tr>";
$endrow= "</tr>";

// create instance of HTML_Form
$frm = new HTML_Form();

// using $frmStr to concatenate long string of form elements
// startForm arguments: action, method, id, optional attributes added in associative array
$frmStr = $frm->startForm('businessDetails.php', 'post', 'demoForm',
            array('class'=>'demoForm', 'onsubmit'=>'return checkBeforeSubmit(this)') ) . PHP_EOL .
    
    // fieldset and legend elements
    $frm->startTag('fieldset') . PHP_EOL .

    "<table class='formTblContainer'><tr><td>" .
		"<table class='formTbl' >" . $newrow . $newcell .

   
	   // wrap form elements in paragraphs 
	    '<strong>Business Location</strong>' . $endcell . $newcell . $endcell .$endrow .
		$frm->startTag('p') .    
		// label and text input with optional attributes
		$frm->addLabelFor('street', $newcell.'Street name: '.$endcell) . $newcell. 
		// using html5 required attribute
		$frm->addInput('text', 'street', $r['streetname'], array('id'=>'street', 'size'=>30, 'required'=>true) ) . 

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL . $endcell . $endrow . $newrow . $newcell . 
	    '<strong>Owner Information</strong>' . $endcell .$newcell . $endcell . $endrow .
		$frm->startTag('p') .    
		// label and text input with optional attributes
		$frm->addLabelFor('owner', $newcell.'Owner: '.$endcell) . $newcell. 
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
		$frm->addLabelFor('localCode', $newcell.'Locality code: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'localCode', $r['locality_code'], array('id'=>'localCode', 'size'=>30, 'required'=>true) ) . 

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
		"<table class='formTbl'>" . $newrow . //$newcell . //border='1' cellpadding='10' cellspacing='1' bgcolor='#FFFFFF'
	
		 $newrow . $newcell . 
	    '<strong>Business Information</strong>' . $endcell .$newcell . $endcell . $endrow .
		$frm->startTag('p') . 
		$frm->addLabelFor('businessname', $newcell.'Business name: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'businessname', $r['business_name'], array('id'=>'businessname', 'size'=>30, 'required'=>true) ) . 
		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow . 

		$frm->startTag('selectlist') . $newcell.'Business class: ' .$endcell.$newcell. 
		// arguments: name, array containing option text/values
		// include values attributes (boolean),
		// optional arguments: selected value, header, additional attributes in associative array
		$frm->addSelectList('businessclass', $businessType, false, $bususe, '' ,array('id'=>'businessclass', 'style'=>'width: 200px') ) .
		$frm->endTag('selectlist') . $endcell. $endrow . 

		$frm->startTag('p') . 
		$frm->addLabelFor('daAssignmentNumber', $newcell.'DA assignment number:'.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'daAssignmentNumber', $r['da_no'], array('id'=>'daAssignmentNumber', 'size'=>30, 'required'=>true) ) . 
		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow . 

		$frm->startTag('p') . 
		$frm->addLabelFor('businessCertificate', $newcell.'Business certificate:'.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'businessCertificate', $r['business_certif'], array('id'=>'businessCertificate', 'size'=>30, 'required'=>true) ) . 
		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow . 

		$frm->startTag('p') . 
		$frm->addLabelFor('employees', $newcell.'Employees: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'employees', $r['employees'], array('id'=>'employees', 'size'=>4, 'required'=>true) ) . 
		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow . 

		$frm->startTag('p') . 
		$frm->addLabelFor('yearEsablishment', $newcell.'Year of establishment: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'yearEsablishment', $r['year_establ'], array('id'=>'yearEsablishment', 'size'=>4, 'required'=>true) ) . 
		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow . 

		$frm->startTag('p') . 
		$frm->addLabelFor('landmark1', $newcell.'Nearest Landmark: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'landmark1', $r['landmark1'], array('id'=>'landmark1', 'size'=>30, 'required'=>true) ) . 
		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow . 
   
		$frm->startTag('p') . 
		$frm->addLabelFor('landmark2', $newcell.'More Landmark: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('text', 'landmark2', $r['landmark2'], array('id'=>'landmark2', 'size'=>30, 'required'=>true) ) . 
		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow . 
   		"</td></tr>" .
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