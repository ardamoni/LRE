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

    <title>District Local Revenue</title>	
	
	<script type="text/javascript" language="javascript" src="jquery_accordion.js"></script>
	<script type="text/javascript">
	$(document).ready(function()
	{
		//slides the element with class "menu_body" when paragraph with class "menu_head" is clicked 
		$("#menuContainer p.menu_head").click(function()
		{
			$(this).css({backgroundImage:"url(icons/down.png)"}).next("div.menu_body").slideToggle(300).siblings("div.menu_body").slideUp("slow");
			$(this).siblings().css({backgroundImage:"url(icons/left.png)"});
		});	
	});
	</script>

		
        <link rel="stylesheet" href="lib/OpenLayers/theme/default/style.css" type="text/css">
        <link rel="stylesheet" href="style.css" type="text/css">
		
		<!-- center and far right column-->
        <style type="text/css">
			#controls {
				width: 230px;
			}
			#controlToggle {
				padding-left: 1em;
			}
			#controlToggle li {
				list-style: none;
			}
			#map {
				width: 1024px;
				height: 650px;
			}

			.olControlAttribution { 
				bottom: 0px;
				left: 2px;
				right: inherit;
				width: 400px;
			}   
			.olControlPanel div { 
              position: relative;
              left: 5px;
			  display:block;
			  width:  24px;
			  height: 24px;
			  margin: 5px;
			  float: left;
    		  background-color:white;
			}
			.olControlPanel .olControlZoomToMaxExtentItemInactive { 
			  width:  18px;  
			  height: 18px;
			  background-image: url("img/zoom-world-mini.png");
			}
			.olControlPanel .olControlZoomBoxItemInactive { 
			  width:  22px;  
			  height: 22px;
			  background-color: orange;
			  background-image: url("img/drag-rectangle-off.png");
			}
			.olControlPanel .olControlZoomBoxItemActive { 
			  width:  22px;  
			  height: 22px;
			  background-color: blue;
			  background-image: url("img/drag-rectangle-on.png");
			}
			.olControlPanel .olControlZoomInItemInactive { 
			  width:  22px;  
			  height: 22px;
			  background-color: white;
			  background-image: url("img/zoomin.png");
			}
			.olControlPanel .olControlZoomOutItemInactive { 
			  width:  22px;  
			  height: 22px;
			  background-color: white;
			  background-image: url("img/zoomout.png");
			}
			.olControlPanPanel .olControlPanNorthItemInactive {
				 left:50%;
				 right:auto;
				 margin-left: -9px;
				 top: 0;
			}
			.olControlPanPanel .olControlPanSouthItemInactive {
				 left: 50%;
				 margin-left: -9px;
				 top: auto;
				 bottom: 0;
			}
			.olControlPanPanel .olControlPanWestItemInactive {
				 top: 50%;
				 margin-top: -9px;
				 left: 0;
			}
			.olControlPanPanel .olControlPanEastItemInactive {
				 top: 50%;
				 margin-top: -9px;
				 left: auto;
				 right: 0;
			}
		   .olControlZoomPanel {
				 left: 5px;
				 right: 23px;
				 top: 150px;
		   } 
		.olControlPanZoomBar {
			left:450px;
		}
		.tableshow { 
			  width:  24px;  
			  height: 24px;
			  background-color: white;
			  background-image: url("img/tableview.png");
			}
		.testbutton { 
			  width:  24px;  
			  height: 24px;
			  background-color: white;
			  background-image: url("img/marker.png");
			}
		.deletezone { 
			  width:  20px;  
			  height: 20px;
			  background-color: white;
			  background-image: url("img/delete2.png");
			}

		</style>
        <style>
			table.mouse_location_sample tr td {
				border: 1px solid #ccc;
				border-color:#ffcc00;
				width: 200px;
			}
			table.map_area tr td {
				border: 1px solid #ccc;
				border-color:#ffcc00;
			}
		</style>
    </head>
	<body>	
	
	<div id="header">  
	<!--the following districtid is hidden, but can be used anywhere in the programme. It contains the districtid, which it gets from the function getsesseionuser()-->
		<h1> <div id="districtname"></div></h1> 
		<div id="tags">GeoJSON</div>
		<table class="mouse_location_sample">
		<tr>
			<td>
				<strong><span id="epsg1"><center>Stats</center></span></strong>
				Parcels #: <strong><small><span id="stat1">0</span></small></strong> <br/>
				Properties #: <strong><small><span id="stat2">0</span></small></strong> <br/>
				Businesses #: <strong><small><span id="stat3">0</span></small></strong> <br/>
			</td>
			<td>
				<strong><span id="epsg2"><center>Fiscal Info</center></span></strong>
				Lon: <strong><small><span id="fis1">0</span></small></strong> <br/>
				Lat: <strong><small><span id="fis2">0</span></small></strong> <br/>
				Lat: <strong><small><span id="fis3">0</span></small></strong> <br/>
			</td>
			<td>
				<strong><span><center>Navigation Tools</center></span></strong>
				<center><span id="tools" class="olControlPanel"></span> </center>
			</td>
			<td>
				<strong><span><center>Tools</center></span></strong>
				<center><span id="tableview" class="tableshow"></span> 
				<span> <button type="submit" class="tableshow" href="javascript:;" onclick="tableshow();" value="" title="Open the table view"></button> </span> 
<!-- 				<span id="testbutton" class="testbutton"></span> -->
				<span> <button type="submit" id="testbutton" class="testbutton" href="javascript:;" onclick="makeLayersVisible();" value="" title="This is a button to test stuf" disabled></button> </span> </center>

			</td> 
			<td>
				<center id="debug2"></center>
			</td> 
			<td>
				 <center>Quick Search</center>
				 <input type="text" id="searchBox" value="" style="width: 200px;" onkeypress="if(event.keyCode==13) {javascript:searchupn();}"  > 
				 <center><input type="submit" href="javascript:;" onclick="searchupn();" value="SEARCH" ></center>
			</td>
			<td>
				<center id="wcUser">Welcome</center>
				<a href="logout.php"> <strong><center>SIGN OUT</center></strong> </a>				
			</td>
		 </tr>
		</table>
	</header>
	
	<div id="container">
		<!-- left most column, col3 -->
		<div id="col1">	
			<!--Code for accordion menu starts here-->
			<div style="float:left" >		
				<div id="menuContainer" class="menu_list"> 
					<p class="menu_head">Home</p>			
					<p class="menu_head">Reports</p>
					<div class="menu_body">
						<a href="#">Daily</a>
						<a href="#">Weekly</a>
						<a href="#">Monthly</a>	
						<a href="#">Annualy</a>								
						<a href=""javascript:;" onclick="propertyAnnualBillOnClick();"">Print Bills for Property Rates</a>
						<a href=""javascript:;" onclick="businessAnnualBillOnClick();"">Print Bills for Business Licenses</a>
						<a href=""javascript:;" onclick="billsRegister();"">Print the Bills Register for Business Licenses</a>
					</div>
					<p class="menu_head">Search</p>
					<div class="menu_body">
						<a href="#">UPN or SUBUPN</a>
						<a href="#">Owner</a>
						<a href="#">Address</a>			
				   </div>
				   <p class="menu_head">Contacts</p>
				   <p class="menu_head">Manual</p>
				   <p class="menu_head">Admin</p>
					<div class="menu_body">
						<a href=""javascript:;" onclick="uploadkml();"">KML to DB conversion</a>
						<a href=""javascript:;" onclick="uploadxls();"">Upload Fee Fixing information</a>
						<a href=""javascript:;" onclick="uploadScannedData();"">Upload Data from Scanning Process</a>
				   </div>

					   <p class="menu_head">Log out</p>
				</div>  
			</div>

			<!--Styling the accordion menu-->
			<style type="text/css">					
				.menu_list {	
					width: 150px;
				}
				.menu_head {
					padding: 5px 10px;
					cursor: pointer;
					position: relative;
					margin:1px;
					font-weight:bold;
					background: #eef4d3 url(icons/left.png) center right no-repeat;
				}
				.menu_body {
					display:none;
				}
				.menu_body a{
				  display:block;
				  color:#006699;
				  background-color:#EFEFEF;
				  padding-left:10px;
				  font-weight:bold;
				  text-decoration:none;
				}
				.menu_body a:hover{
				  color: purple;
				  text-decoration:underline;
				}
			</style>	
		</div> <!-- end of left most column, col1 -->

		<!-- map goes here -->
		<div id="col2">
			<script type="text/javascript">
				//init();
				window.onload = function()
				{
					init();
				};
			</script>
		</div>

		<!-- right most column, col3 -->
		<div id="col3">
		

		<table class="map_area">
		<tr>
			<td> <div id="map" class="smallmap"></div> </td>
			<td>
			<table>
			<tr><td><center id="debug1"></center></td></tr>
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
		    <script src="http://maps.google.com/maps/api/js?v=3&amp;sensor=false"></script>
			<script src="lib/OpenLayers/lib/OpenLayers.js"></script> 
			<script src="lib/spin/spin.js"></script>
			<script src="lib/stopwatch.js"></script>
			<script src="lib/numberformat.js"></script>
			<script src="js/jsfunctions.js"></script>

	</div>	<!-- end of footer -->
    
	
	</body>	
</html>
