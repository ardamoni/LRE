<!DOCTYPE html>
<!--31. Juli 2013 09:21:24 GMT First time sync with GitHUB-->
<html>
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <title>KML onClick DB</title>	
	
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
				height: 550px;
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
			  background-image: url("lib/OpenLayers/img/zoom-world-mini.png");
			}
			.olControlPanel .olControlZoomBoxItemInactive { 
			  width:  22px;  
			  height: 22px;
			  background-color: orange;
			  background-image: url("lib/OpenLayers/img/drag-rectangle-off.png");
			}
			.olControlPanel .olControlZoomBoxItemActive { 
			  width:  22px;  
			  height: 22px;
			  background-color: blue;
			  background-image: url("lib/OpenLayers/img/drag-rectangle-on.png");
			}
			.olControlPanel .olControlZoomInItemInactive { 
			  width:  22px;  
			  height: 22px;
			  background-color: white;
			  background-image: url("lib/OpenLayers/theme/default/img/zoomin.png");
			}
			.olControlPanel .olControlZoomOutItemInactive { 
			  width:  22px;  
			  height: 22px;
			  background-color: white;
			  background-image: url("lib/OpenLayers/theme/default/img/zoomout.png");
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
		<h1 id="title">Bogoso, Local Plan</h1>
		<div id="tags">GeoJSON</div>		
		<table class="mouse_location_sample">
		<tr>
			<td>
				<span id="epsg1">0.0</span> <br/>
				Lon: <strong><span id="lon1">0.0</span></strong> <br/>
				Lat: <strong><span id="lat1">0.0</span></strong> <br/>
			</td>
			<td>
				<span id="epsg2">0.0</span> <br/>
				Lon: <strong><span id="lon2">0.0</span></strong> <br/>
				Lat: <strong><span id="lat2">0.0</span></strong> <br/>
			</td>
			<td>
				<strong><span><center>Navigation Tools</center></span></strong>
				<center><span id="tools" class="olControlPanel"></span> </center>
			</td>
			<td></td> <td></td> <td></td>
			<td>
				<center>Welcome</center>
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
						<a href="#">Past</a>
						<a href="#">Current</a>
						<a href="#">Future</a>	
					</div>
					<p class="menu_head">Search</p>
					<div class="menu_body">
						<a href="php/Search.php" onclick="return popitup('php/Search.php')">UPN or SUBUPN</a>
						<a href="#">Owner</a>
						<a href="#">Address</a>	
				   </div>						
					   <p class="menu_head">Contacts</p>
					   <p class="menu_head">News</p>
					   <p class="menu_head">Manual</p>
					   <p class="menu_head">Log out</p>
				</div>  
			</div>
			<a href="#" onclick="loadByAjax()">UPN/SUBUPN </a>
			<input type="text" name="search" id="search"/>

			<!-- search popup window -->
			<script language="javascript" type="text/javascript">
			function popitup(url) {
				newwindow=window.open(url,'name','height=200,width=500');
				if (window.focus) {newwindow.focus()}
				return false;
			}
			
			function loadByAjax()
			{
				$.ajax({
					  type: "POST",
					  url: "php/Search.php",
					  data: "searchkey=data_from_user_input",
					  success: function(response_data){
						$('mySearch').html(response_data)
					  }
				});
			}
			
			function UpdateSearchResults(x)
			{ 
				$.post("Ajax.Service.Contacts.Search.php", 
					{ Cat: $("#Cat").val() },
					function(data) {
						$("#SearchResults" ).html(data);}
				);
			}
			</script>
			
			
			
			
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
		
			<div id="tags"> vertices, digitizing, draw, drawing </div>

		<table class="map_area">
		<tr>
			<td> <div id="map" class="smallmap"></div> </td>
			<td>
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
		  </table>
		</div> <!-- end of rightmost column -->  
		
	</div> <!-- end of container -->
	
	<div id="footer">	
		<div id="docs">
			<p>
				Information provided by TCPD, <?php echo "name"; ?>, this follows
			</p>
		</div>
		<script src="http://maps.google.com/maps/api/js?v=3&amp;sensor=false"></script>
		<script src="lib/OpenLayers/lib/OpenLayers.js"></script>
		<script src="lib/spin/spin.js"></script>
		<script src="dbgeojsonpoly.js"></script>
	</div>	<!-- end of footer -->
	
	</body>	
</html>