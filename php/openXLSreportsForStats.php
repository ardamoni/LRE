<?php
/**
 * openXLSreports
 * this script requires one _GET parameters, and does not return any value. 
 * 1. $_GET['districtid'] = the current districtid of the user
 * The script creates a table with all availabe predefined XLSX reports
 * You must adjust some variable names
 * 1. id="option1" = this is the first optin, i.e. button in the selction table. All subsequent option will get id="optionX" with X = 2..3..4..5..etc
 * 2.  onclick="openXLS(1) = this specifies the first optin for the function openXLS. All subsequent option will get  onclick="openXLS(X) with X = 2..3..4..5..etc
 * 3.  id="report1" = this will be used by the function openXLS to set the download filename. All subsequent option will get  id="reportX" with X = 2..3..4..5..etc
 * 4.  id="sfile1" = this will be used by the function openXLS to set the temp XLSX filename. All subsequent option will get  id="sfileX" with X = 2..3..4..5..etc
 * THIS SOFTWARE IS PROVIDED BY THE FREEBSD PROJECT "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE FREEBSD PROJECT OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
error_reporting(E_ALL);
// hide notices
@ini_set('error_reporting', E_ALL & ~ E_NOTICE);

set_time_limit(0);
ob_start(); // prevent adding duplicate data with refresh (F5)
session_start();

require_once( "../lib/configuration.php"	);

date_default_timezone_set('Europe/London');

?>
<html>
<link rel="stylesheet" href="../css/ex.css" type="text/css" />
<link rel="stylesheet" href="../css/flatbuttons.css" type="text/css">

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

.tableshow { 
	  width:  24px;  
	  height: 24px;
	  background-color: white;
	  background-image: url("../img/tableview.png");
	}

</style>

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>Excel Reports Selection</title>

</head>
<body>
<script src="../lib/OpenLayers/lib/OpenLayers.js"></script> 
<script src="../lib/spin/spin.js"></script>



<!-- specify option, report name and sfile name as described at the beginning of the script -->
<!-- ALSO make the necessary adjustments in the function openXLS -->
<h1>Reports in Excel-Format</h1>
<h2>Available reports</h2>
	<table>
		<tr><td><center id="spin0"></center></td></tr>
	</table>
		<input name="squery" type="hidden" id="squery" value = "test">
		<br>
	<table class='demoTbl' border='1' cellpadding='10' cellspacing='1'>
		<tr>
			<td><center><strong>Report</center></strong></td>
			<td><center><strong>Description</center></strong></td>
			<td><center><strong>Download</center></strong></td>
			<td><center><strong>Preview</center></strong></td>
		</tr>
		<tr>
			<td><input type="button" type="submit" id="option1" name="xlsopen" a href="javascript:;" onclick="openXLS(1);" class="orange-flat-button" value="Revenue potential Property Rates by district"/></td>
			<div><input type="hidden" id="report1" value=""></div></td>
			<td>This will produce an Excel table with the Potential of Property Rates per year</td>
			<td><div id=sfile1></div></td>
			<td><center id="spin1" type="hidden"><center id="squery1" type="hidden"><div id=prev1></div></center></td>
		</tr>
		<tr>
			<td><input type="button" type="submit" id="option2" name="xlsopen" a href="javascript:;" onclick="openXLS(2);" class="orange-flat-button" value="Revenue potential Business Rates by district"/></td>
			<div><input type="hidden" id="report2" value=""></div></td>
			<td>This will produce an Excel table with the Potential of Business Rates per year</td>
			<td><div id=sfile2></div></td>
			<td><center id="spin2" type="hidden"><center id="squery2" type="hidden"><div id=prev2></div></center></td>
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
var spin = new Spinner(spoptions);
var sd = new Date();
var sdate = sd.getFullYear().toString()+(sd.getMonth()+1).toString()+sd.getDate().toString()+sd.getHours().toString()+sd.getMinutes().toString()+sd.getSeconds().toString();
var sfile = <?php echo json_encode($_GET['districtid']); ?>+sdate;


document.getElementById('spin0').innerHTML="Please be patient, this process will take a while";
switch(opt) {
	case 1:
		var squery = 'select d3.`district_name`, d2.year, sum(d2.`rate`) as TotalRevenueExpected_Property ';
			squery +='from `property` d1, `fee_fixing_property` d2, `area_district` d3 ';
			squery +='WHERE ';
			squery +=' d1.`districtid`=d2.`districtid` AND d1.`property_use`=d2.`code` AND d2.`districtid`=d3.`districtid` GROUP BY d1.`districtid`, d2.`year`';
		document.getElementById('squery'+opt).value=squery;
		document.getElementById('option'+opt).value="List revenue potential";
//		document.getElementById('option1').value=squery;
		document.getElementById('report'+opt).value="01LREpotentialProperty";
		var target = document.getElementById('spin'+opt);
	  break;
	case 2:  
		var squery = 'select d3.`district_name`, d2.year, sum(d2.`rate`) as TotalRevenueExpected_Business ';
			squery +='from `business` d1, `fee_fixing_business` d2, `area_district` d3 ';
			squery +='WHERE ';
			squery +=' d1.`districtid`=d2.`districtid` AND d1.`business_class`=d2.`code` AND d2.`districtid`=d3.`districtid` GROUP BY d1.`districtid`, d2.`year`';
		document.getElementById('squery'+opt).value=squery;
		document.getElementById('option'+opt).value="List revenue potential";
		document.getElementById('report'+opt).value="01LREpotentialBusiness";
		var target = document.getElementById('spin'+opt);
	  break;
	default:  
	}
spin.spin(target);

//alert(document.getElementById('report1').value);
<?php flush(); ?>;
var pageURL = 'excelwriter.php'; //?squery=SELECT * from property WHERE districtid='+<?php echo json_encode($_GET['districtid']); ?>;

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
var sqy = "SELECT * from area_district"; // WHERE districtid='+<?php echo json_encode($_GET['districtid']); ?>;

document.getElementById('spin0').innerHTML="<strong>Export to Excel is completed! Use the link to download the file</strong>";
var outfile = document.getElementById('report'+this.opt).value+".xlsx";
document.getElementById('sfile'+this.opt).innerHTML='<a href="downloadxls2.php?sfile='+this.sfile+'.xlsx&outfile='+outfile+'" id=down1 value="Download file" style="hidden">Download file</a>';
document.getElementById('prev'+this.opt).innerHTML='<span> <button type="submit" class="tableshow" onclick="tablepreview('+this.opt+');"  value="" title="Open the table view"></button> </span>';

//'<a href ="showtable.php?sfile='+this.sfile+'.xlsx&outfile='+outfile+'" id=down1 style="hidden">Download file</a>';

//stop the spinner and show that the process has ended
this.spin.stop();

}

//-----------------------------------------------------------------------------
		//function tablepreview() 
		//opens a window to show tabular data about the corresponding map
		//
//-----------------------------------------------------------------------------
function tablepreview(opt) {

	var popupWindow = null;
  var title = 'Preview';
  var pageURL = 'showtable.php?squery='+document.getElementById('squery'+opt).value;

//call openPDFprint with title and pageURL as the two arguments	
	var pageURL = 'openPDFprint.php?title='+title+'&pageURL='+pageURL;
// var pageURL = 'php/Reports/BillsRegister.php?target='+target+'&districtid='+globaldistrictid;
	var w = 1024;
	var h = 650;
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    var popupWindow = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);

//   var popupWindow = null;
//   var title = 'Preview';
//   var pageURL = 'showtable.php?squery='+document.getElementById('squery'+opt).value;
// 	var w = 1000;
// 	var h = 500;
//     var left = (screen.width/2)-(w/2);
//     var top = (screen.height/2)-(h/2);
//     var popupWindow = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
  
  //var popupWindow = window.open (pageURL, "_self", title);

   //alert('in prev');  
	if(popupWindow && !popupWindow.closed)
	{
		popupWindow.focus();
	}

	return false;

} 
// end of function tablepreview


</script>

<body>
</html>