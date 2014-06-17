<?php

error_reporting(E_ALL);
// hide notices
@ini_set('error_reporting', E_ALL & ~ E_NOTICE);

set_time_limit(0);
ob_start(); // prevent adding duplicate data with refresh (F5)
session_start();

date_default_timezone_set('Europe/London');

?>
<html>
<link rel="stylesheet" href="../css/ex.css" type="text/css" />
<style type="text/css">

table.demoTbl {
    border-collapse: collapse;
/*     border-spacing: 0; */
   	border-color:#ffcc00;
}

table.demoTbl .title {
    width:200px;
}

tr:nth-of-type(even) {
      background-color:#ccc;
    }

table.demoTbl td, table.demoTbl th {
    padding: 6px;
}

table.demoTbl th.first {
    text-align:left;
    background-color:green; 
    }
table.demoTbl td.num {
    text-align:right;
    }
    
table.demoTbl td.foot {
    text-align: center;
}

</style>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>Excel Reports Selection</title>

</head>
<body>
<script src="../lib/OpenLayers/lib/OpenLayers.js"></script> 
<script src="../lib/spin/spin.js"></script>




<h1>Reports in Excel-Format</h1>
<h2>Available reports</h2>
			<table>
			<tr><td><center id="spin1"></center></td></tr>
			</table>

<!-- 
	xlsreports = {link:"List properties by district",
					descripton:"This will produce an Excel table with the content of the properties table",
					phpfile:"excelwritetest-3.php",
					sql:"select * from property where districtid=130"};
 -->

<!-- 	<form id="form1" name="form1" method="post" action="'.$xlsreports["phpfile"].'"?squery="'.$xlsreports["sql"].'"> -->


	<input name="squery" type="hidden" id="squery" value = "test">
	<br>
<!-- 	<strong>Are you sure you want to upload the following data into the database?: </strong><input type="submit" id="Submit" name="Submit" value="Upload" /> -->
<!-- 	</form> -->


		<table class='demoTbl' border='1' cellpadding='10' cellspacing='1'>
		<tr>
		<td><center><strong>Report</center></strong></td>
		<td><center><strong>Description</center></strong></td>
		<td><center><strong>Download</center></strong></td>
		</tr>
		<tr>
		<td><input type="button" type="submit" id="option1" name="xlsopen" a href="javascript:;" onclick="openXLS(1);" value="List all regions"/>
		<div><input type="hidden" id="report1" value=""></div></td>
		<td>This will produce an Excel table with the content of the properties table</td>
		<td><div id=sfile1></div></td>
		</tr>
		<tr>
		<td><input type="button" type="submit" id="option2" name="xlsopen" a href="javascript:;" onclick="openXLS(2);" value="List properties by district"/></td>
		<div><input type="hidden" id="report2" value=""></div></td>
		<td>This will produce an Excel table with the content of the properties table</td>
		<td><div id=sfile2></div></td>
		</tr>
	
		</table>
		
<script type="text/javascript">
function openXLS(opt){

var spoptions = {
  lines: 12, // The number of lines to draw
  length: 7, // The length of each line
  width: 4, // The line thickness
  radius: 10, // The radius of the inner circle
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
  top: 'auto', // Top position relative to parent in px
  left: '50%' // Left position relative to parent in px
};
var target = document.getElementById('spin1');
var spin = new Spinner(spoptions);
var sd = new Date();
var sdate = sd.getFullYear().toString()+(sd.getMonth()+1).toString()+sd.getDate().toString()+sd.getHours().toString()+sd.getMinutes().toString()+sd.getSeconds().toString();
var sfile = <?php echo json_encode($_GET['districtid']); ?>+sdate;


document.getElementById('spin1').innerHTML="Please be patient, this process will take a while";
spin.spin(target);
switch(opt) {
	case 1:
		var squery = 'SELECT `id`, `districtid`, `district_name`, `regionid`, `activestatus`, `districtnameTCPD`, `districtnameCoA`, `coa-regionid`, `coa-districtid`, `coa-disttypeid`, `coa-submetroid` from area_district WHERE districtid='+<?php echo json_encode($_GET['districtid']); ?>;
		document.getElementById('option1').value="List all regions";
		document.getElementById('report1').value="01LREregions";
	  break;
	case 2:  
		var squery = 'SELECT * from property WHERE districtid='+<?php echo json_encode($_GET['districtid']); ?>;
		document.getElementById('option2').value="List all properties";
		document.getElementById('report2').value="01LREproperties";
	  break;
	default:  
	}
//alert(document.getElementById('report1').value);
<?php flush(); ?>;
var pageURL = 'excelwritetest-3.php'; //?squery=SELECT * from property WHERE districtid='+<?php echo json_encode($_GET['districtid']); ?>;

var handlerParameter = {spin: spin, opt: opt, sfile: sfile};

//issue the XMLHTTPRequest to process the export in the background
var request = OpenLayers.Request.POST({
	url: pageURL, 
	data: OpenLayers.Util.getParameterString(
	{squery: squery,
	 sfile : sfile}),
	headers: {
		"Content-Type": "application/x-www-form-urlencoded"
	},
//	callback: OpenLayers.Function.bind(handler, null, handlerParameter)
	callback: handler,
	scope: handlerParameter
});

//    var popupWindow = window.open (pageURL, "_self", title);
//spinner.stop();    
}

function handler(request){
// this is the object used in scope 
// console.log(this);
// alert(this.opt);
document.getElementById('spin1').innerHTML="<strong>Export to Excel is completed! Use the link to download the file</strong>";
var outfile = document.getElementById('report'+this.opt).value+".xlsx";
document.getElementById('sfile'+this.opt).innerHTML='<a href ="downloadxls2.php?sfile='+this.sfile+'.xlsx&outfile='+outfile+'" id=down1 style="hidden">Download file</a>';
this.spin.stop();

}

</script>

<body>
</html>