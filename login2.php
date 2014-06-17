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


<form name = "login" action = "index.php" method = "POST">
<noscript>
        <div class="noscript">
            <div class="noscript-inner">
                <p><strong>JavaScript seems to be disabled in your browser.</strong></p>
                <p>You must have JavaScript enabled in your browser to utilize the functionality of this website.</p>
            </div>
        </div>
</noscript>
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
			new OpenLayers.Control.LayerSwitcher({'ascending':false}), 
//				new OpenLayers.Control.ScaleLine(),
				new OpenLayers.Control.MousePosition(),
			//	new OpenLayers.Control.OverviewMap(),
				new OpenLayers.Control.Attribution(),
				new OpenLayers.Control.KeyboardDefaults()],};
        OpenLayers.Feature.Vector.style['default']['strokeWidth'] = '2';

var sm = new OpenLayers.StyleMap({
			fillColor: "#666666",
			lineColor: "#0033FF"});

var renderer = OpenLayers.Util.getParameters(window.location.href).renderer;
    renderer = (renderer) ? [renderer] : OpenLayers.Layer.Vector.prototype.renderers;

var styleDistricts = { 
		// style_definition
		 strokeColor: "#0000FF",
            strokeOpacity: 0.6,
            strokewidth: 1,
//            fillColor: "#C0C0C0",
            fillOpacity: 0.0
	};

var zoneStyle = new OpenLayers.Style({
  		fillColor: "#66FFFF",
        fillOpacity: 0.4, 
        hoverFillColor: "#587498",
        hoverFillOpacity: 0.8,
        strokeColor: "#FFAC62",
        strokeOpacity: 0.8,
        strokeWidth: 1,
        strokeLinecap: "round",
        strokeDashstyle: "solid",
        hoverStrokeColor: "red",
        hoverStrokeOpacity: 1,
        hoverStrokeWidth: 0.2,
        pointRadius: 6,
        hoverPointRadius: 1,
        hoverPointUnit: "%",
        pointerEvents: "visiblePainted",
        cursor: "inherit"});   
        
var districtselectStyle = new OpenLayers.Style({
	    fillColor: "#ffcc00",
        fillOpacity: 0.4, 
        hoverFillColor: "white",
        hoverFillOpacity: 0.6,
        strokeColor: "#ff9900",
        strokeOpacity: 0.6,
        strokeWidth: 2,
        strokeLinecap: "round",
        strokeDashstyle: "solid",
        hoverStrokeColor: "red",
        hoverStrokeOpacity: 1,
        hoverStrokeWidth: 0.2,
        pointRadius: 6,
        hoverPointRadius: 1,
        hoverPointUnit: "%",
        pointerEvents: "visiblePainted",
        cursor: "pointer"
        });    

   var temporaryStyle = new OpenLayers.Style({
        fillColor: "#587058",
        fillOpacity: 0.4, 
        hoverFillColor: "white",
        hoverFillOpacity: 0.8,
        strokeColor: "#587498",
        strokeOpacity: 0.8,
        strokeLinecap: "round",
        strokeWidth: 2,
        strokeDashstyle: "solid",
        hoverStrokeColor: "red",
        hoverStrokeOpacity: 1,
        hoverStrokeWidth: 0.2,
        pointRadius: 6,
        hoverPointRadius: 1,
        hoverPointUnit: "%",
        pointerEvents: "visiblePainted",
        cursor: "inherit",
        graphicName: "cross"
    });
    
var zoneStyleMap = new OpenLayers.StyleMap({
		 'default': zoneStyle,
		 'select': districtselectStyle,
		 'temporary': temporaryStyle});  

var regionStyleMap = new OpenLayers.StyleMap({
		 'default': styleDistricts,
		 'select': districtselectStyle,
		 'temporary': temporaryStyle});  
		 
		 
var mapLogin = new OpenLayers.Map('mapLogin', options);
//Mapnik
  var mapnik =  new OpenLayers.Layer.OSM("OpenStreetMap");

   var districtmap = new OpenLayers.Layer.Vector("Districts from Database", {	
   		renderers: renderer,
	    visibility: true,
	    isBaseLayer: false,
	    styleMap: zoneStyleMap});

   var regionmap = new OpenLayers.Layer.Vector("Regions from Database", {	
   		renderers: renderer,
	    visibility: true,
	    isBaseLayer: false,
	    styleMap: regionStyleMap});
        
	mapLogin.addLayer(mapnik);     
	mapLogin.addLayer(regionmap);
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

	var regionrequest = OpenLayers.Request.POST({
			url: "php/dbaction.php", 
			data: OpenLayers.Util.getParameterString(
			{dbaction: "getregionmap"}),
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			callback: polyhandlerregion
		});

var report = function(e) {
                OpenLayers.Console.log(e.type, e.feature.id);
            };
            		
/*	 var highlightCtrl = new OpenLayers.Control.SelectFeature(districtmap, {
                hover: true,
                highlightOnly: true,
                renderIntent: "temporary",
                eventListeners: {
                    beforefeaturehighlighted: report,
                    featurehighlighted: report,
                    featureunhighlighted: report
                }
            });
 */
 var selectControlHover = new OpenLayers.Control.SelectFeature(districtmap, {
	hover: true,
	highlightOnly: true,
	renderIntent: "temporary",
	overFeature: function(feature) {
		console.log('hover: number of selected features: ' + districtmap.selectedFeatures.length);
		document.getElementById("title1").innerHTML="Login to dLRev - "+feature.attributes.districtname;
		districtmap.drawFeature(districtmap.getFeatureById(feature.id), {fillColor: "#FFCC00", fillOpacity: 0.1, strokeColor: "#00ffff"});			
	},
	outFeature: function(feature) {
		console.log('hover out: number of selected features: ' + districtmap.selectedFeatures.length);
		districtmap.drawFeature(districtmap.getFeatureById(feature.id));			
	},
        });
        
var selectControl = new OpenLayers.Control.SelectFeature(
  regionmap, {
    hover: true,
    onBeforeSelect: function(feature) {
       // add code to create tooltip/popup
       popup = new OpenLayers.Popup(
          "",
          feature.geometry.getBounds().getCenterLonLat(),
          new OpenLayers.Size(150,100),
          "<div>"+feature.attributes.regionname+"</div>",
//          "<br>Area: "+(feature.geometry.getGeodesicArea(proj900913)).toFixed(2)+"sq m</div>",
          null,
          true,
          null);

       feature.popup = popup;

       mapLogin.addPopup(popup);
       // return false to disable selection and redraw
       // or return true for default behaviour
       return true;
    },
    onUnselect: function(feature) {
       // remove tooltip
       mapLogin.removePopup(feature.popup);
       feature.popup.destroy();
       feature.popup=null;
    }
});

//     var selectCtrl = new OpenLayers.Control.SelectFeature(districtmap,
//                 {clickout: true}
//             );

            mapLogin.addControl(selectControl);
            selectControl.activate();
            mapLogin.addControl(selectControlHover);
            selectControlHover.activate();

//            mapLogin.addControl(selectCtrl);
//            selectCtrl.activate();


    var ghana = new OpenLayers.LonLat(-1.175,7.8).transform(new OpenLayers.Projection("EPSG:4326"),mapLogin.getProjectionObject());

    mapLogin.setCenter(ghana, 7);
    
    

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
				var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes);//, styleDistricts);		
		  districtmap.addFeatures([polygonFeature]);
		  } // end of for 
		  districtmap.redraw();
	}
}	
function polyhandlerregion(regionrequest) {
   request=regionrequest;
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
		var attributes = {regionname: feed[i]['regionname']};
		    // create a linear ring by combining the just retrieved points
		var linear_ring = new OpenLayers.Geometry.LinearRing(polypoints);
		    //the switch checks on the payment status and 
				var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes);//, styleDistricts);		
		  regionmap.addFeatures([polygonFeature]);
		  } // end of for 
		  regionmap.redraw();
	}
} // end of function polyhandler
</script>
