<!DOCTYPE html>
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <title>KML Overlay Example</title>

        <link rel="stylesheet" href="../theme/default/style.css" type="text/css">
        <link rel="stylesheet" href="style.css" type="text/css">
        <style type="text/css">
        .olControlAttribution { 
            bottom: 0px;
            left: 2px;
            right: inherit;
            width: 400px;
        }        

			 #map {
				width: 1280px;
				height: 720px;
				  }
        </style>
        <style>
    table.mouse_location_sample tr td {
        border: 1px solid #ccc;
        width: 200px;
    }
</style>
    </head>
    <body>
<?php
		require_once('../lib/html_form.class.php');
		require_once( "../lib/configuration.php"	);
	// create instance of HTML_Form
		$frm = new HTML_Form();

// using $frmStr to concatenate long string of form elements
// startForm arguments: action, method, id, optional attributes added in associative array
		$frmStr = $frm->startForm('propertyDetails.php', 'post', 'demoForm',
            array('class'=>'demoForm', 'onsubmit'=>'return checkBeforeSubmit(this)') ) . PHP_EOL .
    
    // fieldset and legend elements
	    $frm->startTag('fieldset') . PHP_EOL .

    "<table class='formTblContainer'><tr><td>" .

   
	   // wrap form elements in paragraphs 
		$frm->startTag('p') .    
		// label and text input with optional attributes
		$frm->addLabelFor('owner', 'Owner: '.$endcell) . $newcell. 
		// using html5 required attribute
		$frm->addInput('text', 'owner', $r['owner'], array('id'=>'owner', 'size'=>30, 'required'=>true) ) . 

		// endTag remembers startTag (but you can pass tag if nesting or for clarity)
		$frm->endTag('p') . PHP_EOL . $endcell . $endrow .
	"</table>" .
    $frm->endForm();

// finally, output the long string
echo $frmStr;
?>
	
    <h1 id="title">Spreadsheet upload</h1>
    
   	<form enctype="multipart/form-data" action="php/readXLSX.php" method="POST">
	<input type="text" id="getdistrictid" value="" style="width: 50px;">
	 Enter district id: <br />
	
	<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
	Choose a file to upload: <input name="uploadedfile" type="file" /><br />
	<input type="submit" value="Upload File" />
	</form>'


    </body>
</html>
