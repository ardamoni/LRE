<?php
	session_start();
?>
<!DOCTYPE html>
<!--31. Juli 2013 09:21:24 GMT First time sync with GitHUB-->
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">
	<meta http-equiv="Content-Script-Type" content="text/javascript">
	<meta name="robots" content="noindex">
	<meta name="referrer" content="never">
    <title>Ntobua - District Local Revenue</title>

	<script type="text/javascript" language="javascript" src="jquery_accordion.js"></script>
	<link rel="stylesheet" href="lib/OpenLayers/theme/default/style.css" type="text/css">
<!-- 	the next stylesheet contains the styles for overlays etc. -->
	<link rel="stylesheet" href="css/styles.css" type="text/css">
	<link rel="stylesheet" href="css/flatbuttons.css" type="text/css">
	<link rel="stylesheet" href="css/themify-icons.css" type="text/css">


	<script type="text/javascript">

// Add jQuery functionality
	$(document).ready(function()
	{
		//slides the element with class "menu_body" when paragraph with class "menu_head" is clicked
		$("#menuContainer p.menu_head").click(function()
		{
			$(this).css({backgroundImage:"url(icons/down.png)"}).next("div.menu_body").slideToggle(300).siblings("div.menu_body").slideUp("slow");
			$(this).siblings().css({backgroundImage:"url(icons/left.png)"});
		});

	});

	function overlay() {
	el = document.getElementById("overlay");
	var offset1 = document.getElementById('stats').getBoundingClientRect();
	var offset2 = document.getElementById('fisprop').getBoundingClientRect();
	var offset3 = document.getElementById('fisbus').getBoundingClientRect();
	var offset4 = document.getElementById('navtools').getBoundingClientRect();
	var offseticons = document.getElementById('icon-section').getBoundingClientRect();
	var top = offseticons.top+10;
	var left = offset4.left+30;
// 	el.style.margin = "'.top.' '. left'";
	el.style.top = top+'px';
	el.style.left = left+'px';
	el.value = 1;
	el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
}
	function overlayadmin() {
	el = document.getElementById("overlayadmin");
	var offset1 = document.getElementById('stats').getBoundingClientRect();
	var offset2 = document.getElementById('fisprop').getBoundingClientRect();
	var offset3 = document.getElementById('fisbus').getBoundingClientRect();
	var offset4 = document.getElementById('navtools').getBoundingClientRect();
	var offseticons = document.getElementById('icon-section').getBoundingClientRect();
	var top = offseticons.top+10;
	var left = offset4.left+30;
// 	el.style.margin = "'.top.' '. left'";
	el.style.top = top+'px';
	el.style.left = left+'px';
	el.value = 1;
	el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
}
	function overlayxls() {
	el = document.getElementById("overlayxls");
	var offset1 = document.getElementById('stats').getBoundingClientRect();
	var offset2 = document.getElementById('fisprop').getBoundingClientRect();
	var offset3 = document.getElementById('fisbus').getBoundingClientRect();
	var offset4 = document.getElementById('navtools').getBoundingClientRect();
	var offseticons = document.getElementById('icon-section').getBoundingClientRect();
	var top = offseticons.top+10;
	var left = offset4.left+30;
// 	el.style.margin = "'.top.' '. left'";
	el.style.top = top+'px';
	el.style.left = left+'px';
	el.value = 1;
	el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
}
	function overlayreports() {
	el = document.getElementById("overlayreports");
	var offset1 = document.getElementById('stats').getBoundingClientRect();
	var offset2 = document.getElementById('fisprop').getBoundingClientRect();
	var offset3 = document.getElementById('fisbus').getBoundingClientRect();
	var offset4 = document.getElementById('navtools').getBoundingClientRect();
	var offseticons = document.getElementById('icon-section').getBoundingClientRect();
	var top = offseticons.top+10;
	var left = offset4.left+30;
// 	el.style.margin = "'.top.' '. left'";
	el.style.top = top+'px';
	el.style.left = left+'px';
	el.value = 1;
	el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
}
	function overlaysearch() {
	el = document.getElementById("overlaysearch");
	var offset1 = document.getElementById('stats').getBoundingClientRect();
	var offset2 = document.getElementById('fisprop').getBoundingClientRect();
	var offset3 = document.getElementById('fisbus').getBoundingClientRect();
	var offset4 = document.getElementById('navtools').getBoundingClientRect();
	var offseticons = document.getElementById('icon-section').getBoundingClientRect();
	var top = offseticons.top+10;
	var left = offset4.left+30;
// 	el.style.margin = "'.top.' '. left'";
	el.style.top = top+'px';
	el.style.left = left+'px';
	el.value = 1;
	el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
}
	function overlayeasteregg() {
	el = document.getElementById("overlayeasteregg");
	var w = 800;
	var h = 330;
    var left = (screen.width/2)-w;
    var top = (screen.height/2)-h;
// 	el.style.margin = "'.top.' '. left'";
	el.style.top = top+'px';
	el.style.left = left+'px';
	el.value = 1;
	el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
}

</script>

</head>
<body>
<!-- 	This is needed for the menu system -->
<!-- this is the popup below the icon section -->
	<div id="overlay">
     <div>
          <p>Please select one of the Print options.</p>
			<input type="submit" value="Bills for Property Rates" href="javascript:;" onclick="printAnnualBill('property');" title="Bills for Property Rates" class="orange-flat-small">
			<input type="submit" value="Bills for Business Licenses" href="javascript:;" onclick="printAnnualBill('business');" title="Bills for Business Licenses" class="orange-flat-small">
			<input type="submit" value="Bills Register for Property Rates" href="javascript:;" onclick="billsRegister('property');" title="Bills Register for Property Rates" class="orange-flat-small">
			<input type="submit" value="Bills Register for Business Licenses" href="javascript:;" onclick="billsRegister('business');" title="Bills Register for Business Licenses" class="orange-flat-small">
          [<a href='#' onclick='overlay()'>close</a>]
     </div>
	</div>
	<div id="overlayadmin">
     <div>
          <p>Please select one of the Admin options.</p>
			<input type="submit" value="KML to DB conversion" href="javascript:;" onclick="uploadkml();" title="Upload KML file into the database" class="orange-flat-small">
			<input type="submit" value="Upload Fee Fixing information" href="javascript:;" onclick="uploadxls();" title="Store Fee Fixing information in database" class="orange-flat-small">
			<input type="submit" value="Upload Scanned Data" href="javascript:;" onclick="uploadScannedData();" title="Store scanned Data in database" class="orange-flat-small">
			<input type="submit" value="Collector Zone relation" href="javascript:;" onclick="updateCZinPropBus();" title="Relate each parcel with collector zone" class="orange-flat-small">
			<input type="submit" value="Administration Module" href="javascript:;" onclick="index_admin();" title="Administration Module" class="orange-flat-small">
          [<a href='#' onclick='overlayadmin()'>close</a>]
     </div>
	</div>
	<div id="overlayxls">
     <div>
          <p>Please select one of the Export options.</p>
			<input type="submit" value="Excel Export" href="javascript:;" onclick="xlsexport();" title="Available Excel Exports" class="orange-flat-small">
          [<a href='#' onclick='overlayxls()'>close</a>]
     </div>
	</div>
	<div id="overlayreports">
     <div>
          <p>Please select one of the Report options.</p>
			<input type="submit" value="Weekly" href="javascript:;" onclick="" title="Generate the weekly report" class="orange-flat-small">
			<input type="submit" value="Monthly" href="javascript:;" onclick="" title="Generate the monthly report" class="orange-flat-small">
			<input type="submit" value="Quarterly" href="javascript:;" onclick="" title="Generate the quarterly report" class="orange-flat-small">
			<input type="submit" value="Annualy" href="javascript:;" onclick="" title="Generate the anual report" class="orange-flat-small">
          [<a href='#' onclick='overlayreports()'>close</a>]
     </div>
	</div>
	<div id="overlaysearch">
     <div>
          <p>Please enter either a Name <br/> or a Street into the entry field.</p>
<!-- <form> -->
		<input type="text" id="searchOther" value="" style="width: 200px;" onkeypress="if(event.keyCode==13) {javascript:searchOther();}"  >
		<center><input type="radio" id="target" name="target" value="street">Street
		<input type="radio" id="target" name="target" value="owner">Owner<br></center>
		<input type="submit" value="Search" href="javascript:;" onclick="searchOther();" title="Submit the search" class="orange-flat-small">
          [<a href='#' onclick='overlaysearch()'>close</a>]
<!-- </form> -->
     </div>
	</div>
	<div id="overlayeasteregg">
     <div>
        <canvas id="eggCanvas" width="800" height="300"></canvas>
    <script>
      var eggcanvas = document.getElementById('eggCanvas');
      var eggcontext = eggcanvas.getContext('2d');
      var x = 30;
      var y = 35;
      var width = 740;
      var height = 240;
      var imageObj = new Image();
      imageObj.src = 'uploads/dlRevCrew-small.jpg';

      imageObj.onload = function() {
        eggcontext.drawImage(imageObj, x, y, width, height);
      };
      eggcontext.font = 'italic 20pt Verdana';
      eggcontext.fillText('The Crew that brought you dLRev!', 30, 25);
    </script>[<a href='#' onclick='overlayeasteregg()'>close</a>]
<!-- </form> -->
     </div>
	</div>
<!-- end of popup below the icon section -->


	<div id="header">
	<!--the following districtname is set in jsfunctions.js in function sessionuserhandler-->
		<h1> <div id="districtname"></div></h1>
		<div id="tags">GeoJSON</div>
		<table class="lremain">
		<tr>
			<td>
				<input type="submit" id="stats" value="Stats" class="orange-flat-small">
				<table style="border:0px" cellpadding="3" cellspacing="5" align='center'>
				<tr>
					<td style="border:0px">Parcels #:</td>
					<td width="25px" style="border:0px">
						<strong><small><span id="stat1">0</span></small></strong>
					</td>
				</tr>
				<tr>
					<td style="border:0px">Properties #:</td>
					<td width="25px" style="border:0px">
						<strong><small><span id="stat2">0</span></small></strong>
					</td>
				</tr>
				<tr>
					<td style="border:0px">Businesses #:</td>
					<td width="25px" style="border:0px">
						<strong><small><span id="stat3">0</span></small></strong>
					</td>
				</tr>
				</table>
			</td>
			<td>
				<input type="submit" id="fisprop" value="Property Rates Info" href="javascript:;" onclick="getFiscalStats();" title="Click to calculate fiscal stats" class="orange-flat-small">
				<table style="border:0px" cellpadding="3" cellspacing="5" align='center'>
				<tr>
					<td width="10px" style="border:0px"><div id="fispropexp">Expected:</div></td>
<!-- 					<input type="text" readonly="readonly" style="border:0px" id="fispropexp" value=""></td> -->
					<td width="25px" style="border:0px">
						<strong><small><span id="fis1">0</span></small></strong>
					</td>
				</tr>
				<tr>
					<td width="10px" style="border:0px">Collected:</td>
					<td width="25px" style="border:0px">
						<strong><small><span id="fis2">0</span></small></strong>
					</td>
				</tr>
				<tr>
					<td width="10px" style="border:0px">Outstanding<small><br>(incl. arrears)</small>:</td>
					<td width="25px" style="border:0px">
						<strong><small><span id="fis3">0</span></small></strong>
					</td>
				</tr>
				</table>
			</td>
			<td>
				<input type="submit" id="fisbus" value="BOP Info" href="javascript:;" onclick="getFiscalStats();" title="Click to calculate fiscal stats" class="orange-flat-small">
				<table style="border:0px" cellpadding="3" cellspacing="5" align='center'>
				<tr>
					<td width="10px" style="border:0px"><div id="fisbusexp">Expected:</div></td>
					<td width="25px" style="border:0px">
						<strong><small><span id="fis4">0</span></small></strong>
					</td>
				</tr>
				<tr>
					<td width="10px" style="border:0px">Collected:</td>
					<td width="25px" style="border:0px">
						<strong><small><span id="fis5">0</span></small></strong>
					</td>
				</tr>
				<tr>
					<td width="10px" style="border:0px">Outstanding<small><br>(incl. arrears)</small>:</td>
					<td width="25px" style="border:0px">
						<strong><small><span id="fis6">0</span></small></strong>
					</td>
				</tr>
				</table>
			</td>
			<td>
				<strong><span id="navtools"><center>Navigation Tools</center></span></strong>
				<center><span id="tools" class="olControlPanel"></span></center>
			</td>
			<td>
			<div class="icon-section" id="icon-section">
				<table style="border:0px" cellpadding="3" cellspacing="5" align='center'>
				<tr>
					<td style="border:0px"></td>
					<td width="25px" style="border:0px">
						<div class="icon-container">
							<span class="ti-printer" type="submit" href="javascript:;" onclick="overlay();" value="" title="Open Print options"></span><span class="icon-name"></span>
						</div>
					</td>
					<td width="25px" style="border:0px">
						<div class="icon-container">
							<span class="ti-settings" type="submit" href="javascript:;" href="javascript:;" onclick="overlayadmin();" value="" title="Open Admin options"></span><span class="icon-name"></span>
						</div>
					</td>
					<td width="25px" style="border:0px">
						<div class="icon-container">
							<span class="ti-export" type="submit" href="javascript:;" href="javascript:;" onclick="overlayxls();" value="" title="Open Excel Export"></span><span class="icon-name"></span>
						</div>
					</td>
				</tr>
				<tr>
					<td style="border:0px"></td>
					<td width="25px" style="border:0px">
						<div class="icon-container">
							<span class="ti-write" type="submit" href="javascript:;" href="javascript:;" onclick="overlayreports();" value="" title="Open Reports"></span><span class="icon-name"></span>
						</div>
					</td>
					<td width="25px" style="border:0px">
						<div class="icon-container">
							<span class="ti-search" type="submit" href="javascript:;" href="javascript:;" onclick="overlaysearch();" value="" title="Open Street and Name Search"></span><span class="icon-name"></span>
						</div>
					</td>
				</tr>
				</table>
			</div>
				<center id="debug2"></center>
			</td>
			<td>
				 <center>Quick Search</center>
				 <input type="text" id="searchBox" value="" style="width: 200px;" onkeypress="if(event.keyCode==13) {javascript:searchupn();}"  >
				 <center><input type="submit" href="javascript:;" onclick="searchupn();" class="orange-flat-small" value="SEARCH" ></center>
			</td>
			<td>
				<center id="wcUser">Welcome</center>
				<form action="logout.php" method="post">
				<input type="submit" value="SIGN OUT" class="pomegranate-flat-button">
				</form>
			</td>
		 </tr>
		</table>
	</header>

	<div id="container">
		<!-- left most column, col3 -->

		<!-- map goes here -->
		<div id="col2">
			<script type="text/javascript">
				//init();
				window.onload = function()
				{
				// here we adjust the display to match the screen dimensions
				  if (window.innerWidth <= 1180){
					document.getElementById("map").style.width = window.innerWidth*0.75+"px";
					} else {
					document.getElementById("map").style.width = window.innerWidth*0.82+"px";
					}
				  if (window.innerHeight < 650){
					document.getElementById("map").style.height = window.innerHeight*0.75+"px";
					} else {
					document.getElementById("map").style.height = window.innerHeight*0.80+"px";
					}

					document.getElementById("controls").style.visibility="hidden";

					init();
				};
			</script>
		</div>

		<!-- right most column, col3 -->
		<div id="col3">


		<table class="map_area">
		<tr>
			<td> <div id="map" class="smallmap"></div>
    		</div> </td>
			<td>
			<table>
<!--
			if the layerswitcher is located outside the map you will need the next line
			in jsfunctions.js there is some changes necessary as well line 53 for example
			<tr><td><div id="layerswitcher" class="olControlLayerSwitcher"></div></td></tr>
-->
			<tr><td><div id="debug1"></div></td></tr>
			<tr>
			<td>
			<div id="tags"> vertices, digitizing, draw, drawing </div>
				<div id="controls">
					<ul id="controlToggle">
						<li> <input type="radio" name="type" value="none" id="noneToggle"
								   onclick="toggleControl(this);" checked="checked" />
							<label for="noneToggle">navigate</label>
						</li>
						<li> <input type="radio" name="type" value="polygon" id="polygonToggle" onclick="toggleControl(this);" />
							<label for="polygonToggle">draw polygon</label>
						</li>
						<li> <input type="radio" name="type" value="modify" id="modifyToggle"
								   onclick="toggleControl(this);" />
							<label for="modifyToggle">modify feature</label>
							<ul>
								<li> <input id="createVertices" type="checkbox" checked
										   name="createVertices" onchange="update()" />
									<label for="createVertices">allow vertices creation</label>
								</li>
								<li> <input id="rotate" type="checkbox" name="rotate" onchange="update()" />
									<label for="rotate">allow rotation</label>
								</li>
								<li> <input id="resize" type="checkbox" name="resize" onchange="update()" />
									<label for="resize">allow resizing</label>
									(<input id="keepAspectRatio" type="checkbox" name="keepAspectRatio" onchange="update()" checked="checked" />
									<label for="keepAspectRatio">keep aspect ratio</label>)
								</li>
								<li> <input id="drag" type="checkbox" name="drag" onchange="update()" />
									<label for="drag">allow dragging</label>
								</li>
							</ul>
						</li>
					</ul>
				</div>
			</td>
			</tr>
			<tr>
			<td>
				<div id="legend" style="hidden"></div>
				<canvas id="myCanvas" width="200" height="200" style="hidden">
<!-- 				style="border:1px solid #d3d3d3;"> -->
					Your browser does not support the HTML5 canvas tag.</canvas>
			</td>
			</tr>
			</table>
			</td>
		   </tr>
		  </table>
		</div> <!-- end of rightmost column -->

	</div> <!-- end of container -->

	<div id="footer">
		<div id="docs">
			<p>
				Geo Location Information provided by TCPD
				<?php
				//echo $_SESSION['user']['user'];
				?>
			</p>
		</div>

			<script src="lib/jquery-ui-1.11.0/external/jquery/jquery.js"></script>
			<script src="lib/jquery-ui-1.11.0/jquery-ui.js"></script>
		    <script src="http://maps.google.com/maps/api/js?v=3&amp;sensor=false"></script>
			<script src="lib/OpenLayers/lib/OpenLayers.js"></script>
			<script src="lib/spin/spin.js"></script>
			<script src="lib/stopwatch.js"></script>
			<script src="lib/numberformat.js"></script>
			<script src="js/jsfunctions.js"></script>

	</div>	<!-- end of footer -->


	</body>
</html>
