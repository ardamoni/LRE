<?php
	session_start();
?>
<!DOCTYPE html>
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <title>KML Overlay Example</title>

        <link rel="stylesheet" href="../theme/default/style.css" type="text/css">
        <link rel="stylesheet" href="../style.css" type="text/css">
        <style type="text/css">
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
				height: 3em;
				font-size:1em;
				text-align:left;
			}
			table.formTbl td  {
				width: 700px;
				font-size:1em;
				text-align:left;
				padding:5px;
			}
</style>
    </head>
    <body>
        <h1 id="title">Spreadsheet upload</h1>

<?php
		require_once('../lib/html_form.class.php');
		require_once( "../lib/configuration.php"	);

//get the districts from feefixing for the drop-down list
$run = "SELECT districtid, district_name from `area_district` ORDER BY `districtid` ;";

$query 	= mysql_query($run);

$district	= array();
//$duse = '130 : Prestea Huni Valley';
while ($row = mysql_fetch_array($query)) {
	//check which district is stored in $SESSION
    if ($_SESSION['user']['districtid']==$row['districtid'])
    {
     $duse = $row['districtid'].' : '.$row['district_name'];
    }

    $json = $row['districtid'].' : '.$row['district_name'];
	//put the list into the drop down list
	$dlist[] 			= $json;
 }//end while


$currentdate = getdate();
$yearuse = $currentdate['year'];
 
for ($x=$yearuse-2; $x<=$yearuse+10; $x++)
  {
  $yearlist[] = $x;
  }
	// create instance of HTML_Form
		$frm = new HTML_Form();

// using $frmStr to concatenate long string of form elements
// startForm arguments: action, method, id, optional attributes added in associative array
		$frmStr = $frm->startForm('readXLSX.php', 'post', 'demoForm',
            array('enctype'=>'multipart/form-data' , 'class'=>'demoForm' )) . PHP_EOL .
    
    // fieldset and legend elements
	    $frm->startTag('fieldset') . PHP_EOL .

    "<table class='formTbl'><tr><td>" .

   
	   // wrap form elements in paragraphs 
		$frm->endTag('p') .
		$frm->addLabelFor('uploadedfile', $newcell.'Choose a file to upload: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('hidden', 'MAX_FILE_SIZE', '100000', array('id'=>'MAX_FILE_SIZE', 'size'=>30, 'required'=>true) ) . 
		$frm->addInput('file', 'uploadedfile', '', array('id'=>'uploadedfile', 'size'=>30, 'required'=>true) ) . 
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow .

		$frm->startTag('label') . $newcell.'Select District where the Fee Fixing is coming from: ' .$endcell.$newcell. 
		$frm->addSelectList('district', $dlist, false, $duse ) .
		$frm->endTag('label') . $endcell. $endrow . 
		$frm->startTag('p') . 
		
		$frm->startTag('label') .$newcell. 'Select Year: ' .$endcell.$newcell. 
		$frm->addSelectList('year', $yearlist, false, $yearuse ) .
		$frm->endTag('label') . $endcell. $endrow . 
		$frm->startTag('p') .

		$frm->addLabelFor('ifproperty', $newcell.'Select category: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('radio', 'ifproperty', '1', array('id'=>'ifproperty', 'checked'=>'1')  ) . ' Property Rates ' . PHP_EOL .
		$frm->addInput('radio', 'ifproperty', '0', array('checked'=>'0')) . ' Business Licenses' . 

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') .
	    $frm->startTag('submit') .    
		$frm->addInput('submit', 'submit', 'Upload File') .
		$frm->endTag('submit') . PHP_EOL .


		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL .  $endcell. $endrow .

	"</table>" .
    $frm->endForm();

// finally, output the long string
echo $frmStr;
?>
</body>
</html>
