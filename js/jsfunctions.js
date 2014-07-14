// 24. Juli 2013 12:16:13 GMT working on getting polygons on a map
// 01.08.13 11:30 created the LREinit from dbgeojsonpoly.js and placed all the functions in lib/jsfunctions.js
// 27. September 2013 10:10:43 GMT moved previous code from LREinit and created init() in order to keep file count low

//make drawing tools invisible
OpenLayers.ProxyHost = "proxy.cgi?url=";
 document.getElementById("controls").style.visibility="hidden";
 
var projWGS84 = new OpenLayers.Projection("EPSG:4326");
var proj900913 = new OpenLayers.Projection("EPSG:900913");

var session_name = "<?php echo json_encode($_SESSION['user']['name']); ?>";
var session_user = "<?php=$_SESSION['user']['user'];?>";
var session_roleid = "<?php=$_SESSION['user']['roleid'];?>";


//     		   document.getElementById("debug1").innerHTML=session_roleid+" "+session_user;

// global variables, CLEAN before populating them
	var global_upn = '';
	var global_subupn = [];
	var global_out_property;
	var global_out_business;
	var globalfeatureid;
	var globaldistrictid='';
	var globalpropertychanged = false; //used to display the outstanding revenue
	var globalbusinesschanged = false; //same, but for business
	var nogoogle = false;

//we need to get the starting window dimensions for a potential resize of the map
	var windowWidth = window.innerWidth;
  	var windowHeight = window.innerHeight;
  	var mapDefaultSizeWidth = 1180;
  	var mapDefaultSizeHeight = 650;

var options = {   
			  scales: [500, 1000, 2500, 5000, 10000],
			  numZoomLevels: 26,
			  allOverlays: true,
			  autoUpdateSize: true,
			  projection: proj900913, //new OpenLayers.Projection("EPSG:900913"),
			  displayProjection: projWGS84, //new OpenLayers.Projection("EPSG:4326"),
			  controls:[
			//	new OpenLayers.Control.PanZoomBar(),
				new OpenLayers.Control.LayerSwitcher({'ascending':false}), 
				new OpenLayers.Control.ScaleLine(),
				new OpenLayers.Control.MousePosition(),
			//	new OpenLayers.Control.OverviewMap(),
				new OpenLayers.Control.Attribution(),
				new OpenLayers.Control.KeyboardDefaults()],};

var map = new OpenLayers.Map('map', options);

//allow user to resize window including the map area
window.onresize = function()
{
  if (window.innerWidth < windowWidth){
  	document.getElementById("map").style.width = window.innerWidth*0.75+"px";
  	} else {
  	document.getElementById("map").style.width = mapDefaultSizeWidth+"px";
  	}
  if (window.innerHeight < windowHeight){
  	document.getElementById("map").style.height = window.innerHeight*0.75+"px";
  	} else {
  	document.getElementById("map").style.height = mapDefaultSizeHeight+"px";
  	}
  setTimeout( function() { map.updateSize();});
//  alert('CurrentSize: '+map.getCurrentSize() + ' window.innerwidth: ' + window.innerWidth + ' windowWidth: ' + windowWidth + ' screen: ' + screen.width + ' map.size: ' + map.size);;
}


var colzones, controls;
var globalinsertCZ = true;

var w = new Stopwatch(); 
//w.setListener(updateClock);

var spinopts = {
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
var target = document.getElementById('map');
var spinner = new Spinner(spinopts); //.spin(target);


//define the vector layers
var fromDistrict = new OpenLayers.Layer.Vector("District Boundary", {		 
	    visibility: true,
	    eventListeners: {"visibilitychanged": getPropertyPolygons,
 						 //"featureadded": function(){alert("Feature added")}
 						 }
     });
var fromLocalplan = new OpenLayers.Layer.Vector("Local Plan (Real Time)", {		 
		visibility: false,
	    eventListeners: {"visibilitychanged": getLocalplanPolygons,
 						 //"featureadded": function(){alert("Feature added")}
 						 }
     });

var fromProperty = new OpenLayers.Layer.Vector("Property Status (Real Time)", {		 
	    visibility: false,
	    eventListeners: {"visibilitychanged": getPropertyPolygons,
 						 //"featureadded": function(){alert("Feature added")}
 						 }
     });
var fromBusiness = new OpenLayers.Layer.Vector("Business Status (Real Time)", {		 
	    visibility: false,
	    eventListeners: {"visibilitychanged": getBusinessPolygons,
 						 //"featureadded": function(){alert("Feature added")}
 						 }
     });  


//specify the colours used for collector zones     
var icolor=0, colzonecolor=''; //used in onSketchComplete
var startcolor = '#F5E8E2'; //used in onSketchComplete
//var colors = ['#EBC137','#E38C2D','#DB4C2C','#771E10','#48110C']; //used in onSketchComplete

var colors = ['#1FCB4A', 	'#59955C', 	'#48FB0D', 	'#2DC800', 	'#59DF00', 	'#9D9D00', 	'#B6BA18',
'#27DE55', 	'#6CA870', 	'#79FC4E', 	'#32DF00', 	'#61F200', 	'#C8C800', 	'#CDD11B',
'#4AE371', 	'#80B584', 	'#89FC63', 	'#36F200', 	'#66FF00', 	'#DFDF00', 	'#DFE32D',
'#7CEB98', 	'#93BF96', 	'#99FD77', 	'#52FF20', 	'#95FF4F', 	'#FFFFAA', 	'#EDEF85',
'#93EEAA', 	'#A6CAA9', 	'#AAFD8E', 	'#6FFF44', 	'#ABFF73', 	'#FFFF84', 	'#EEF093',
'#BABA21', 	'#C8B400', 	'#DFA800', 	'#DB9900', 	'#FFB428', 	'#FF9331', 	'#FF800D',
'#E0E04E', 	'#D9C400', 	'#F9BB00', 	'#EAA400', 	'#FFBF48', 	'#FFA04A', 	'#FF9C42',
'#E6E671', 	'#E6CE00', 	'#FFCB2F', 	'#FFB60B', 	'#FFC65B', 	'#FFAB60', 	'#FFAC62',
'#EAEA8A', 	'#F7DE00', 	'#FFD34F', 	'#FFBE28', 	'#FFCE73', 	'#FFBB7D', 	'#FFBD82',
'#EEEEA2', 	'#FFE920', 	'#FFDD75', 	'#FFC848', 	'#FFD586', 	'#FFC48E', 	'#FFC895',
'#F1F1B1', 	'#FFF06A', 	'#FFE699', 	'#FFD062', 	'#FFDEA2', 	'#FFCFA4', 	'#FFCEA2'];

//specify the styles for the real time localplan vector layer
var LUPMISdefault = {
			strokeColor: "#FF7F50",
            strokeOpacity: 0.8,
            strokewidth: 4,
            fillColor: "#FFFFFF",
            fillOpacity: 0.15,
};
var LUPMIScolour01 = {
			strokeColor: "#CC5B1D",
            strokeOpacity: 0.8,
            strokewidth: 2,
            fillColor: "#E7D4C3",
            fillOpacity: 0.15,
};
var LUPMIScolour02 = {
			strokeColor: "#0E00A0",
            strokeOpacity: 0.8,
            strokewidth: 2,
            fillColor: "#CCDBEC",
            fillOpacity: 0.15,
};
var LUPMIScolour03 = {
			strokeColor: "#C71110",
            strokeOpacity: 0.8,
            strokewidth: 2,
            fillColor: "#F2BEBC",
            fillOpacity: 0.15,
};
var LUPMIScolour04 = {
			strokeColor: "#DCDD3B",
            strokeOpacity: 0.8,
            strokewidth: 2,
            fillColor: "#F0EEBE",
            fillOpacity: 0.15,
};
var LUPMIScolour05 = {
			strokeColor: "#76C533",
            strokeOpacity: 0.8,
            strokewidth: 2,
            fillColor: "#E4F0C4",
            fillOpacity: 0.15,
};
var LUPMIScolour06 = {
			strokeColor: "#CB00D4",
            strokeOpacity: 0.8,
            strokewidth: 2,
            fillColor: "#F0BFEB",
            fillOpacity: 0.15,
};
var LUPMISnoUPN = {
			strokeColor: "#780000",
            strokeOpacity: 0.8,
            strokewidth: 2,
            fillColor: "#FF00FF",
            fillOpacity: 0.55,
};

//define styles for the RedGreen vector layer
var styleRed = { 
		// style_definition
		 strokeColor: "#FFAC62",
            strokeOpacity: 0.8,
            strokewidth: 1,
            fillColor: "#FF0033",
            fillOpacity: 0.6,
	};
	
var styleNotYetGreen = { 
		// style_definition
		 strokeColor: "#FFAC62",
            strokeOpacity: 0.8,
            strokewidth: 1,
            fillColor: "#996633",
            fillOpacity: 0.6,
	};
var styleGreen = { 
		// style_definition
		 strokeColor: "#FFAC62",
            strokeOpacity: 0.8,
            strokewidth: 1,
            fillColor: "#336633",
            fillOpacity: 0.6,
	};
var styleNeutral = { 
		// style_definition
		 strokeColor: "#FFAC62",
            strokeOpacity: 0.8,
            strokewidth: 1,
            fillColor: "#FFFF99",
            fillOpacity: 0.6,
	};
var styleDistrictBoundary = { 
		// style_definition
		 strokeColor: "#0000FF",
            strokeOpacity: 0.8,
            strokewidth: 10,
            fillColor: "#FFFF99",
            fillOpacity: 0.0,
	};
	
	
function init(){

// if (session_roleid<=100){
// if (session_user='ekke'){
  startNormalUser();
// } 
//  if (session_user='monitor'){
//   startMonitoringUser();
//  } 
  
} //end init()

function startNormalUser() {

//check if Internet connection exists
// 		if (doesConnectionExist() == true) {
// 			alert("connection exists!");
// 		} else {
// 			alert("connection doesn't exist!");
// 		}

   var sm = new OpenLayers.StyleMap({
    			fillColor: "#666666",
    			lineColor: "#0033FF"});

//    			'default': mystyle,
//Mapnik
      var mapnik =  new OpenLayers.Layer.OSM("OpenStreetMap");

//check whether we are online or offline
if (doesConnectionExist()){
try {
  	  var gmap = new OpenLayers.Layer.Google(
					"Google Hybrid",
					{type: google.maps.MapTypeId.HYBRID,
// 					"Google Streets", // the default
// 					{numZoomLevels: 20,
 					visibility: false
					});
    } catch (e) {
         alert(e);
         var nogoogle=true;
    	return false;
    }//Google Streets      
} // end check online					
//Markers
//      var markers = new OpenLayers.Layer.Markers( "Markers" );
//KML we are not using this anymore     
/*      var kmlRedGreen =  new OpenLayers.Layer.Vector("Payment Status", {
            strategies: [new OpenLayers.Strategy.Fixed()],
            styleMap: sm,
            visibility: false,
            projection: map.displayProjection,
            protocol: new OpenLayers.Protocol.HTTP({
                url: "kml/RedGreen.kml",
                format: new OpenLayers.Format.KML({
                    extractStyles: true, 
                    extractAttributes: true,
                    kvpAttributes: false,
                    maxDepth: 2
                })
            })
        });
*/
//kmlLocalPlan      
//     var kmlLocalPlan =  new OpenLayers.Layer.Vector("Local Plan", {
//             strategies: [new OpenLayers.Strategy.Fixed()],
//             styleMap: sm,
//             visibility: false,
//             projection: map.displayProjection,
//             protocol: new OpenLayers.Protocol.HTTP({
//                 url: "kml/bogoso.kml",
//                 format: new OpenLayers.Format.KML({
//                     extractStyles: true, 
//                     extractAttributes: true,
//                     kvpAttributes: false,
//                     maxDepth: 2
//                 })
//             })
//         });
                
    var renderer = OpenLayers.Util.getParameters(window.location.href).renderer;
    renderer = (renderer) ? [renderer] : OpenLayers.Layer.Vector.prototype.renderers;

    zoneStyle = new OpenLayers.Style({
  		fillColor: "#336699",
        fillOpacity: 0.4, 
        hoverFillColor: "white",
        hoverFillOpacity: 0.8,
        strokeColor: "#003366",
        strokeOpacity: 0.8,
        strokeWidth: 5,
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

   var zoneselectStyle = new OpenLayers.Style({
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
				 
	zoneStyleMap = new OpenLayers.StyleMap({
				 'default': zoneStyle,
				 'select': zoneselectStyle,
				 'temporary': temporaryStyle});

 //modifies the default "default" style settings of OpenLayers

//define the collector zone vector layer    			
	colzones = new OpenLayers.Layer.Vector("Collector Zones", {
		renderers: renderer,
		 visibility: false,
         hover: true,
         styleMap: zoneStyleMap });
//		renderers: ["Canvas", renderer],
         

    map.addControl(new OpenLayers.Control.ModifyFeature(colzones, {vertexRenderIntent: "vertex"}));   
    

// Add Layers
	map.addLayer(mapnik);
//only call Google Maps if Internet exists	
 if (doesConnectionExist()){
//    if(!nogoogle){
	map.addLayer(gmap);
}else //Internet not available
{
// OpenstreetMap background should not be visible, i.e. white background
mapnik.visibility = false;
}
//	map.addLayer(fromDistrict);
//	map.addLayer(kmlLocalPlan);
	map.addLayer(fromLocalplan);
	map.addLayer(fromProperty);
	map.addLayer(fromBusiness);
	map.addLayer(colzones);


// polygon drawing for collector zones  	
	if (console && console.log) {
		function report(event) {
			console.log(event.type, event.feature ? event.feature.id : event.components);
		}
		colzones.events.on({
			"beforefeaturemodified": report,
			"featuremodified": onFeatureModifiedCZ,
			"featureadded": onFeatureAddedCZ,
			"afterfeaturemodified": report,
			"vertexmodified": report,
			"sketchmodified": report,
			"sketchstarted": report,
			"sketchcomplete": onSketchComplete,
			"visibilitychanged": onVisibiltyChangedcz
		});
	}
	controls = {
		polygon: new OpenLayers.Control.DrawFeature(colzones,
					OpenLayers.Handler.Polygon),
		modify: new OpenLayers.Control.ModifyFeature(colzones)
//		editingToolbarControl: new OpenLayers.Control.EditingToolbar(colzones)
	};
	
	for(var key in controls) {
		map.addControl(controls[key]);
	}

// end polygon drawing for collector zones  	

//   on click events for vector layers
   select = new OpenLayers.Control.SelectFeature([fromBusiness, fromLocalplan, fromProperty, fromBusiness, colzones],{
                hover: false,
                highlightOnly: false,
                renderIntent: "temporary",
            }); 
//            kmlRedGreen.events.on({
//                "featureselected": onFeatureSelect,
//                "featureunselected": onFeatureUnselect,
//            });
// 			kmlLocalPlan.events.on({
//                 "featureselected": onFeatureSelectSub,
//                 "featureunselected": onFeatureUnselect
//             });
			fromLocalplan.events.on({
                "featureselected": onFeatureSelectLocalplan,
                "featureunselected": onFeatureUnselect,

            });
			fromProperty.events.on({
                "featureselected": onFeatureSelectFJ,
                "featureunselected": onFeatureUnselect,

            });
			fromBusiness.events.on({
                "featureselected": onFeatureSelectBus,
                "featureunselected": onFeatureUnselect,

            });
			colzones.events.on({
                "featureselected": onFeatureSelectcz,
                "featureunselected": onFeatureUnselect
            });
            map.addControl(select);
            select.activate();   
//kml.feature end
            
//     OpenLayers.Util.getElement("epsg1").innerHTML = map.getProjection();
//     OpenLayers.Util.getElement("epsg2").innerHTML = "EPSG:4326";
//     
//             map.events.register("mousemove", map, mouseMoveListener);

    var layerSwitch = new OpenLayers.Control.LayerSwitcher();

    // Add Navigation controls
    var navigation = new OpenLayers.Control.Navigation();
    var history = new OpenLayers.Control.NavigationHistory();
    history.previous.title = "Return to previous map view";
	history.next.title = "Go to next map view";      
    var panzoom= new OpenLayers.Control.PanZoomBar();
    var pan=new OpenLayers.Control.PanPanel();
    var zoomPan=new OpenLayers.Control.ZoomPanel();
    var zoomIn=new OpenLayers.Control.ZoomIn({title:"Zoom in"});
	var zoomOut=new OpenLayers.Control.ZoomOut({title:"Zoom out"});

    var zoomMax= new OpenLayers.Control.ZoomToMaxExtent({title:"Zoom to the max extent"});
    var zb = new OpenLayers.Control.ZoomBox({title:"Zoom box: Selecting it you can zoom on an area by clicking and dragging."});

	var container = document.getElementById("tools");

    var panel = new OpenLayers.Control.Panel({div: container});
//    panel.addControls([history, history.next, history.previous, panzoom]);
   panel.addControls([history.next, history.previous, zoomIn, zoomOut]);
  
    map.addControls([navigation, history, panel, panzoom, layerSwitch]);
    
//get the user name from $_SESSION
//this also calls the districtcenter function to determine the center of the map
//finally it draws the map and centers it according to the district center
 getSessionUser();

 //   var bogoso = new OpenLayers.LonLat(-2.012644, 5.567).transform(new OpenLayers.Projection("EPSG:4326"),map.getProjectionObject());
//    map.setCenter(globaldistrictcenter, 10); //globaldistrictcenter
    document.getElementById('noneToggle').checked = true;
} //end startNormalUser()


//created by Ekke on 1. August 2013 10:44:55 GMT

//-----------------------------------------------------------------------------
		//function mouseMoveListener() 
		//displays lon lat information in two boxes above the map
		//
//-----------------------------------------------------------------------------

// function mouseMoveListener(event) {
// 	var lonlat = map.getLonLatFromPixel(event.xy);
// 			
// // 	OpenLayers.Util.getElement("lon1").innerHTML = lonlat.lon;
// // 	OpenLayers.Util.getElement("lat1").innerHTML = lonlat.lat;
// // 	
// // 	lonlat.transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
// // 	OpenLayers.Util.getElement("lon2").innerHTML = lonlat.lon;
// // 	OpenLayers.Util.getElement("lat2").innerHTML = lonlat.lat;
// }

  
//-----------------------------------------------------------------------------
		//function onFeatureSelect() 
		//called by the click event on the map
		//it performs a POST to dbaction.php which retrieves the information of the clicked parcel using the upn stored in the feature
		//the popup action is done in handler()
//-----------------------------------------------------------------------------
// ARBEN
function onFeatureSelect(evt) 
{
	feature = evt.feature;
	dbact = "feedUPNinfo";
	var request = OpenLayers.Request.POST(
	{
		url: "php/dbaction.php", 
		data: OpenLayers.Util.getParameterString(
		{
			dbaction: "feedUPNinfo",
			clickfeature: feature.attributes.UPN.value,
			sub: "false"
		}),
		headers: 
		{
			"Content-Type": "application/x-www-form-urlencoded"
		},
		callback: handler
	});	
} 
            
//-----------------------------------------------------------------------------
		//function onFeatureSelectLocalplan() 
		//called by the click event on the localplan layer
		//it displays the attribute information UPN, landuse, and ParcelOf in a popup
//-----------------------------------------------------------------------------
function onFeatureSelectLocalplan(evt) {
	feature = evt.feature;
    var curpos = new OpenLayers.Geometry.Point(feature.geometry.getBounds().getCenterLonLat().lon,feature.geometry.getBounds().getCenterLonLat().lat);

    content = 'UPN: '+feature.attributes.upn+
				'<br>Address: '+feature.attributes.address+
				'<br>Parcel Of: '+feature.attributes.ParcelOf+
				'<br>Area: '+(feature.geometry.getGeodesicArea(proj900913)).toFixed(2)+'sq m'+
				'<br>Land use: '+feature.attributes.landuse;
// 	content += '<br><br><select><option value="property">Property</option><option value="business">Business</option><option value="other">Others</option></select>';
//	content += "<input type='button' class='' value='Add' title='Add details' onclick='addDetails()' >";	
	

		var popup = new OpenLayers.Popup.FramedCloud(
										"featurePopup",
                                        feature.geometry.getBounds().getCenterLonLat(),
                                        new OpenLayers.Size(100,100),
                                        content, 
                                        null, true, onPopupClose);

		feature.popup = popup;
		popup.feature = feature;
		map.addPopup(popup, true);		} 

//-----------------------------------------------------------------------------
		//function onFeatureSelectSub() 
		//called by the click event on the map
		//it performs a POST to dbaction.php which retrieves the information of the clicked parcel
		//we need this function because the UPN information is part of the CDATA description of the .KML file 
		//and not as an individual feature.attribute within the feature
		//the popup action is done in handler()
//-----------------------------------------------------------------------------
function onFeatureSelectSub(evt) {
	feature = evt.feature;
	var request = OpenLayers.Request.POST({
		url: "php/dbaction.php", 
		data: OpenLayers.Util.getParameterString(
		{dbaction: "feedUPNinfo",
		 clickfeature: feature.attributes.description,
		 sub: "true"}),
		headers: {
			"Content-Type": "application/x-www-form-urlencoded"
		},
		callback: handler
	});
} 
//-----------------------------------------------------------------------------
		//function onFeatureSelectFJ() 
		//called by the click event on the map
		//it performs a POST to dbaction.php which retrieves the information of the clicked parcel
		//we need this function because the UPN information is directly accessible as a feature attribute
		//and not as an individual feature.attribute within the feature
		//the popup action is done in handler()
//-----------------------------------------------------------------------------
function onFeatureSelectFJ(evt) {
	feature = evt.feature;
//               alert('features: '+feature.attributes.upn);
	var request = OpenLayers.Request.POST({
		url: "php/dbaction.php", 
		data: OpenLayers.Util.getParameterString(
		{dbaction: "feedUPNinfo",
		 clickfeature: feature.attributes.upn,
		 sub: "false"}),
		headers: {
			"Content-Type": "application/x-www-form-urlencoded"
		},
		callback: handler
	});
} 
//-----------------------------------------------------------------------------
		//function onFeatureSelectBus() 
		//called by the click event on the map
		//it performs a POST to dbaction.php which retrieves the information of the clicked parcel
		//we need this function because the UPN information is directly accessible as a feature attribute
		//and not as an individual feature.attribute within the feature
		//the popup action is done in handler()
//-----------------------------------------------------------------------------
function onFeatureSelectBus(evt) {
	feature = evt.feature;
	//                alert('features: '+feature.attributes.upn);
	var request = OpenLayers.Request.POST({
		url: "php/dbaction.php", 
		data: OpenLayers.Util.getParameterString(
		{dbaction: "feedBusinessinfo",
		 clickfeature: feature.attributes.upn,
		 sub: "false"}),
		headers: {
			"Content-Type": "application/x-www-form-urlencoded"
		},
		callback: handler
	});
} 

//-----------------------------------------------------------------------------
		//function onFeatureSelectcz() 
		//displays information about the clicked Collector Zone
		//
//-----------------------------------------------------------------------------
function onFeatureSelectcz(evt) {
	paraevt = evt;
	feature = evt.feature;
	var intersectedUPNs=0;
	var intersectedParcels=0;
	var revbalance=0;
	var jsonLPVisible = fromLocalplan.getVisibility();
	var jsonPVisible = fromProperty.getVisibility();
  	var jsonBVisible = fromBusiness.getVisibility();

   	if (!jsonLPVisible)
   	{	html = 'Please open the Local Plan! \nThis will enable the calculation of numbers of Parcels';
   		alert(html); 
   	}//else{	
//   	  spinner.spin(target);

		if (jsonPVisible){
			var searchlayer=fromProperty.id;
		}
		else if (jsonBVisible)
		{	var searchlayer=fromBusiness.id; }
		else
		{ 	html = 'Please open either the Properties or the Business Map!\nThis will enable the calculation of numbers of Properties or Businesses';
			alert(html);
		}
		
		// check each feature from Localplan layer if intersecting with the collector zone polygon
			for( var i = 0; i < map.getLayer(fromLocalplan.id).features.length; i++ ) 
			{
				if (feature.geometry.intersects(map.getLayer(fromLocalplan.id).features[i].geometry)) { 
					var checkPoint = new OpenLayers.Geometry.Point(map.getLayer(fromLocalplan.id).features[i].geometry.getBounds().getCenterLonLat().lon,map.getLayer(fromLocalplan.id).features[i].geometry.getBounds().getCenterLonLat().lat);
					if (feature.geometry.containsPoint(checkPoint)){
						intersectedParcels++;
// for debugging
//	if (console && console.log) {
//			console.log(fromLocalplan.features[i].attributes.upn);
//		}

					}
				}			
			}
		if (jsonPVisible || jsonBVisible) {
		// check each feature from layer below if intersecting with the collector zone polygon
			console.log('property layer');
			for( var i = 0; i < map.getLayer(searchlayer).features.length; i++ ) 
			{
				if (feature.geometry.intersects(map.getLayer(searchlayer).features[i].geometry)) { 
					var checkPoint = new OpenLayers.Geometry.Point(map.getLayer(searchlayer).features[i].geometry.getBounds().getCenterLonLat().lon,map.getLayer(searchlayer).features[i].geometry.getBounds().getCenterLonLat().lat);
				if (feature.geometry.containsPoint(checkPoint)){
					intersectedUPNs++;
					revbalance=revbalance+Number(map.getLayer(searchlayer).features[i].attributes.revbalance);
				// for debugging
					if (console && console.log) {
						console.log(map.getLayer(searchlayer).features[i].attributes.upn, intersectedUPNs.toString());
					}
				}
				}			
			}
		}
  // 	}

		content = 'Collector ID: '+feature.attributes.collectorid+
					'<br>Area: '+(feature.geometry.getGeodesicArea(proj900913)/1000000).toFixed(2)+'sq km'+
					'<br>Parcels: '+intersectedParcels.toString();
		if (jsonPVisible){			
		content +=	'<br>Properties: '+intersectedUPNs.toString();
		}else if (jsonBVisible){
		content +=	'<br>Businesses: '+intersectedUPNs.toString();
		}
		content +=	'<br>Outstanding: '+number_format(revbalance, 2, '.', ',')+' GHC'+
					'<br>Zone ID: '+feature.attributes.zoneid;
		content += "<br><input type='button' class='deletezone' value='' title='Delete the selected collector zone' onclick='deletezone(paraevt)' >";	

		var popup = new OpenLayers.Popup.FramedCloud("featurePopup",
						feature.geometry.getBounds().getCenterLonLat(),
						new OpenLayers.Size(100,100),
						content, 
						null, true, onPopupClose);

//		spinner.stop();				
		feature.popup = popup;
		popup.feature = feature;
		map.addPopup(popup, true);
} 

//-----------------------------------------------------------------------------
		//function handler() 
		//gets the request feed from the POST in onFeatureSelect(),onFeatureSelectSub(),onFeatureSelectFJ()
		//and displays the retrieved information in a popup
//-----------------------------------------------------------------------------

// ARBEN   
function handler(request) 
{    
	// erro 5xx and 4xx are same 
	// http://www.w3.org/Protocols/HTTP/HTRESP.html
    if( request.status == 500  || request.status == 413 ) 
	{
        // TODO: do something to calm the user
    }
    
    // the browser's parser may have failed
    if( !request.responseXML ) 
	{
        // get the response from php and read the json encoded data
		feed = JSON.parse(request.responseText);

		var html = '';
		var supnid= '';
		// cleaning the global valiables before use
		global_upn = null;
		global_subupn = [];
		
		if (feed.length==0){
// 		    if (feed[0]['business_name']!='property'){
// 				html += 'Please check the Business Classification!';
// 				var title = 'Business';
// 		    }else {
// 				html += 'Please check the Property Classification!';
// 				var title = 'Property';
// 		    }
			html += '<p>There seems to be discrepancy with the Fee Fixing Information!</p>';
//			html += "<input type='button' value='"+title+" Details' onclick='propertyDetailsOnClick(global_upn, global_subupn, globaldistrictid, "+i+", pushBusiness)' >";	
		}

		for( var i = 0; i < feed.length; i++ ) 
		{			
			global_upn = feed[i]['upn'];
			global_subupn[i] = feed[i]['subupn'];
			pushBusiness = feed[i]['business_name'];
		}

		// build html for each feed item
		for( var i = 0; i < feed.length; i++ ) 
		{		
		    if ((feed[i]['owner']==null) || (feed[i]['owner']=='')){
			html += '<h3>Owner: unknown (Check Property Details)</h3>';
			}
			else{
			html += '<h3>Owner: '+ feed[i]['owner'] +'</h3>';
			}
			html += '<p>Street: '+ feed[i]['streetname'] +'</p>';
			html += '<p>House Nr: '+ feed[i]['housenumber'] +'</p>';
			html += '<p>UPN: '+ feed[i]['upn'] +'</p>';
			html += '<p>SUBUPN: '+ feed[i]['subupn'] +'</p>';
			//html += '<p>YEAR: '+ feed[i]['year'] +'</p>';
			if ((feed[i]['revenue_balance']==0) && (feed[i]['revenue_collected']!=0)){
			html += '<div><strong>Revenue Balance: <FONT COLOR="32CD32">'+ feed[i]['revenue_balance'] +'</FONT> GHS</strong></div>';
			}else{
			html += '<div><strong>Revenue Balance: <FONT COLOR="FF0000">'+ feed[i]['revenue_balance'] +'</FONT> GHS</strong></div>';
			}
			html += '<p>Current rate: '+ feed[i]['rate'] +' GHS</p>';
 			html += '<p>Payment Due: '+ feed[i]['revenue_due'] +' GHS</p>';
			html += '<p>Revenue Collected: '+ feed[i]['revenue_collected'] +' GHS</p>';
//			html += '<p>Date payed: '+ feed[i]['date_payment'] +'</p>';
			switch(parseInt(feed[i]['pay_status'])) {
				case 1:
					html += '<p>Payment Status: <strong><FONT COLOR="FF0000"> DUE</FONT></strong></p>';			
					break;
				case 9:
					html += '<p>Payment Status: <strong><FONT COLOR="32CD32"> PAID</FONT></strong></p>';			
					break;
				default: {	html += '<p>Payment Status: <strong><FONT COLOR="800000"> Unknown</FONT><</strong>/p>';	}

			}		
// 			html += '<p>Payment Status: '+ feed[i]['pay_status'] +'</p>';			
		//check whether called from property or business
		    if (feed[i]['business_name']!='property'){
				html += '<p>Business Name: '+ feed[i]['business_name'] +'</p>';
				var title = 'Business';
		    }else {
				var title = 'Property';
		    }
			html += '<p>Owner Address: '+ feed[i]['owneraddress'] +'</p>';
			html += '<p>Owner Tel: '+ feed[i]['owner_tel'] +'</p>';
			html += '<p>Owner Email: '+ feed[i]['owner_email'] +'</p>';									
			html += "<input type='button' value='Revenue Collection' class='orange-flat-small' onclick='collectRevenueOnClick(global_upn, global_subupn, globaldistrictid, "+i+", pushBusiness)' >";	
			html += "<input type='button' value='"+title+" Details' class='orange-flat-small' onclick='propertyDetailsOnClick(global_upn, global_subupn, globaldistrictid, "+i+", pushBusiness)' >";	
			html += "<input type='button' value='Print Bill' class='orange-flat-small' onclick='printIndividualBillOnClick(global_upn, global_subupn, globaldistrictid, "+i+", pushBusiness)' >";	
			html += '<hr />';
		}

		html += "<input type='button' value='UPN History' class='peter-river-flat-small' onclick='UPNHistoryOnClick()' >";	
		//html += "<button onclick='collectRevenueOnClick(\''+upn+'\', \''+subupn+'\')'>Revenue Collection</button>";	
		//html += ("<input type='button' value='Revenue Collection' />").find('input[type=button]').click( function(){ collectRevenueOnClick(upn, subupn); } );


		var popup = new OpenLayers.Popup.FramedCloud(
										"featurePopup",
                                        feature.geometry.getBounds().getCenterLonLat(),
                                        new OpenLayers.Size(200,200),
                                        html, 
                                        null, true, onPopupClose);

		feature.popup = popup;
		popup.feature = feature;
		map.addPopup(popup, true);		
	}
}  // end of handler function

//-----------------------------------------------------------------------------
		//function collectRevenueOnClick() 
		//  on mouse-click activation to create the window for revenue payments

//-----------------------------------------------------------------------------
function collectRevenueOnClick(global_upn, global_subupn, globaldistrictid, supnid, callproperty) 
{	
	var upn = global_upn;
	var subupn = global_subupn[supnid];
	var ifproperty = callproperty;
	var districtid = globaldistrictid;
	var popupWindow = null;
	if (ifproperty=='property'){
		var title = 'Property Revenue Collection';
		var pageURL = 'php/revenueCollectionForm.php?upn='+upn+'&subupn='+subupn+'&districtid='+globaldistrictid+'&title='+title+'&ifproperty='+ifproperty;
	}else{
		var ifproperty = 'business';
		var title = 'Business Revenue Collection';
		var pageURL = 'php/revenueCollectionForm.php?upn='+upn+'&subupn='+subupn+'&districtid='+globaldistrictid+'&title='+title+'&ifproperty='+ifproperty;
	}
	var w = 450;
	var h = 550;
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    var popupWindow = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);

//	popupWindow = window.open('php/revenueCollectionForm.php?upn='+upn+'&subupn='+subupn, 'Revenue Collection', 'height=500, width=500, left=500, top=200, resizable=yes');	

	if(popupWindow && !popupWindow.closed)
	{
		popupWindow.focus();
	}
	if (ifproperty=='property'){
	    globalpropertychanged=true;
	}else{
	    globalbusinesschanged=true;
	}
	return false;
}	

//-----------------------------------------------------------------------------
		//function propertyDetailsOnClick() 
		//  on mouse-click activation to create the window for revenue payments

//-----------------------------------------------------------------------------
function propertyDetailsOnClick(global_upn, global_subupn, globaldistrictid, supnid, callproperty ) 
{	
	var upn = global_upn;
	var subupn = global_subupn[supnid];
	var ifproperty = callproperty;
	var popupWindow = null;
	if (ifproperty=='property'){
		var pageURL = 'php/propertyDetailsForm.php?upn='+upn+'&subupn='+subupn+'&districtid='+globaldistrictid;
		var title = 'Property Details';
	}else{
		var pageURL = 'php/businessDetailsForm.php?upn='+upn+'&subupn='+subupn+'&districtid='+globaldistrictid;
		var title = 'Business Details';
	}
	var w = 1024;
	var h = 750;
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    var popupWindow = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);
//	popupWindow = window.open('php/propertyDetailsForm.php?upn='+upn+'&subupn='+subupn+'&districtid='+globaldistrictid, 'Property Details', 'height=700, width=1024, left=, top='+top+', left='+left+', resizable=yes');	

	if(popupWindow && !popupWindow.closed)
	{
		popupWindow.focus();
	}

	return false;
}	

//-----------------------------------------------------------------------------
		// function UPNHistoryOnClick() 
		// on mouse-click activation to create the window for UPN History

//-----------------------------------------------------------------------------
function UPNHistoryOnClick( ) 
{	
	var upn = this.global_upn;
	var subupn = this.global_subupn;

	var popupWindow = null;
	popupWindow = window.open('php/Reports/SubupnHistory.php?upn='+upn+'&subupn='+subupn, 'SUBUPN History', 'height=500, width=800, left=500, top=200, resizable=yes');	
	//popupWindow = window.open('php/Reports/UPNHistory.php?upn='+upn+'&subupn='+subupn, 'UPN History', 'height=500, width=800, left=500, top=200, resizable=yes');	

	if(popupWindow && !popupWindow.closed)
	{
		popupWindow.focus();
	}

	return false;
}	

//-----------------------------------------------------------------------------
		//function propertyAnnualBillOnClick() 
		//  on mouse-click activation to create the window for revenue bills printing

//-----------------------------------------------------------------------------
function propertyAnnualBillOnClick() 
{	
	var upn = global_upn;
//	var subupn = global_subupn[supnid];
	var popupWindow = null;
	var pageURL = 'php/Reports/PropertyAnnualBill.php?districtid='+globaldistrictid;
	var title = 'Property Annual Bill Printing';
	var w = 1024;
	var h = 650;
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    var popupWindow = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);

	if(popupWindow && !popupWindow.closed)
	{
		popupWindow.focus();
	}

	return false;
}	
//-----------------------------------------------------------------------------
		//function printIndividualBillOnClick() 
		//  on mouse-click activation to create the window for revenue bills printing

//-----------------------------------------------------------------------------
function printIndividualBillOnClick(global_upn, global_subupn, globaldistrictid, supnid, callproperty ) 
{	
	var upn = global_upn;
	var subupn = global_subupn[supnid];
	var ifproperty = callproperty;
	var popupWindow = null;
	if (ifproperty=='property'){
		var pageURL = 'php/Reports/PropertyAnnualBill_One.php?upn='+upn+'&subupn='+subupn+'&districtid='+globaldistrictid;
		var title = 'Property Bill';
	}else{
		var pageURL = 'php/Reports/BusinessAnnualBill_One.php?upn='+upn+'&subupn='+subupn+'&districtid='+globaldistrictid;
		var title = 'Business Bill';
	}
	var w = 1024;
	var h = 650;
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    var popupWindow = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);

	if(popupWindow && !popupWindow.closed)
	{
		popupWindow.focus();
	}

	return false;
}	

//-----------------------------------------------------------------------------
		//function businessAnnualBillOnClick() 
		//  on mouse-click activation to create the window for revenue bills printing

//-----------------------------------------------------------------------------
function businessAnnualBillOnClick() 
{	
	var upn = global_upn;
//	var subupn = global_subupn[supnid];
	var popupWindow = null;
	var pageURL = 'php/Reports/BusinessAnnualBill.php?districtid='+globaldistrictid;
	var title = 'Business Annual Bill Printing';
	var w = 1024;
	var h = 650;
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    var popupWindow = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);

	if(popupWindow && !popupWindow.closed)
	{
		popupWindow.focus();
	}

	return false;
}	

//-----------------------------------------------------------------------------
		//function billsRegister()
		//  on mouse-click activation to create the window for revenue bills printing

//-----------------------------------------------------------------------------
function billsRegister(target) 
{	
	var upn = global_upn;
	var subupn = this.global_subupn;
	var popupWindow = null;
	var target = target;
	var pageURL = 'php/Reports/BillsRegister.php?target='+target+'&districtid='+globaldistrictid;
	var title = 'Bills Register for Business Licenses';
	var w = 1024;
	var h = 650;
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    var popupWindow = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);

	if(popupWindow && !popupWindow.closed)
	{
		popupWindow.focus();
	}

	return false;
}	


// TODO: refresh parent window
function parent_refresh() 
{
	if( popupWindow.closed )
	{
		// refresh parent window
	}	
}	

//-----------------------------------------------------------------------------
		//function getLocalplanPolygons() 
		//is the onvisibilitychanged event for the Collector Zone Layer
		//it calls dbaction.php - getlocalplan() to retrieve geometry information to draws polygones 
		//from the boundaries stored in the table KML_From_LUPMIS
		//it calls a polyhandler() to actually create the polygones based on the returned data
//-----------------------------------------------------------------------------
function getLocalplanPolygons() {  
//   alert("inside getpolygones");
   var jsonVisible = fromLocalplan.getVisibility();
   if (fromLocalplan.features.length<1) {
	  spinner.spin(target);
	  w.start();
		var request = OpenLayers.Request.POST({
			url: "php/dbaction.php", 
			data: OpenLayers.Util.getParameterString(
			{dbaction: "getlocalplan",
			 districtid: globaldistrictid}),
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			callback: polylocalplanhandler
		});
     }else{
			spinner.stop()
	};           
	showLegend();
} //end of function getLocalplanPolygons

//-----------------------------------------------------------------------------
		//function polyhandler() 
		//is the callback handler for getPropertyPolygons()
		//it takes the request feed from getlocalplan.php and creates polygones on the Layer fromProperty
//-----------------------------------------------------------------------------

function polylocalplanhandler(request) {
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
		var i = 0;
		var revbalance = 0;
		// build geometry for each feed item
		for (var i = 0; i < feed.length; i++) {
			boundary = feed[i]['boundary'].trim();       	
			var coordinates = boundary.split(" ");
			var polypoints = [];
			for (var j=0;j < coordinates.length; j++) {
				points = coordinates[j].split(",");
//debug
//	if (console && console.log) {
//	if (feed[i]['upn']=='608-0614-0033') {
//	if (points.length>1){
//		console.log('ekke', feed[i]['upn'], j, points, boundary);
//		}}
//}
		//sometimes there is garbage at the end of the boundaries and the points array is missing one coordinate. 
		//this will result in polygones missing the final closure and hence the line will not be drawn around the entire polygone
		if (points.length>1){
				point = new OpenLayers.Geometry.Point(points[0], points[1]).transform(projWGS84,proj900913);
				polypoints.push(point);
			}}
				// create some attributes for the feature
			var attributes = {upn: feed[i]['upn'],
								landuse: feed[i]['Landuse'],
								address: feed[i]['Address'],
								ParcelOf: feed[i]['ParcelOf']};
				// create a linear ring by combining the just retrieved points
			var linear_ring = new OpenLayers.Geometry.LinearRing(polypoints);
				//the switch checks on the colouring and land use 
//				document.getElementById("debug2").innerHTML='ekke: '+parseInt(feed[i]['LUPMIS_color'].substr(14,2))+' '+feed[i]['LUPMIS_color'].substr(14,2)+' '+feed[i]['LUPMIS_color'];
				switch(parseInt(feed[i]['LUPMIS_color'].substr(14,2) )) {
				case 1:
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, LUPMIScolour01);		
					break;
				case 2:
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, LUPMIScolour02);		
					break;
				case 3:
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, LUPMIScolour03);		
					break;
				case 4:
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, LUPMIScolour04);		
					break;
				case 5:
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, LUPMIScolour05);		
					break;
				case 6:
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, LUPMIScolour06);		
					break;
				default: {	var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, LUPMISdefault); }
				}	
				//check if UPN is empty or is less than 13 characters, if yes then color the parcel accordingly
				var s=feed[i]['upn'];
			   var checkentry =(s.match(/-/g)||[]).length; //this checks if two - signs are in the entry
			   var checkentry2 =s.length; //this checks if 13 characters are in the entry
			   if ((checkentry != 2) || (checkentry2 < 13)){
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, LUPMISnoUPN);		
				}

			  fromLocalplan.addFeatures([polygonFeature]);

		  } // end of for 
		  fromLocalplan.redraw();
	}
		spinner.stop();
		w.stop();  
// 		document.getElementById("debug1").innerHTML=w.toString();
		showLegend();


//       alert(w.toString());
} // end of function polyhandler

//-----------------------------------------------------------------------------
		//function getPropertyPolygons() 
		//is the onvisibilitychanged event for the property layer
		//it calls dbaction.php - getproperty() to retrieve geometry information to draws polygones 
		//from the boundaries stored in the table KML_From_LUPMIS
		//it calls a polyhandler() to actually create the polygones based on the returned data
//-----------------------------------------------------------------------------
function getPropertyPolygons() {  
//   alert("inside getpolygones");
   var jsonVisible = fromProperty.getVisibility();
//   alert(jsonVisible);
   if ((fromProperty.features.length<1) || (globalpropertychanged)) {
	  spinner.spin(target);
	  w.start();
		var request = OpenLayers.Request.POST({
			url: "php/dbaction.php", 
			data: OpenLayers.Util.getParameterString(
			{dbaction: "getproperty",
			 districtid: globaldistrictid}),
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			callback: polyhandler
		});
     }else{
//      if (jsonVisible) {
//      		document.getElementById("debug2").innerHTML='Outstanding revenue: <br>'+number_format(global_out_property, 2, '.', ',')+' GHC';					
// 		}else{
//      		document.getElementById("debug2").innerHTML=' - ';	
//      		}
			showLegend();
			spinner.stop()
	};           
	
	globalpropertychanged = false;
//	document.getElementById('testbutton').disabled = '';

} //end of function getPropertyPolygons

//-----------------------------------------------------------------------------
		//function polyhandler() 
		//is the callback handler for getPropertyPolygons()
		//it takes the request feed from getlocalplan.php and creates polygones on the Layer fromProperty
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
		var i = 0;
		var revbalance = 0;
		// build geometry for each feed item
		for (var i = 0; i < feed.length; i++) {
			boundary = feed[i]['boundary'].trim();       	
			var coordinates = boundary.split(" ");
			var polypoints = [];
			for (var j=0;j < coordinates.length; j++) {
				points = coordinates[j].split(",");
				if (points.length>1){
					point = new OpenLayers.Geometry.Point(points[0], points[1]).transform(projWGS84,proj900913);
					polypoints.push(point);
				}
			}
			
				// create some attributes for the feature
			var attributes = {upn: feed[i]['upn'],
								revbalance: feed[i]['revenue_balance']};
				// create a linear ring by combining the just retrieved points
			var linear_ring = new OpenLayers.Geometry.LinearRing(polypoints);
				//the switch checks on the payment status and 
			switch(parseInt(feed[i]['status'])) {
				case 1:
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, styleRed);		
				  break;
				case 5:  
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, styleNotYetGreen);		
				  break;
				case 9:  
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, styleGreen);		
				  break;
				default:  
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, styleNeutral);		
				}
				var num = Number(feed[i]['revenue_balance']);
				var n = num.valueOf(); 
				revbalance = revbalance+num;
				global_out_property = revbalance;
// 				document.getElementById("debug2").innerHTML='Outstanding revenue: <br>'+number_format(global_out_property, 2, '.', ',')+' GHC';
			  fromProperty.addFeatures([polygonFeature]);

		  } // end of for 
		  fromProperty.redraw();
	}
		spinner.stop();
		w.stop();  
// 		document.getElementById("debug1").innerHTML=w.toString();
		showLegend();

//       alert(w.toString());
} // end of function polyhandler

//-----------------------------------------------------------------------------
		//function getBusinessPolygons() 
		//is the onvisibilitychanged event for the Buisness Layer
		//it calls dbaction.php - getlocalplan() to retrieve geometry information to draws polygones 
		//from the boundaries stored in the table KML_From_LUPMIS
		//it calls a polyhandler() to actually create the polygones based on the returned data
//-----------------------------------------------------------------------------
function getBusinessPolygons() {  
//   alert("inside getpolygones");
   var jsonBVisible = fromBusiness.getVisibility();
   if ((fromBusiness.features.length<1) || (globalpropertychanged)) {
	  spinner.spin(target);
	  w.start();
		var request = OpenLayers.Request.POST({
			url: "php/dbaction.php", 
			data: OpenLayers.Util.getParameterString(
			{dbaction: "getbusiness",
			 districtid: globaldistrictid}),
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			callback: Businesshandler
		});
     }else{
// 		 if (jsonBVisible) {
// 				document.getElementById("debug2").innerHTML='Outstanding revenue: <br>'+number_format(global_out_business, 2, '.', ',')+' GHC';	
// 				showLegend();
// 		  }else{
// 				document.getElementById("debug2").innerHTML=' - ';
// 				}
		showLegend();
		spinner.stop();
	 }           
	 globalbusinesschanged = false;

} //end of function getBusinessPolygons

//-----------------------------------------------------------------------------
		//function Businesshandler() 
		//is the callback handler for getPropertyPolygons()
		//it takes the request feed from getlocalplan.php and creates polygones on the Layer fromProperty
//-----------------------------------------------------------------------------

function Businesshandler(request) {
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
		var i = 0;
		var revbalance = 0;
		// build geometry for each feed item
		for (var i = 0; i < feed.length; i++) {
			boundary = feed[i]['boundary'].trim();       	
			var coordinates = boundary.split(" ");
			var polypoints = [];
			for (var j=0;j < coordinates.length; j++) {
				points = coordinates[j].split(",");
				if (points.length>1){
					point = new OpenLayers.Geometry.Point(points[0], points[1]).transform(projWGS84,proj900913);
					polypoints.push(point);
				}
			}
				// create some attributes for the feature
			var attributes = {upn: feed[i]['upn'],
								revbalance: feed[i]['revenue_balance']};
				// create a linear ring by combining the just retrieved points
			var linear_ring = new OpenLayers.Geometry.LinearRing(polypoints);
				//the switch checks on the payment status and 
			switch(parseInt(feed[i]['status'])) {
				case 1:
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, styleRed);		
				  break;
				case 5:  
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, styleNotYetGreen);		
				  break;
				case 9:  
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, styleGreen);		
				  break;
				default:  
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, styleNeutral);		
				}
				var num = Number(feed[i]['revenue_balance']);
				var n = num.valueOf(); 
				revbalance = revbalance+num;
				global_out_business = revbalance;
// 				document.getElementById("debug2").innerHTML='Outstanding revenue: <br>'+number_format(global_out_business, 2, '.', ',')+' GHC';					
//					document.getElementById("debug2").innerHTML=typeof(num);
				
// for debugging
// 	if (console && console.log) {
// //	 		console.log('getbusiness poly');
// 			if (feed[i]['id']=='4928'){
// 			console.log(i,';',feed[i]['id'], ';',num);}
// 			console.log(i,';',feed[i]['id'],';',feed[i]['upn'],';',global_out_business,';', num, ';',revbalance);
// 		}
// 			
				
			  fromBusiness.addFeatures([polygonFeature]);

		  } // end of for 
		  fromBusiness.redraw();
	}
		spinner.stop();
		w.stop();  
// 		document.getElementById("debug1").innerHTML=w.toString();
		showLegend();


//       alert(w.toString());
} // end of function Businesshandler

//-----------------------------------------------------------------------------
		//function onPopupClose() 
		//used to close the popups in various onSelectFeature events
		//
//-----------------------------------------------------------------------------
function onPopupClose(evt) {
        select.unselectAll();
}

//-----------------------------------------------------------------------------
		//function onFeatureUnselect() 
		//used to close the popups in various onSelectFeature events
		//
//-----------------------------------------------------------------------------
function onFeatureUnselect(event) {
		var feature = event.feature;
		if(feature.popup) {
			map.removePopup(feature.popup);
			feature.popup.destroy();
			delete feature.popup;
		}
}   
            
//-----------------------------------------------------------------------------
		//function onFeatureAddedCZ() 
		//Inserts feature information in the database by calling dbaction.php:insertCZ
		//Response is handled by inserthandler()
//-----------------------------------------------------------------------------
function onFeatureAddedCZ(event) {
	var feature = event.feature;
  	//the colors are defined at the beginning of the main .js file
	globalfeatureid = feature.id;
	if (globalinsertCZ){
		var request = OpenLayers.Request.POST({
			url: "php/dbaction.php", 
			data: OpenLayers.Util.getParameterString(
			{dbaction: "insertCZ",
			 zoneid: "",
			 districtid: globaldistrictid, // "234",
			 polygon: feature.geometry,
			 collector: "",
			 zonecolour: feature.style.fillColor}),
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			callback: inserthandler
	});
	} //else{alert(feature.attributes.zoneid+" insert not done");	}
}      

//-----------------------------------------------------------------------------
		//function onFeatureModifiedCZ() 
		//Inserts feature information in the database by calling dbaction.php:insertCZ
		//Response is handled by inserthander()
//-----------------------------------------------------------------------------
function onFeatureModifiedCZ(event) {
	var featureCZ = event.feature;
	globalfeatureid = featureCZ.id;
	var districtid = globaldistrictid; //"234";
  	//the colors are defined at the beginning of the main .js file
	var request = OpenLayers.Request.POST({
		url: "php/dbaction.php", 
		data: OpenLayers.Util.getParameterString(
		{dbaction: "insertCZ",
		 zoneid: featureCZ.attributes.zoneid,
		 districtid: districtid,
		 polygon: featureCZ.geometry,
		 collectorid: featureCZ.attributes.collectorid,
		 zonecolour: featureCZ.style.fillColor}),
		headers: {
			"Content-Type": "application/x-www-form-urlencoded"
		},
		callback: inserthandler
	});
}  

//-----------------------------------------------------------------------------
		//function inserthandler() 
		//is the handler for onFeatureAddedCZ and reacts on error or success messages from dbaction.php:insertCZ
		//
//-----------------------------------------------------------------------------
function inserthandler(request) {

	// the server could report an error
		if(request.status == 500) {
		// do something to calm the user
		alert('The database server is reporting an error.<br>Please inform the system administrator');
	}
	// the server could say you sent too much stuff
	if(request.status == 413) {
		// tell the user to trim their request a bit
		alert('The database server is reporting too much information was sent<br>Please inform the system administrator');
	}
	// the browser's parser may have failed
	var html="";
	var zoneidfromdb="";	

	if(!request.responseXML) {
		// get the response from php and read the json encoded data
	   feed=JSON.parse(request.responseText);
	   // build html for each feed item
		for (var i = 0; i < feed.length; i++) {
			html += feed[i]['text'];
			zoneidfromdb = feed[i]['zoneid'];
			collectorid = feed[i]['collectorid'];
			districtid = feed[i]['districtid'];
			}
		feature = colzones.getFeatureById(globalfeatureid);
		attributesCZ = {zoneid: zoneidfromdb,
						collectorid: collectorid,
						districtid: districtid};	

		feature.attributes = attributesCZ;
		alert(html);	
	} // else{  alert('inside inserthandler');}

		colzones.redraw();	

} 
// end of function inserthandler


//-----------------------------------------------------------------------------
		//function onSketchComplete() 
		//changes the color of each created collector zone polygone and stores the information in the database
		//
//-----------------------------------------------------------------------------
function onSketchComplete(event) {
//alert("onSketchComplete");
    globalinsertCZ=true;
      
	var feature = event.feature;
		attributes = {zoneid: feature.id};		
		feature.attributes = attributes;

//	var gps = feature.geometry;
	//the colors are defined at the beginning of the main .js file
	if(colzones.features.length>0){
	  icolor=colzones.features.length+1;
	  colzonecolor = colors[icolor];
	  feature.style = {fill: true, fillColor: colzonecolor, fillOpacity: 0.2};
	}else{
	feature.style = {fill: true, fillColor: startcolor, fillOpacity: 0.2};
	colzonecolor=startcolor;
	}
	colzones.redraw();	
}      

//-----------------------------------------------------------------------------
		//function onVisibiltyChangedcz() 
		//is used for Collector Zones and
		//makes the drawing controls visible next to the right side of the map
//-----------------------------------------------------------------------------
function onVisibiltyChangedcz(){
  var czVisible = colzones.getVisibility();
 if (czVisible) {
 globalinsertCZ = false;
 document.getElementById("controls").style.visibility="visible";
 var request = OpenLayers.Request.POST({
		url: "php/dbaction.php", 
		data: OpenLayers.Util.getParameterString(
		{dbaction: "getCZ",
		 districtid: globaldistrictid}), 
		headers: {
			"Content-Type": "application/x-www-form-urlencoded"
		},
		callback: getCZhandler
	});
 }else{
 document.getElementById("controls").style.visibility="hidden";
 }
}

//-----------------------------------------------------------------------------
		//function getCZhandler() 
		//is the handler for onFeatureAddedCZ and reacts on error or success messages from dbaction.php:insertCZ
		//this function creates the features, adds the attributes and displays them as a new layer 'colzones' on the map 
//-----------------------------------------------------------------------------
function getCZhandler(request) {
	// the server could report an error
		if(request.status == 500) {
		// do something to calm the user
		alert('The database server is reporting an error.<br>Please inform the system administrator');
	}
	// the server could say you sent too much stuff
	if(request.status == 413) {
		// tell the user to trim their request a bit
		alert('The database server is reporting too much information was sent<br>Please inform the system administrator');
	}
	// the browser's parser may have failed
	if(!request.responseXML) {
		// get the response from php and read the json encoded data
	   feed=JSON.parse(request.responseText);
	   // build html for each feed item
		for (var i = 0; i < feed.length; i++) {
			zoneid = feed[i]['zoneid'];
			districtid = feed[i]['districtid'];
			collectorid = feed[i]['collectorid'];
			zonecolour = feed[i]['zone_colour'];
			polygon = feed[i]['polygon'];
			fstyle = new OpenLayers.Style({fill: true, fillColor: zonecolour, fillOpacity: 0.2});
			attributesCZ = {zoneid: zoneid, districtid: districtid,	collectorid: collectorid};	
			if (polygon.search('POLYGON')>-1){
				var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.fromWKT(polygon), attributesCZ, {fill: true, fillColor: zonecolour, fillOpacity: 0.2});		
			}else{
				boundary = feed[i]['polygon'].trim();       	
				var coordinates = boundary.split(" ");
				var polypoints = [];
				for (var j=0;j < coordinates.length; j++) {
					points = coordinates[j].split(",");
					if (points.length>1){
						point = new OpenLayers.Geometry.Point(points[0], points[1]).transform(projWGS84,proj900913);
						polypoints.push(point);
					}
				}
				// create a linear ring by combining the just retrieved points
				var linear_ring = new OpenLayers.Geometry.LinearRing(polypoints);
				var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributesCZ, {fill: true, fillColor: zonecolour, fillOpacity: 0.2});		
			}	
		    colzones.addFeatures([polygonFeature]);
			} //end for 
	} //end if		

		  colzones.redraw();	

} 
// end of function getCZhandler

 		
//-----------------------------------------------------------------------------
		//function update() 
		//is used for Collector Zones and
		//is used by the drawing controls 
//-----------------------------------------------------------------------------
function update() {
	select.deactivate();
	// reset modification mode
	controls.modify.mode = OpenLayers.Control.ModifyFeature.RESHAPE;
	var rotate = document.getElementById("rotate").checked;
	if(rotate) {
		controls.modify.mode |= OpenLayers.Control.ModifyFeature.ROTATE;
	}
	var resize = document.getElementById("resize").checked;
	if(resize) {
		controls.modify.mode |= OpenLayers.Control.ModifyFeature.RESIZE;
		var keepAspectRatio = document.getElementById("keepAspectRatio").checked;
		if (keepAspectRatio) {
			controls.modify.mode &= ~OpenLayers.Control.ModifyFeature.RESHAPE;
		}
	}
	var drag = document.getElementById("drag").checked;
	if(drag) {
		controls.modify.mode |= OpenLayers.Control.ModifyFeature.DRAG;
	}
	if (rotate || drag) {
		controls.modify.mode &= ~OpenLayers.Control.ModifyFeature.RESHAPE;
	}
	controls.modify.createVertices = document.getElementById("createVertices").checked;
	var sides = parseInt(document.getElementById("sides").value);
	sides = Math.max(3, isNaN(sides) ? 0 : sides);
	controls.regular.handler.sides = sides;
	var irregular =  document.getElementById("irregular").checked;
	controls.regular.handler.irregular = irregular;
}


//-----------------------------------------------------------------------------
		//function deletezone() 
		//is used to delete Collector Zones 
		//
//-----------------------------------------------------------------------------
function deletezone(evt) {
 feature = evt.feature;
 var request = OpenLayers.Request.POST({
		url: "php/dbaction.php", 
		data: OpenLayers.Util.getParameterString(
		{dbaction: "deleteCZ",
		 zoneid: feature.attributes.zoneid}), 
		headers: {
			"Content-Type": "application/x-www-form-urlencoded"
		},
		callback:  alert('Zone with id: '+feature.attributes.zoneid+ ' was deleted from database')
	});
 map.removePopup(feature.popup);
 colzones.removeFeatures( [ feature ] );
}    


// end of function getCZhandler
//-----------------------------------------------------------------------------
		//function toggleControl() 
		//is used for Collector Zones and
		//is used by the drawing controls 
//-----------------------------------------------------------------------------
function toggleControl(element) {
	select.deactivate();
	for(key in controls) {
		var control = controls[key];
		if(element.value == key && element.checked) {
			control.activate();
		} else {
			control.deactivate();
		}
	}
			if(element.value == 'deletezone'){
			deletezone(element);
			}
	if(element.id == "noneToggle"){
			select.activate();				
		}
}    

function updateClock(watch) {
    	OpenLayers.Util.getElement("tools").innerHTML = watch.toString();
}

//-----------------------------------------------------------------------------
		//function getSessionUser() 
		//is the first function to be called and gets the PHP $SESSION info
		//calls sessionuserhandler
//-----------------------------------------------------------------------------
function getSessionUser(){
var request = OpenLayers.Request.GET({
    url: "php/getsession.php", 
    callback: sessionuserhandler
});
}
// end of function getSessionUser

//-----------------------------------------------------------------------------
		//function sessionuserhandler() 
		//is the handler for getSessionUser 
		//
//-----------------------------------------------------------------------------
function sessionuserhandler(request) {

// the browser's parser may have failed
if(!request.responseXML) {
	var html ='';
	var userdistrict='';
	var userdistrictname='';
	var districtboundary='';
	var numberOfParcels='';
	var numberOfProperty='';
	var numberOfBusiness='';
		// get the response from php and read the json encoded data
		feed=JSON.parse(request.responseText);
		for (var i = 0; i < feed.length; i++) {
			html += feed[i]['username'];
			userdistrict += feed[i]['userdistrict'];
			userdistrictname += feed[i]['userdistrictname'];
			numberOfParcels += feed[i]['numberOfParcels'];
			numberOfProperty += feed[i]['numberOfProperty'];
			numberOfBusiness += feed[i]['numberOfBusiness'];
			districtboundary += feed[i]['districtboundary']};

	//check if there is a session, if not log out			
	if(userdistrictname=='null') {
		setTimeout("location.href = 'logout.php';",1000);
	}			 

	document.getElementById("wcUser").innerHTML='Welcome: '+html;
	globaldistrictid=userdistrict;
	document.getElementById("districtname").innerHTML=userdistrictname;
	document.getElementById("stat1").innerHTML=number_format(numberOfParcels, 0, '.', ',');
	document.getElementById("stat2").innerHTML=number_format(numberOfProperty, 0, '.', ',');
	document.getElementById("stat3").innerHTML=number_format(numberOfBusiness, 0, '.', ',');

	//Now we center the map according to the boundary 
	getdistrictcenter(districtboundary);
} // else{  alert('inside inserthandler');}
} 
// end of function sessionuserhandler

//-----------------------------------------------------------------------------
		//function getFiscalStats() 
		//get the info for Due, Paid, and Balance
		//calls fiscalstatshandler
//-----------------------------------------------------------------------------
function getFiscalStats(){
spinner.spin(target);
var request = OpenLayers.Request.GET({
    url: "php/getfiscalstats.php", 
    callback: fiscalstatshandler
});
}
// end of function getSessionUser

//-----------------------------------------------------------------------------
		//function fiscalstatshandler() 
		//is the handler for getFiscalStats 
		//
//-----------------------------------------------------------------------------
function fiscalstatshandler(request) {

// the browser's parser may have failed
if(!request.responseXML) {
	var sumPropertyBalance='';
	var sumPropertyPaid='';
	var sumPropertyDue='';
	var sumBusinessBalance='';
	var sumBusinessPaid='';
	var sumBusinessDue='';
		// get the response from php and read the json encoded data
		feed=JSON.parse(request.responseText);
		for (var i = 0; i < feed.length; i++) {
			sumPropertyBalance += feed[i]['sumPropertyBalance'];
			sumPropertyPaid += feed[i]['sumPropertyPaid'];
			sumPropertyDue += feed[i]['sumPropertyDue'];
			sumBusinessBalance += feed[i]['sumBusinessBalance'];
			sumBusinessPaid += feed[i]['sumBusinessPaid'];
			sumBusinessDue += feed[i]['sumBusinessDue'];
			};

	document.getElementById("fis1").innerHTML=number_format(sumPropertyDue, 2, '.', ',')+' GHC';
	document.getElementById("fis2").innerHTML=number_format(sumPropertyPaid, 2, '.', ',')+' GHC';
	document.getElementById("fis3").innerHTML=number_format(sumPropertyBalance, 2, '.', ',')+' GHC';
	document.getElementById("fis4").innerHTML=number_format(sumBusinessDue, 2, '.', ',')+' GHC';
	document.getElementById("fis5").innerHTML=number_format(sumBusinessPaid, 2, '.', ',')+' GHC';
	document.getElementById("fis6").innerHTML=number_format(sumBusinessBalance, 2, '.', ',')+' GHC';

	document.getElementById("fisprop").value='Property Rates';
	document.getElementById("fisbus").value='Business Permits';

} 
spinner.stop();
} 
// end of function fiscalstatshandler

//-----------------------------------------------------------------------------
		//function getdistrictcenter(districtboundary) 
		//defines a polygon from districtboundary and centres the map accordingly
		//
//-----------------------------------------------------------------------------
function getdistrictcenter(districtboundary){
// build geometry for each feed item
	var coordinates = districtboundary.split(" ");
	var polypoints = [];
	for (var j=0;j < coordinates.length; j++) {
		points = coordinates[j].split(",");
		point = new OpenLayers.Geometry.Point(points[0], points[1]).transform(new OpenLayers.Projection("EPSG:4326"),map.getProjectionObject()); //transform(projWGS84,proj900913);
		polypoints.push(point);
	}
		// create a linear ring by combining the just retrieved points
	var linear_ring = new OpenLayers.Geometry.LinearRing(polypoints);
	var attributes = {districtid: globaldistrictid};

	//the switch checks on the payment status and 
	var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, styleDistrictBoundary);		
	var districtcenter = new OpenLayers.LonLat(polygonFeature.geometry.getBounds().getCenterLonLat().lon,polygonFeature.geometry.getBounds().getCenterLonLat().lat);
//	fromDistrict.addFeatures([polygonFeature]);
	map.setCenter(districtcenter, 12); //globaldistrictcenter
//	fromDistrict.redraw();
}
// end of function getdistrictcenter

//-----------------------------------------------------------------------------
		//function searchupn() 
		//is called by the SEARCH button in the main windows 
		//
//-----------------------------------------------------------------------------
function searchupn() {
  var sUPN = document.getElementById('searchBox').value;
//   var sName = document.getElementById('searchName').value;
//   var sStreet = document.getElementById('searchStreet').value;
   sUPN = sUPN.trim();
  var jsonLPVisible = fromLocalplan.getVisibility();
  var jsonPVisible = fromProperty.getVisibility();
  var jsonBVisible = fromBusiness.getVisibility();
   if (jsonPVisible){
   		var searchlayer=fromProperty.id;
   }
   else if (jsonBVisible)
   {	var searchlayer=fromBusiness.id; }
   else if (jsonLPVisible)
   {	var searchlayer=fromLocalplan.id; }
   else
   { 	html = 'Please open either the Local Plan, Properties or the Business Map! \n Search for '+sUPN+' is only possible in these Maps';
   		alert(html);
   	}
   if (sUPN){
	   var checkentry =(sUPN.match(/-/g)||[]).length; //this checks if two - signs are in the entry
	   var checkentry2 =sUPN.length; //this checks if 13 characters are in the entry
	   if ((checkentry != 2) || (checkentry2 < 13)){
		html = 'Please check your entry! \n'+sUPN+'\nappears to be an incorrect UPN';
		alert(html);}
	
		foundUPN=map.getLayer(searchlayer).getFeaturesByAttribute('upn', sUPN);
		if (foundUPN.length > 0){
		for (var i = 0; i < foundUPN.length; i++) {
	//			map.getLayer(searchlayer).drawFeature(map.getLayer(searchlayer).getFeatureById(foundUPN[i].id), {fillColor: "#99FF33", fillOpacity: 0.8, strokeColor: "#00ffff"});			
			   var curpos = new OpenLayers.LonLat(map.getLayer(searchlayer).getFeatureById(foundUPN[i].id).geometry.getBounds().getCenterLonLat().lon,map.getLayer(searchlayer).getFeatureById(foundUPN[i].id).geometry.getBounds().getCenterLonLat().lat);
			   map.panTo(curpos);
			   map.setCenter(curpos, 17);
				select.select(map.getLayer(searchlayer).getFeatureById(foundUPN[i].id));
				}
		}else{
		html = 'UPN: '+sUPN+' could not be found!\nPlease check your entry';
		alert(html);
		}
	}
} 
// end of function searchupn
//-----------------------------------------------------------------------------
		//function searchOther() 
		//is called by the SEARCH button in the main windows 
		//
//-----------------------------------------------------------------------------
function searchOther() {

  var sUPN = '';
  var sString = document.getElementById('searchOther').value;
   sString = sString.trim();
   var radios = document.getElementsByName('target');
    var value = -1;
    //check which radio button was selected
    for (var i = 0; i < radios.length; i++) 
    {
        if (radios[i].checked) 
        {
            value = i;
            break;
        }
    }   
   var starget = '';
   var goodtogo = true;
  
 if (sString.length == 0){
	alert('Please enter either a Street or Owner name into the entry field');
	goodtogo = false;
	}else{	
	goodtogo = true;
	} 
 if (value == -1){
	alert('Please select either Street or Owner from the radio buttons');
	goodtogo = false;
	}else{	
		goodtogo = true;
	} 
 
//read the value from the radio button and assign a target table
 if (value == 0){
	starget='street';
	}else if (value == 1) {
	starget='owner';
	}
	
	
  var jsonLPVisible = fromLocalplan.getVisibility();
  var jsonPVisible = fromProperty.getVisibility();
  var jsonBVisible = fromBusiness.getVisibility();
   if (jsonPVisible){
   		var searchlayerid=fromProperty.id;
   		var searchlayer='property';
   }
   else if (jsonBVisible)
   {	var searchlayer='business';
	    var searchlayerid=fromBusiness.id;}
   else if (jsonLPVisible)
   {	var searchlayer='localplan';
		var searchlayerid=fromLocalplan.id;}
   else
   { 	html = 'Please open either the Properties or the Business Map! \n Search for >'+sString+'< is only possible in these Maps';
   		alert(html);
   	}
   	
//alert(starget+' '+value+' '+sString+' '+searchlayer);

//goodtogo=false;
if (goodtogo==true) {
spinner.spin(target);
	

   	var handlerParameter = {searchlayerid: searchlayerid, spinner: spinner};
// 		url: "php/dbaction.php", 

// if we are here, then all is good to start the search   	
   	 var request = OpenLayers.Request.POST({
		url: "php/dbaction.php", 
		data: OpenLayers.Util.getParameterString(
		{dbaction: "searchOther",
		 districtid: globaldistrictid,
		 starget: starget,
		 sString: sString,
		 searchlayer: searchlayer}), 
		headers: {
			"Content-Type": "application/x-www-form-urlencoded"
		},
		callback: searchOtherHandler,
		scope: handlerParameter
	});
} //end goodtogo
} 
// end of function searchOther

//-----------------------------------------------------------------------------
		//function searchOtherHandler() 
		//is the handler for onFeatureAddedCZ and reacts on error or success messages from dbaction.php:insertCZ
		//this function creates the features, adds the attributes and displays them as a new layer 'colzones' on the map 
//-----------------------------------------------------------------------------
function searchOtherHandler(request) {
  var sUPN = '';
  var jsonLPVisible = fromLocalplan.getVisibility();
  var jsonPVisible = fromProperty.getVisibility();
  var jsonBVisible = fromBusiness.getVisibility();
   if (jsonPVisible){
   		var searchlayer=fromProperty.id;
   }
   else if (jsonBVisible)
   {	var searchlayer=fromBusiness.id; }
   else if (jsonLPVisible)
   {	var searchlayer=fromLocalplan.id; }

	// the server could report an error
		if(request.status == 500) {
		// do something to calm the user
		alert('The database server is reporting an error.<br>Please inform the system administrator');
	}
	// the server could say you sent too much stuff
	if(request.status == 413) {
		// tell the user to trim their request a bit
		alert('The database server is reporting too much information was sent<br>Please inform the system administrator');
	}
	// the browser's parser may have failed
	if(!request.responseXML) {
		// get the response from php and read the json encoded data
	   feed=JSON.parse(request.responseText);
	   // build html for each feed item
// 	   	   var html='';
// 		for (var i = 0; i < feed.length; i++) {
// 			html += feed[i]['upn'];
// 			}
// 
//  alert('done: '+html);

		for (var i = 0; i < feed.length; i++) {
			sUPN = feed[i]['upn'];
//			fstyle = new OpenLayers.Style({fill: true, fillColor: zonecolour, fillOpacity: 0.2});
 			foundUPN=map.getLayer(searchlayer).getFeaturesByAttribute('upn', sUPN);
//			alert(' '+searchlayer+' '+fromBusiness.id+' '+map.getLayer(searchlayer).getFeaturesByAttribute('upn', sUPN));
			if (foundUPN.length > 0){
			for (var j = 0; j < foundUPN.length; j++) {
 						map.getLayer(searchlayer).drawFeature(map.getLayer(searchlayer).getFeatureById(foundUPN[j].id), {fillColor: "#99FF33", fillOpacity: 0.8, strokeColor: "#00ffff"});			
					}
			}else{
			html = 'UPN: '+sUPN+' could not be found!\nPlease check your entry';
			alert(html);
			}
		} //end for 
	} //end if		
// 
// 		  map.getLayer(searchlayer).redraw();	
  spinner.stop();
} 
// end of function searchOtherResult

 	

//-----------------------------------------------------------------------------
		//function tableshow() 
		//opens a window to show tabular data about the corresponding map
		//
//-----------------------------------------------------------------------------
function tableshow() {
  var jsonPVisible = fromProperty.getVisibility();
  var jsonBVisible = fromBusiness.getVisibility();
  var popupWindow = null;
  var pageURL = 'php/showtable.php?districtid='+globaldistrictid;

  
   if (jsonPVisible && jsonBVisible){
   		html = 'Table view is only available for data from one map.  \nPlease close either the Properties or the Business Map! ';
   		alert(html);
   }
   else if (jsonBVisible || jsonPVisible)  {
		popupWindow = window.open(pageURL,"Table View", 'border=0, status=0, height=500, width=1000, left=500, top=200, resizable=no,location=no,menubar=no,status=no,toolbar=no');	
	}
   else
   { 	html = 'Please open either the Properties or the Business Map! \nTable view is only available for data from one of these two Maps';
   		alert(html);
   	}
   
	if(popupWindow && !popupWindow.closed)
	{
		popupWindow.focus();
	}

	return false;

} 
// end of function tableshow

//-----------------------------------------------------------------------------
		//function xlsexport() 
		//opens a window to export tabular data to Excel
		//
//-----------------------------------------------------------------------------
function xlsexport() {
  var jsonPVisible = fromProperty.getVisibility();
  var jsonBVisible = fromBusiness.getVisibility();
  	var w = 1000;
	var h = 550;
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
	var pageURL = 'php/openXLSreports.php?districtid='+globaldistrictid;

  
//    if (jsonPVisible && jsonBVisible){
//    		html = 'Table view is only available for data from one map.  \nPlease close either the Properties or the Business Map! ';
//    		alert(html);
//    }
//    else if (jsonBVisible || jsonPVisible)  {
 		popupWindow = window.open(pageURL,"Excel Reports", 'border=0, status=0, width='+w+', height='+h+', top='+top+', left='+left+', resizable=no,location=no,menubar=no,status=no,toolbar=no');	
// 	}
//    else
//    { 	html = 'Please open either the Properties or the Business Map! \nTable view is only available for data from one of these two Maps';
//    		alert(html);
//    	}
   
	if(popupWindow && !popupWindow.closed)
	{
		popupWindow.focus();
	}

	return false;

} 
// end of function xlsexport


//-----------------------------------------------------------------------------
		//function uploadkml() 
		//opens a window to select a kml file for uploading into the db
		//
//-----------------------------------------------------------------------------
function uploadkml(){
	var pageURL = 'php/uploadKMLfopen.php';
	var title = 'Upload KML';
	var w = 500;
	var h = 250;
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    var popupWindow = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);

	if(popupWindow && !popupWindow.closed)
	{
		popupWindow.focus();
	}

	return false;
}

function uploadxls(){
	var pageURL = 'php/uploadXLSfopen.php';
	var title = 'Upload XLS';
	var w = 1024;
	var h = 650;
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    var popupWindow = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);

	if(popupWindow && !popupWindow.closed)
	{
		popupWindow.focus();
	}

	return false;
}

//-----------------------------------------------------------------------------
		//function uploadScannedData() 
		//opens a window to select a csv, xls, xlsx file for uploading into the db
		//
//-----------------------------------------------------------------------------
function uploadScannedData() {
	var pageURL = 'php/uploadScanDatafopen.php';
	var title = 'Upload XLS';
	var w = 1024;
	var h = 650;
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
    var popupWindow = window.open (pageURL, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);

	if(popupWindow && !popupWindow.closed)
	{
		popupWindow.focus();
	}

	return false;

} 
// end of function tableshow

//-----------------------------------------------------------------------------
		//function addDetails() 
		// used in the local plan to add property or business or ... info to a UPN
		//opens a window for adding details to UPN based on the selection from a dropdown list
		//
//-----------------------------------------------------------------------------
function addDetails() {

	return false;

} 
// end of function tableshow

function checkConnection(e) {
		if (doesConnectionExist() == true) {
			alert("connection exists!");
		} else {
			alert("connection doesn't exist!");
		}
	}

//-----------------------------------------------------------------------------
		//function doesConnectionExist() 
		//is used to determine, if the user has Internet access or not
		//if not, maps from OpenStreetmap or Google are not loaded
//-----------------------------------------------------------------------------
function doesConnectionExist() {
    var xhr = new XMLHttpRequest();
//    var file = "http://www.co-gmbh.com/Bild9.png";
    var file = "img/marker-green.png"; //http://localgis.local/LRE/
    var randomNum = Math.round(Math.random() * 10000);
    
    xhr.open('HEAD', file + "?rand=" + randomNum, false);
    
    try {
    	xhr.send();
//	    	alert(xhr.status);
    	
	    if (xhr.status >= 200 && xhr.status < 304) {
// 	    	alert(xhr.status);
	        return true;
	    } else {
// 	    	alert(xhr.status);
	        return false;
	    }
    } catch (e) {
    	return false;
    }
}
//-----------------------------------------------------------------------------
		//function showLegend() 
		//is used to show a legend for the colours used in the map
		//
//-----------------------------------------------------------------------------
function showLegend() {
	var jsonLPVisible = fromLocalplan.getVisibility();
	var jsonPVisible = fromProperty.getVisibility();
	var jsonBVisible = fromBusiness.getVisibility();

	var canv=document.getElementById("myCanvas");
	var ctx=canv.getContext("2d");
	var ystart=2;
	var rectHeight=10;
	var rectWidth=15;
	ctx.clearRect(0,0,200,200);
	ctx.font="12px Verdana";

	if ((jsonPVisible) || (jsonBVisible)){
		document.getElementById("legend").style.visibility="visible";
		document.getElementById("legend").innerHTML="Legend:<br>";
		document.getElementById("myCanvas").style.visibility="visible";
		ctx.fillStyle=styleGreen['fillColor'];
		ctx.fillRect(2,ystart,rectWidth,rectHeight); 
		ctx.fillText("No outstanding items",20,ystart+(rectHeight));
		ystart=ystart+20;
		ctx.fillStyle=styleRed['fillColor'];
		ctx.fillRect(2,ystart,rectWidth,rectHeight); 
		ctx.fillText("Outstanding items",20,ystart+(rectHeight)); 
		ystart=ystart+20;
		ctx.fillStyle=styleNotYetGreen['fillColor'];
		ctx.fillRect(2,ystart,rectWidth,rectHeight); 
		ctx.fillText("Some outstanding items",20,ystart+(rectHeight)); 
		ystart=ystart+20;
		ctx.fillStyle=styleNeutral['fillColor'];
		ctx.fillRect(2,ystart,rectWidth,rectHeight); 
		ctx.fillStyle="#000000";//styleNeutral['fillColor'];
		ctx.fillText("No fiscal information available",20,ystart+(rectHeight)); 
	}
	else if (jsonLPVisible)
	{		
		document.getElementById("legend").style.visibility="visible";
		document.getElementById("legend").innerHTML="Legend:<br>";
		document.getElementById("myCanvas").style.visibility="visible";
		ctx.fillStyle=LUPMISdefault['strokeColor'];
		ctx.fillRect(2,ystart,rectWidth,rectHeight); 
		ctx.fillText("No land use available",20,ystart+(rectHeight));
		ystart=ystart+20;
		ctx.fillStyle=LUPMIScolour01['strokeColor'];
		ctx.fillRect(2,ystart,rectWidth,rectHeight); 
		ctx.fillText("Residential high density",20,ystart+(rectHeight));
		ystart=ystart+20;
		ctx.fillStyle=LUPMIScolour02['strokeColor'];
		ctx.fillRect(2,ystart,rectWidth,rectHeight); 
		ctx.fillText("Commercial",20,ystart+(rectHeight));
		ystart=ystart+20;
		ctx.fillStyle=LUPMIScolour03['strokeColor'];
		ctx.fillRect(2,ystart,rectWidth,rectHeight); 
		ctx.fillText("Place of worship",20,ystart+(rectHeight));
		ystart=ystart+20;
		ctx.fillStyle=LUPMIScolour04['strokeColor'];
		ctx.fillRect(2,ystart,rectWidth,rectHeight); 
		ctx.fillText("JHS",20,ystart+(rectHeight));
		ystart=ystart+20;
		ctx.fillStyle=LUPMIScolour05['strokeColor'];
		ctx.fillRect(2,ystart,rectWidth,rectHeight); 
		ctx.fillText("Parks",20,ystart+(rectHeight));
		ystart=ystart+20;
		ctx.fillStyle=LUPMIScolour06['strokeColor'];
		ctx.fillRect(2,ystart,rectWidth,rectHeight); 
		ctx.fillText("Artisan production",20,ystart+(rectHeight));
		ystart=ystart+20;
		ctx.fillStyle=LUPMISnoUPN['fillColor'];
		ctx.fillRect(2,ystart,rectWidth,rectHeight); 
		ctx.fillText("UPN wrong or inconsistent",20,ystart+(rectHeight));
		ystart=ystart+20;
	 }
	else
	{ 
	 document.getElementById("myCanvas").style.visibility="hidden";
 	 document.getElementById("legend").style.visibility="hidden";
	}
}
//-----------------------------------------------------------------------------
		//function updateCZinPropBus() 
		//is used to update the collector zones in property and business
		//
//-----------------------------------------------------------------------------
function updateCZinPropBus() {
	var jsonLPVisible = fromLocalplan.getVisibility();
	var jsonPVisible = fromProperty.getVisibility();
	var jsonBVisible = fromBusiness.getVisibility();
	var jsonCZVisible = colzones.getVisibility();
	var JSONObject= [];
	var JSONObjectBus= [];
//start spinner
	spinner.spin(target);

// 	if (!jsonLPVisible){
// 		fromLocalplan.setVisibility(true);
// 	}
	if (!jsonPVisible){
		fromProperty.setVisibility(true);
	}
 	if (!jsonBVisible){
 		fromBusiness.setVisibility(true);
 	}
	if (!jsonCZVisible){
		colzones.setVisibility(true);
	}
	for( var j = 0; j < colzones.features.length; j++ ) {
		feature = colzones.feature;
		var searchlayer=fromProperty.id;
		var searchlayer2=fromBusiness.id;
		console.log('property layer');
//alert('starting now with Property');
		for( var i = 0; i < map.getLayer(searchlayer).features.length; i++ ) 
		{
			if (colzones.features[j].geometry.intersects(map.getLayer(searchlayer).features[i].geometry)) { 
				var checkPoint = new OpenLayers.Geometry.Point(map.getLayer(searchlayer).features[i].geometry.getBounds().getCenterLonLat().lon,map.getLayer(searchlayer).features[i].geometry.getBounds().getCenterLonLat().lat);
				if (colzones.features[j].geometry.containsPoint(checkPoint)){
					JSONObject.push({upn: map.getLayer(searchlayer).features[i].attributes.upn,colzone: colzones.features[j].attributes.zoneid});
	// for debugging
		if (console && console.log) {
				console.log(map.getLayer(searchlayer).features[i].attributes.upn);
			}
				}
			}			
		}
//alert('Property finished - Starting now with Business');
		for( var i = 0; i < map.getLayer(searchlayer2).features.length; i++ ) 
		{
			if (colzones.features[j].geometry.intersects(map.getLayer(searchlayer2).features[i].geometry)) { 
				var checkPoint = new OpenLayers.Geometry.Point(map.getLayer(searchlayer2).features[i].geometry.getBounds().getCenterLonLat().lon,map.getLayer(searchlayer2).features[i].geometry.getBounds().getCenterLonLat().lat);
				if (colzones.features[j].geometry.containsPoint(checkPoint)){
					JSONObjectBus.push({upn: map.getLayer(searchlayer2).features[i].attributes.upn,colzone: colzones.features[j].attributes.zoneid});
	// for debugging
		if (console && console.log) {
				console.log(map.getLayer(searchlayer2).features[i].attributes.upn);
			}
				}
			}			
		}
	}
//  alert(JSON.stringify(JSONObject));

var handlerParameter = {spin: spinner};

	var request = OpenLayers.Request.POST(
	{
		url: "php/dbaction.php", 
		data: OpenLayers.Util.getParameterString(
		{
			dbaction: "updateCZinProp",
			propincz: JSON.stringify(JSONObject),
			busincz: JSON.stringify(JSONObjectBus),
			sub: "false"
		}),
		headers: 
		{
			"Content-Type": "application/x-www-form-urlencoded"
		},
		callback: handlerupdateCZinProp,
		scope: handlerParameter
	});	
	
}

//-----------------------------------------------------------------------------
		//function handlerupdateCZinProp() 
		//is used to show that the records has been updated
		//
//-----------------------------------------------------------------------------
function handlerupdateCZinProp(request) {
feed=JSON.parse(request.responseText);
alert(feed[0]['upn']+' '+feed[0]['message']);
this.spin.stop();
}

//-----------------------------------------------------------------------------
		//function printFunction() 
		//is used to print a map area
		//very experimental!!!
//-----------------------------------------------------------------------------
function printFunction() {
	var title = 'Upload XLS';
	var w = 1024;
	var h = 650;
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
//    var popupWindow = window.open ('', title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+h+', top='+top+', left='+left);

// var context = colzones.renderer.canvas; //layer.renderer.hitContext;
// var size = map.getSize();
// var imageData = context.getImageData(10,10,50,50);
// //alert(imageData);
// var canv  = document.getElementById("myCanvas");
// var ctx=canv.getContext("2d");
// ctx.putImageData(imageData,10,70);
// var dataUrl = canv.toDataURL();
// //alert(dataURL);
// 
// window.open(dataUrl, "toDataURL() image", "width=600, height=200");
// 
//     html2canvas(document.getElementById("myCanvas"), {
//         onrendered: function (canvas) {
//             var img = canvas.toDataURL("image/png").replace("image/png", "image/octet-stream");  // here is the most important part because if you dont replace you will get a DOM 18 exception.
// 			window.open(img, "toDataURL() image", "width=600, height=200");
// 			//window.location.href=image; // it will save locally
//            // window.open(img);
//         }
//     });

   html2canvas(document.getElementById("map"), {
        onrendered: function (canvas) {
            var img = canvas.toDataURL("image/png")
            window.open(img);
        }
    });}