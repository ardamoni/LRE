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
</style>
    </head>
    <body>
        <h1 id="title">Spreadsheet upload</h1>

<?php
		require_once('../lib/html_form.class.php');
		require_once( "../lib/configuration.php"	);
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
		$frm->startTag('label') . $newcell.'Select District where the Fee Fixing is coming from: ' .$endcell.$newcell. 
		// arguments: name, array containing option text/values
		// include values attributes (boolean),
		// optional arguments: selected value, header, additional attributes in associative array
		$frm->addSelectList('constructMaterial', $cmlist, false, $cmuse ) .
		$frm->endTag('label') . $endcell. $endrow . 
		$frm->startTag('p') . 

		$frm->addLabelFor('uploadedfile', $newcell.'Choose a file to upload: '.$endcell) .$newcell.
		// using html5 required attribute
		$frm->addInput('hidden', 'MAX_FILE_SIZE', '100000', array('id'=>'MAX_FILE_SIZE', 'size'=>30, 'required'=>true) ) . 
		$frm->addInput('file', 'uploadedfile', '', array('id'=>'uploadedfile', 'size'=>30, 'required'=>true) ) . 
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
	
<!--   	<form enctype="multipart/form-data" action="readXLSX.php" method="POST">
	<input type="text" id="getdistrictid" value="" style="width: 50px;">
	 Enter district id: <br />
	
	<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
	Choose a file to upload: <input name="uploadedfile" type="file" /><br />
	<input type="submit" value="Upload File" />
	</form>'
--!>
    </body>
</html>
