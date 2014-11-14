<?php
require_once( "../../lib/configuration.php"	);

$districtID = $_GET['districtid'];
 echo 'Districtid: '.$districtID;

  //DELETE #2 w/Prepared Statement
$bind = array(
    ":districtid" => $districtID
);

var_dump($bind);

$result = $pdo->delete("property_due", "districtid = :districtid", $bind);
// $result = $pdo->delete("property_due", "districtid = ".$districtID);
 
 var_dump($result);

 echo '<br>Affected rows: '.$result;

//  if ($result>0) {
//   }else{
//   echo '<br>Could not populate due!!';
//   }


?>

<html>
<link rel="stylesheet" href="../../css/ex.css" type="text/css" />
<link rel="stylesheet" href="../../css/flatbuttons.css" type="text/css">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>Start Populate Property Due</title>

</head>
<body>
<!-- Progress information -->
<div id="information" style="width">Please be patient, process is time consuming!</div>
<p><input type="button" a href="javascript:;" onclick="window.close();" class="orange-flat-small" value="Close Preview"></a></p>
<input type="button" type="submit" id="option1" name="xlsopen" a href="javascript:;" onclick="openPopDue();" class="orange-flat-button" value="Start populate_property_due"/>

<!-- <div><input type="text" id="target" value=""></div> -->
<iframe src="#" id="PDFcontent"></iframe>
<!-- specify option, report name and sfile name as described at the beginning of the script -->
<!-- ALSO make the necessary adjustments in the function openXLS -->

<script type="text/javascript">

function openPopDue(){
//get the necessary parameters from $_GET
title = <?php echo json_encode($_GET['districtid']); ?>;
pageURL = 'populate_property_due.php?districtid='+<?php echo json_encode($_GET['districtid']); ?>;

var w = window.innerWidth-40;
var h = window.innerHeight-60;
var sd = new Date();

//var sdate = sd.getFullYear().toString()+(sd.getMonth()+1).toString()+sd.getDate().toString()+sd.getHours().toString()+sd.getMinutes().toString()+sd.getSeconds().toString();
var sdate = sd.getDate().toString()+'.'+(sd.getMonth()+1).toString()+'.'+sd.getFullYear().toString()+'-'+sd.getHours().toString()+':'+sd.getMinutes().toString()+'h';//+sd.getSeconds().toString();
document.getElementById("information").innerHTML="Please be patient, process is time consuming! - "+sdate;
document.title=title;
//configure the iframe to receive the PDF content
document.getElementById("PDFcontent").src=pageURL;
document.getElementById("PDFcontent").width=w;
document.getElementById("PDFcontent").height=h;
document.getElementById("PDFcontent").onload=function()	{
		var sd = new Date();
		var sdate = sd.getDate().toString()+'.'+(sd.getMonth()+1).toString()+'.'+sd.getFullYear().toString()+'-'+sd.getHours().toString()+':'+sd.getMinutes().toString()+'h';//+sd.getSeconds().toString();
		document.getElementById("information").innerHTML="Process completed! - "+sdate;
	};
}
</script>


<body>
</html>
