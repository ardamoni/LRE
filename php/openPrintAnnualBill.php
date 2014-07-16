<?php
/**
 * openPrintAnnualBill
 * this script is required to give a user responds while the bill is being printed
 var myvar = <?php echo json_encode($myVarValue); ?>;
 */
error_reporting(E_ALL);
// hide notices
@ini_set('error_reporting', E_ALL & ~ E_NOTICE);

set_time_limit(0);
ob_start(); // prevent adding duplicate data with refresh (F5)
session_start();

require_once( "../lib/configuration.php"	);

date_default_timezone_set('Europe/London');

//var_dump($_GET);

?>
<html>
<link rel="stylesheet" href="../style.css" type="text/css" />
<link rel="stylesheet" href="../css/flatbuttons.css" type="text/css">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>Print Annual Bills</title>

</head>
<body>
<script src="../lib/OpenLayers/lib/OpenLayers.js"></script> 
<script src="../lib/spin/spin.js"></script>

<!-- Progress information -->
<div id="information" style="width">Please be patient, process is time consuming!</div>
<!-- Target for the spinner -->
<h1><div><center id="target"></center></div></h1>
<!-- 
<br/>
<br/>
 -->
<!-- <div><input type="text" id="target" value=""></div> -->
<iframe src="#" id="callPropertyAnnualBill"></iframe>
<!-- specify option, report name and sfile name as described at the beginning of the script -->
<!-- ALSO make the necessary adjustments in the function openXLS -->
<script type="text/javascript">

var spoptions = {
  lines: 13, // The number of lines to draw
  length: 20, // The length of each line
  width: 10, // The line thickness
  radius: 30, // The radius of the inner circle
  corners: 1, // Corner roundness (0..1)
  rotate: 14, // The rotation offset
  direction: 1, // 1: clockwise, -1: counterclockwise
  color: '#000', // #rgb or #rrggbb
  speed: 1, // Rounds per second
  trail: 60, // Afterglow percentage
  shadow: false, // Whether to render a shadow
  hwaccel: false, // Whether to use hardware acceleration
  className: 'spinner', // The CSS class to assign to the spinner
  zIndex: 2e9, // The z-index (defaults to 2000000000)
  top: '50%', // Top position relative to parent in px
  left: '50%' // Left position relative to parent in px
};
var target = document.getElementById('target');
var spin = new Spinner(spoptions);
spin.spin(target);
districtid = <?php echo json_encode($_GET['districtid']); ?>;
ifproperty = <?php echo json_encode($_GET['ifproperty']); ?>;
//title = <?php echo json_encode($_GET['title']); ?>;
var w = window.innerWidth-40;
var h = window.innerHeight-60;
var sd = new Date();
//var sdate = sd.getFullYear().toString()+(sd.getMonth()+1).toString()+sd.getDate().toString()+sd.getHours().toString()+sd.getMinutes().toString()+sd.getSeconds().toString();
var sdate = sd.getDate().toString()+'.'+(sd.getMonth()+1).toString()+'.'+sd.getFullYear().toString()+'-'+sd.getHours().toString()+':'+sd.getMinutes().toString()+'h';//+sd.getSeconds().toString();
document.getElementById("information").innerHTML="Please be patient, process is time consuming! - "+sdate;

//alert(document.getElementById('report1').value);
if (ifproperty=='property'){
	var pageURL = 'Reports/PropertyAnnualBill.php?districtid='+districtid;
	var title = 'Property Annual Bill Printing';
	document.getElementById("callPropertyAnnualBill").src=pageURL;
}else if (ifproperty=='business'){
	var pageURL = 'Reports/BusinessAnnualBill.php?districtid='+districtid;
	var title = 'Business Annual Bill Printing';
	document.getElementById("callPropertyAnnualBill").src=pageURL;
}
	document.getElementById("callPropertyAnnualBill").width=w;
	document.getElementById("callPropertyAnnualBill").height=h;
	document.getElementById("callPropertyAnnualBill").onload=function(){
																spin.stop(); 
																var sd = new Date();
																var sdate = sd.getDate().toString()+'.'+(sd.getMonth()+1).toString()+'.'+sd.getFullYear().toString()+'-'+sd.getHours().toString()+':'+sd.getMinutes().toString()+'h';//+sd.getSeconds().toString();
																document.getElementById("information").innerHTML="Process completed! - "+sdate;
																};

// 
// var handlerParameter = {spin: spin};
// 
// //issue the XMLHTTPRequest to process the export in the background
// var request = OpenLayers.Request.POST({
// 	url: pageURL, 
// 	data: OpenLayers.Util.getParameterString(
// 	{districtid: districtid}),
// 	headers: {
// 		"Content-Type": "application/x-www-form-urlencoded"
// 	},
// 	callback: handler,
// 	scope: handlerParameter
// });

//    var popupWindow = window.open (pageURL, "_self", title);
//spinner.stop();    

// function handler(request){
// // this is the object used in scope 
// this.spin.stop();
// }
//spin.stop();
</script>
<body>
</html>