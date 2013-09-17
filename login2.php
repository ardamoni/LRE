<?php

  	/*
	 *	No Direct Access To This File
	 *	-----------------------------------------------------------------------
	 */ 
	defined( 'VALID_REVENUE' ) or die( 'STOP' );
 
?>
<style type="text/css">
        .olControlAttribution { 
            bottom: 0px;
            left: 2px;
            right: inherit;
            width: 400px;
        }        

			 #mapLogin {
				width: 720px;
				height: 640px;
				  }
</style>
        <link rel="stylesheet" href="lib/OpenLayers/theme/default/style.css" type="text/css">
        <link rel="stylesheet" href="style.css" type="text/css">


<form name = "login" action = "index2.php" method = "POST">

<table class="map_area" border = "1">
 	<tr>
		<td> <div id="mapLogin" class="smallmap"></div> </td>
		<td>
			<table width = "300" align = "center" border = "1">
				<tr>
					<td><p id = "form-desc"><?php echo "USERNAME"; ?>:</p>
					<input type = "text" id = "txt1" name = "user" size = "30"><br /><br /></td>
				</tr>
				<tr>
					<td><p id = "form-desc"><?php echo "PASSWORD"; ?>:</p>
					<input type = "password" id = "txt1" name = "pass" size = "30"><br /><br /></td>
				</tr>
				<tr>
					<td colspan = "2">		
						<input type = "submit" value = "<?php echo "SIGN IN"; ?>" id = "btn1">		
					</td>
				</tr>
			</table>
		</td>
	</tr>	
</table>
</form>
<script src="lib/OpenLayers/lib/OpenLayers.js"></script> 

<script type="text/javascript">
var mapLogin;
var projWGS84 = new OpenLayers.Projection("EPSG:4326");
var proj900913 = new OpenLayers.Projection("EPSG:900913");

var options = {   
			  scales: [500, 1000, 2500, 5000, 10000],
			  numZoomLevels: 26,
			  allOverlays: true,
			  projection: new OpenLayers.Projection("EPSG:900913"),
			  displayProjection: new OpenLayers.Projection("EPSG:4326"),
			  controls:[
//			new OpenLayers.Control.Navigation(),
//				new OpenLayers.Control.PanZoomBar(),
//				new OpenLayers.Control.LayerSwitcher({'ascending':false}), 
//				new OpenLayers.Control.ScaleLine(),
				new OpenLayers.Control.MousePosition(),
			//	new OpenLayers.Control.OverviewMap(),
				new OpenLayers.Control.Attribution(),
				new OpenLayers.Control.KeyboardDefaults()],};

var sm = new OpenLayers.StyleMap({
			fillColor: "#666666",
			lineColor: "#0033FF"});

var styleDistricts = { 
		// style_definition
		 strokeColor: "#3300CC",
            strokeOpacity: 0.6,
            strokewidth: 1,
            fillColor: "#66FFFF",
            fillOpacity: 0.1,
	};

var mapLogin = new OpenLayers.Map('mapLogin', options);
//Mapnik
  var mapnik =  new OpenLayers.Layer.OSM("OpenStreetMap");

//KML-Districts    
/*
      var kmldistricts =  new OpenLayers.Layer.Vector("Districts in Ghana", {
            strategies: [new OpenLayers.Strategy.Fixed()],
            visibility: true,
            styleMap: sm,
            projection: mapLogin.displayProjection,
            protocol: new OpenLayers.Protocol.HTTP({
                url: "kml/Ghana_districts.kml",
                format: new OpenLayers.Format.KML({
                    extractStyles: true, 
                    extractAttributes: false,
                    maxDepth: 0
                })
            })
        });
*/
   var districtmap = new OpenLayers.Layer.Vector("Districts from Database", {		 
	    visibility: true,
//	    eventListeners: {"added": getpolygons,
 						 //"featureadded": function(){alert("Feature added")}
 //						 }
     });
        
	mapLogin.addLayer(mapnik);     
//	mapLogin.addLayer(kmldistricts);
	mapLogin.addLayer(districtmap);
	var request = OpenLayers.Request.POST({
			url: "php/dbaction.php", 
			data: OpenLayers.Util.getParameterString(
			{dbaction: "getdistrictmap"}),
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			callback: polyhandler
		});

    var ghana = new OpenLayers.LonLat(-1.1759874280090854,8.173345828918867).transform(new OpenLayers.Projection("EPSG:4326"),mapLogin.getProjectionObject());

    mapLogin.setCenter(ghana, 7);

//-----------------------------------------------------------------------------
		//function getpolygons() 
//-----------------------------------------------------------------------------
function getpolygons() {  
		var request = OpenLayers.Request.POST({
			url: "php/dbaction.php", 
			data: OpenLayers.Util.getParameterString(
			{dbaction: "getdistrictmap"}),
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			callback: polyhandler
		});

} //end of function getpolygons

//-----------------------------------------------------------------------------
		//function polyhandler() 
		//is the callback handler for getpolygons()
		//it takes the request feed from getlocalplan.php and creates polygones on the Layer fromjson
//-----------------------------------------------------------------------------

function polyhandler(request) {
	// the server could report an error
	if(request.status == 500) {
		// do something to calm the user
	}
	// the server could say you sent too much stuff
	if(request.status == 413) {
		// tell the user to trim their request a bit
	}
	// the browser's parser may have failed

	if(!request.responseXML) {
		// get the response from php and read the json encoded data
	   feed=JSON.parse(request.responseText);

		var boundary = [];
		var i = 0
		// build geometry for each feed item
		for (var i = 0; i < feed.length; i++) {
			boundary = feed[i]['boundary'];       	
			var coordinates = boundary.split(" ");
			var polypoints = [];
			for (var j=0;j < coordinates.length; j++) {
				points = coordinates[j].split(",");
				point = new OpenLayers.Geometry.Point(points[0], points[1]).transform(projWGS84,proj900913);
				polypoints.push(point);
			}
			// create some attributes for the feature
		var attributes = {districtname: feed[i]['districtname']};
		    // create a linear ring by combining the just retrieved points
		var linear_ring = new OpenLayers.Geometry.LinearRing(polypoints);
		    //the switch checks on the payment status and 
				var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, styleDistricts);		
		  districtmap.addFeatures([polygonFeature]);
		  } // end of for 
		  districtmap.redraw();
	}
} // end of function polyhandler
</script>
