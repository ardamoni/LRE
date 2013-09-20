// 24. Juli 2013 12:16:13 GMT working on getting polygons on a map
// 01.08.13 11:30 created the LREinit from dbgeojsonpoly.js and placed all the functions in lib/jsfunctions.js

//make drawing tools invisible
 document.getElementById("controls").style.visibility="hidden";
 
//get the user name from $_SESSION
 getSessionUser();
var projWGS84 = new OpenLayers.Projection("EPSG:4326");
var proj900913 = new OpenLayers.Projection("EPSG:900913");

//			  var session_name = "<?php echo json_encode($_SESSION['user']['name']); ?>";
//			  var session_name = "<?php=$_SESSION['user'];?>";

//     		   document.getElementById("wcUser").innerHTML=session_name;

// global variables, CLEAN before populating them
var global_upn = '';
var global_subupn = [];

var options = {   
			  scales: [500, 1000, 2500, 5000, 10000],
			  numZoomLevels: 26,
			  allOverlays: true,
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
  top: 'auto', // Top position relative to parent in px
  left: 'auto' // Left position relative to parent in px
};
var target = document.getElementById('map');
var spinner = new Spinner(spinopts); //.spin(target);
var globalfeatureid;
var fromjson = new OpenLayers.Layer.Vector("Payment Status (Real Time)", {		 
	    visibility: false,
	    eventListeners: {"visibilitychanged": getpolygons,
 						 //"featureadded": function(){alert("Feature added")}
 						 }
     });
     
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

var styleRed = { 
		// style_definition
		 strokeColor: "#FFAC62",
            strokeOpacity: 0.8,
            strokewidth: 1,
            fillColor: "#FF0033",
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
	
	
//function init(){


   var sm = new OpenLayers.StyleMap({
    			fillColor: "#666666",
    			lineColor: "#0033FF"});

//    			'default': mystyle,
//Mapnik
      var mapnik =  new OpenLayers.Layer.OSM("OpenStreetMap");

//Google Streets      
  	  var gmap = new OpenLayers.Layer.Google(
					"Google Streets", // the default
					{numZoomLevels: 20,
					visibility: false
					});
//Markers
//      var markers = new OpenLayers.Layer.Markers( "Markers" );
//KML      
      var kmlRedGreen =  new OpenLayers.Layer.Vector("Payment Status", {
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
//KMLsub      
    var kmlLocalPlan =  new OpenLayers.Layer.Vector("Local Plan", {
            strategies: [new OpenLayers.Strategy.Fixed()],
            styleMap: sm,
            visibility: false,
            projection: map.displayProjection,
            protocol: new OpenLayers.Protocol.HTTP({
                url: "kml/bogoso.kml",
                format: new OpenLayers.Format.KML({
                    extractStyles: true, 
                    extractAttributes: true,
                    kvpAttributes: false,
                    maxDepth: 2
                })
            })
        });
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
 //  "default": OpenLayers.Feature.Vector.style['default'],
   				   'default': zoneStyle,
                         'select': zoneselectStyle,
                         'temporary': temporaryStyle});    			

 //modifies the default "default" style settings of OpenLayers
    			
	colzones = new OpenLayers.Layer.Vector("Collector Zones", {
		renderers: renderer,
		 visibility: false,
		styleMap: zoneStyleMap });

    map.addControl(new OpenLayers.Control.ModifyFeature(colzones, {vertexRenderIntent: "vertex"}));    

// Add Layers
	map.addLayer(mapnik);
	map.addLayer(gmap);
	map.addLayer(kmlLocalPlan);
	map.addLayer(kmlRedGreen);
	map.addLayer(fromjson);
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
// kml.feature

   select = new OpenLayers.Control.SelectFeature([kmlRedGreen, kmlLocalPlan, fromjson, colzones]); 
            kmlRedGreen.events.on({
                "featureselected": onFeatureSelect,
                "featureunselected": onFeatureUnselect,
            });
			kmlLocalPlan.events.on({
                "featureselected": onFeatureSelectSub,
                "featureunselected": onFeatureUnselect
            });
			fromjson.events.on({
                "featureselected": onFeatureSelectFJ,
                "featureunselected": onFeatureUnselect,
//       			"loadend": onloadendRedGreen,
//				"visibilitychanged": onVisibiltyChangedRedGreen

            });
			colzones.events.on({
                "featureselected": onFeatureSelectcz,
                "featureunselected": onFeatureUnselect
            });
            map.addControl(select);
            select.activate();   
//kml.feature end
            
    OpenLayers.Util.getElement("epsg1").innerHTML = map.getProjection();
    OpenLayers.Util.getElement("epsg2").innerHTML = "EPSG:4326";
    
//    OpenLayers.Event.observe(map.div, 'mousemove', mouseMoveListenerA);
            map.events.register("mousemove", map, mouseMoveListener);
/*            function(e) {
                var position = this.events.getMousePosition(e);
                OpenLayers.Util.getElement("lon1").innerHTML = position;
            });
*/
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

    var bogoso = new OpenLayers.LonLat(-2.012644, 5.567).transform(new OpenLayers.Projection("EPSG:4326"),map.getProjectionObject());

    map.setCenter(bogoso, 15);
    document.getElementById('noneToggle').checked = true;
//} //end init()

function mouseMoveListener(event) {
//    var position = this.events.getMousePosition(e);
	var lonlat = map.getLonLatFromPixel(event.xy);
			
	OpenLayers.Util.getElement("lon1").innerHTML = lonlat.lon;
	OpenLayers.Util.getElement("lat1").innerHTML = lonlat.lat;
	
	lonlat.transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
	OpenLayers.Util.getElement("lon2").innerHTML = lonlat.lon;
	OpenLayers.Util.getElement("lat2").innerHTML = lonlat.lat;
}