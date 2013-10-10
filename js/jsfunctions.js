// 24. Juli 2013 12:16:13 GMT working on getting polygons on a map
// 01.08.13 11:30 created the LREinit from dbgeojsonpoly.js and placed all the functions in lib/jsfunctions.js
// 27. September 2013 10:10:43 GMT moved previous code from LREinit and created init() in order to keep file count low

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
var global_out_property;
var global_out_business;

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
	
	
function init(){


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
				 'default': zoneStyle,
				 'select': zoneselectStyle,
				 'temporary': temporaryStyle});    			

 //modifies the default "default" style settings of OpenLayers
    			
	colzones = new OpenLayers.Layer.Vector("Collector Zones", {
		renderers: renderer,
		 visibility: false,
         hover: true,
         styleMap: zoneStyleMap });

    map.addControl(new OpenLayers.Control.ModifyFeature(colzones, {vertexRenderIntent: "vertex"}));    

// Add Layers
	map.addLayer(mapnik);
	map.addLayer(gmap);
	map.addLayer(kmlLocalPlan);
//	map.addLayer(kmlRedGreen);
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
// kml.feature

//   select = new OpenLayers.Control.SelectFeature([kmlRedGreen, kmlLocalPlan, fromProperty, colzones]); 
   select = new OpenLayers.Control.SelectFeature([fromBusiness, kmlLocalPlan, fromProperty, colzones]); 
//            kmlRedGreen.events.on({
//                "featureselected": onFeatureSelect,
//                "featureunselected": onFeatureUnselect,
//            });
			kmlLocalPlan.events.on({
                "featureselected": onFeatureSelectSub,
                "featureunselected": onFeatureUnselect
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
            
    OpenLayers.Util.getElement("epsg1").innerHTML = map.getProjection();
    OpenLayers.Util.getElement("epsg2").innerHTML = "EPSG:4326";
    
            map.events.register("mousemove", map, mouseMoveListener);

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
} //end init()

//created by Ekke on 1. August 2013 10:44:55 GMT

//-----------------------------------------------------------------------------
		//function mouseMoveListener() 
		//displays lon lat information in two boxes above the map
		//
//-----------------------------------------------------------------------------

function mouseMoveListener(event) {
	var lonlat = map.getLonLatFromPixel(event.xy);
			
	OpenLayers.Util.getElement("lon1").innerHTML = lonlat.lon;
	OpenLayers.Util.getElement("lat1").innerHTML = lonlat.lat;
	
	lonlat.transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
	OpenLayers.Util.getElement("lon2").innerHTML = lonlat.lon;
	OpenLayers.Util.getElement("lat2").innerHTML = lonlat.lat;
}

  
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
	//                alert('features: '+feature.attributes.upn);
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
	feature = evt.feature;
	var intersectedUPNs=0;
	var revbalance=0;
	var jsonPVisible = fromProperty.getVisibility();
  	var jsonBVisible = fromBusiness.getVisibility();
   	if (jsonPVisible){
   		var searchlayer=fromProperty.id;
   	}
   	else if (jsonBVisible)
   	{	var searchlayer=fromBusiness.id; }
   	else
   	{ 	html = 'Please open either the Properties or the Business Map!';
   		alert(html);
   	}
	// check each feature from layer below if intersecting with the collector zone polygon
		for( var i = 0; i < map.getLayer(searchlayer).features.length; i++ ) 
		{
			if (feature.geometry.intersects(map.getLayer(searchlayer).features[i].geometry)) { 
                var checkPoint = new OpenLayers.Geometry.Point(map.getLayer(searchlayer).features[i].geometry.getBounds().getCenterLonLat().lon,map.getLayer(searchlayer).features[i].geometry.getBounds().getCenterLonLat().lat);
				if (feature.geometry.containsPoint(checkPoint)){
// colouring for testing 					fromProperty.drawFeature(fromProperty.features[i], {fillColor: "#99FF33", strokeColor: "#00ffff"});			
					intersectedUPNs++;
					revbalance=revbalance+Number(map.getLayer(searchlayer).features[i].attributes.revbalance);
				}
			}			
		}
	content = 'Collector ID: '+feature.attributes.collectorid+
				'<br>District ID: '+feature.attributes.districtid+
				'<br>Area: '+(feature.geometry.getGeodesicArea(proj900913)/1000000)+'sq km'+
				'<br>Properties: '+intersectedUPNs.toString()+
				'<br>Outstanding: '+number_format(revbalance, 2, '.', ',')+' GHC'+
				'<br>Zoneid: '+feature.attributes.zoneid;
	var popup = new OpenLayers.Popup.FramedCloud("featurePopup",
					feature.geometry.getBounds().getCenterLonLat(),
					new OpenLayers.Size(100,100),
					content, 
					null, true, onPopupClose);
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

		// cleaning the global valiables before use
		global_upn = null;
		global_subupn = [];

		for( var i = 0; i < feed.length; i++ ) 
		{			
			global_upn = feed[i]['upn'];
			global_subupn[i] = feed[i]['subupn'];
		}

		// build html for each feed item
		for( var i = 0; i < feed.length; i++ ) 
		{		
			html += '<h3>Owner: '+ feed[i]['owner'] +'</h3>';
			html += '<p>Street: '+ feed[i]['streetname'] +'</p>';
			html += '<p>House Nr: '+ feed[i]['housenumber'] +'</p>';
			html += '<p>UPN: '+ feed[i]['upn'] +'</p>';
			html += '<p>SUBUPN: '+ feed[i]['subupn'] +'</p>';
			html += '<div><strong>Revenue Balance: '+ feed[i]['revenue_balance'] +'</strong></div>';
			html += '<p>Payment Due: '+ feed[i]['revenue_due'] +'</p>';
			html += '<p>Revenue Collected: '+ feed[i]['revenue_collected'] +'</p>';
			html += '<p>Date payed: '+ feed[i]['date_payment'] +'</p>';
			html += '<p>Payment Status: '+ feed[i]['pay_status'] +'</p>';			
		//check whether called from property or business
		    if (feed[i]['business_name']!='property'){
			html += '<p>Business Name: '+ feed[i]['business_name'] +'</p>';
		    }
			html += '<p>Owner Address: '+ feed[i]['owneraddress'] +'</p>';
			html += '<p>Owner Tel: '+ feed[i]['owner_tel'] +'</p>';
			html += '<p>Owner Email: '+ feed[i]['owner_email'] +'</p>';									
			html += '<hr />';
		}

		html += "<input type='button' value='Revenue Collection' onclick='collectRevenueOnClick()' >";	
		//html += "<button onclick='collectRevenueOnClick(\''+upn+'\', \''+subupn+'\')'>Revenue Collection</button>";	
		//html += ("<input type='button' value='Revenue Collection' />").find('input[type=button]').click( function(){ collectRevenueOnClick(upn, subupn); } );


		var popup = new OpenLayers.Popup.FramedCloud(
										"featurePopup",
                                        feature.geometry.getBounds().getCenterLonLat(),
                                        new OpenLayers.Size(100,100),
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
function collectRevenueOnClick( ) 
{	
	var upn = this.global_upn;
	var subupn = this.global_subupn;

	var popupWindow = null;
	popupWindow = window.open('php/revenueCollectionForm.php?upn='+upn+'&subupn='+subupn, 'Revenue Collection', 'height=500, width=500, left=500, top=200, resizable=yes');	

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
		//function getPropertyPolygons() 
		//is the onvisibilitychanged event for the Collector Zone Layer
		//it calls dbaction.php - getlocalplan() to retrieve geometry information to draws polygones 
		//from the boundaries stored in the table KML_From_LUPMIS
		//it calls a polyhandler() to actually create the polygones based on the returned data
//-----------------------------------------------------------------------------
function getPropertyPolygons() {  
//   alert("inside getpolygones");
   var jsonVisible = fromProperty.getVisibility();
   if (fromProperty.features.length<1) {
	  spinner.spin(target);
	  w.start();
		var request = OpenLayers.Request.POST({
			url: "php/dbaction.php", 
			data: OpenLayers.Util.getParameterString(
			{dbaction: "getlocalplan"}),
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			callback: polyhandler
		});
     }else{
     if (jsonVisible) {
     		document.getElementById("debug2").innerHTML='Outstanding revenue: <br>'+number_format(global_out_property, 2, '.', ',')+' GHC';					
		}else{
     		document.getElementById("debug2").innerHTML=' - ';					
     		}
			spinner.stop()
	};           

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
			boundary = feed[i]['boundary'];       	
			var coordinates = boundary.split(" ");
			var polypoints = [];
			for (var j=0;j < coordinates.length; j++) {
				points = coordinates[j].split(",");
				point = new OpenLayers.Geometry.Point(points[0], points[1]).transform(projWGS84,proj900913);
				polypoints.push(point);
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
					var num = Number(feed[i]['revenue_balance']);
					var n = num.valueOf(); 
					revbalance = revbalance+num;
					global_out_property = revbalance;
					document.getElementById("debug2").innerHTML='Outstanding revenue: <br>'+number_format(global_out_property, 2, '.', ',')+' GHC';
				  break;
				case 9:  
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, styleGreen);		
				  break;
				default:  
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, styleNeutral);		
				}
			  fromProperty.addFeatures([polygonFeature]);

		  } // end of for 
		  fromProperty.redraw();
	}
		spinner.stop();
		w.stop();  
		document.getElementById("debug1").innerHTML=w.toString();

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
   if (fromBusiness.features.length<1) {
	  spinner.spin(target);
	  w.start();
		var request = OpenLayers.Request.POST({
			url: "php/dbaction.php", 
			data: OpenLayers.Util.getParameterString(
			{dbaction: "getbusiness"}),
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			callback: Businesshandler
		});
     }else{
     if (jsonBVisible) {
     		document.getElementById("debug2").innerHTML='Outstanding revenue: <br>'+number_format(global_out_business, 2, '.', ',')+' GHC';	
     		}else{
     		document.getElementById("debug2").innerHTML=' - ';					
     		}
			spinner.stop()};           

} //end of function getBusinessPolygons

//-----------------------------------------------------------------------------
		//function polyhandler() 
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
			boundary = feed[i]['boundary'];       	
			var coordinates = boundary.split(" ");
			var polypoints = [];
			for (var j=0;j < coordinates.length; j++) {
				points = coordinates[j].split(",");
				point = new OpenLayers.Geometry.Point(points[0], points[1]).transform(projWGS84,proj900913);
				polypoints.push(point);
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
					var num = Number(feed[i]['revenue_balance']);
					var n = num.valueOf(); 
					revbalance = revbalance+num;
					global_out_business = revbalance;
					document.getElementById("debug2").innerHTML='Outstanding revenue: <br>'+number_format(global_out_business, 2, '.', ',')+' GHC';					
//					document.getElementById("debug2").innerHTML=typeof(num);
				  break;
				case 9:  
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, styleGreen);		
				  break;
				default:  
					var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, styleNeutral);		
				}
			  fromBusiness.addFeatures([polygonFeature]);

		  } // end of for 
		  fromBusiness.redraw();
	}
		spinner.stop();
		w.stop();  
		document.getElementById("debug1").innerHTML=w.toString();

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
			 districtid: "234",
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
	var districtid ="234";
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
		 districtid: "234"}), //HARDCODED !!!
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
		//
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
			var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.fromWKT(polygon), attributesCZ, {fill: true, fillColor: zonecolour, fillOpacity: 0.2});		
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
	if(element.id == "noneToggle"){
			select.activate();				
		}
}    

function updateClock(watch) {
    	OpenLayers.Util.getElement("tools").innerHTML = watch.toString();
}

function getSessionUser(){
var request = OpenLayers.Request.GET({
    url: "php/getsession.php", 
    callback: sessionuserhandler
});
}
//-----------------------------------------------------------------------------
		//function sessionuserhandler() 
		//is the handler for getSessionUser 
		//
//-----------------------------------------------------------------------------
function sessionuserhandler(request) {
	// the browser's parser may have failed
	if(!request.responseXML) {
	var html ='';
		// get the response from php and read the json encoded data
		feed=JSON.parse(request.responseText);
		for (var i = 0; i < feed.length; i++) {
			html += feed[i]}
   document.getElementById("wcUser").innerHTML='Welcome: '+html//request.responseText;
// alert(request.responseText);
	} // else{  alert('inside inserthandler');}
} 
// end of function sessionuserhandler


//-----------------------------------------------------------------------------
		//function searchupn() 
		//is called by the SEARCH button in the main windows 
		//
//-----------------------------------------------------------------------------
function searchupn() {
  var s = document.getElementById('searchBox').value;
   s = s.trim();
  var jsonPVisible = fromProperty.getVisibility();
  var jsonBVisible = fromBusiness.getVisibility();
   if (jsonPVisible){
   		var searchlayer=fromProperty.id;
   }
   else if (jsonBVisible)
   {	var searchlayer=fromBusiness.id; }
   else
   { 	html = 'Please open either the Properties or the Business Map! \n Search for '+s+' is only possible in these two Maps';
   		alert(html);
   	}
   
   var checkentry =(s.match(/-/g)||[]).length; //this checks if two - signs are in the entry
   var checkentry2 =s.length; //this checks if 13 characters are in the entry
   if ((checkentry != 2) || (checkentry2 < 13)){
    html = 'Please check your entry! \n'+s+'\nappears to be an incorrect UPN';
	alert(html);}
	
	foundUPN=map.getLayer(searchlayer).getFeaturesByAttribute('upn', s);
	if (foundUPN.length > 0){
	for (var i = 0; i < foundUPN.length; i++) {
			map.getLayer(searchlayer).drawFeature(map.getLayer(searchlayer).getFeatureById(foundUPN[i].id), {fillColor: "#99FF33", fillOpacity: 0.8, strokeColor: "#00ffff"});			
			}
	}else{
	html = 'UPN: '+s+' could not be found!\nPlease check your entry';
	alert(html);
	}		
} 
// end of function searchupn

//-----------------------------------------------------------------------------
		//function tableshow() 
		//opens a window to show tabular data about the corresponding map
		//
//-----------------------------------------------------------------------------
function tableshow() {
  var jsonPVisible = fromProperty.getVisibility();
  var jsonBVisible = fromBusiness.getVisibility();
  var popupWindow = null;
  
   if (jsonPVisible && jsonBVisible){
   		html = 'Table view is only available for data from one map.  \nPlease close either the Properties or the Business Map! ';
   		alert(html);
   }
   else if (jsonBVisible || jsonPVisible)  {
		popupWindow = window.open("php/showtable.php","Table View", 'border=0, status=0, height=500, width=1000, left=500, top=200, resizable=no,location=no,menubar=no,status=no,toolbar=no');	
	}
   else
   { 	html = 'Please open either the Properties or the Business Map! \nTable view is only available for data from one of these two Maps';
   		alert(html);
   	}
   

	
//	popupWindow = window.open("","_blank","resizable=no,scrollbars=no,location=no,menubar=no,status=no,toolbar=no");
//	popupWindow.document.open();
//	popupWindow.document.writeln("<html><head><title>Table View</title></head>");
//	popupWindow.document.writeln("this is ekke</html>");
	

	if(popupWindow && !popupWindow.closed)
	{
		popupWindow.focus();
	}

	return false;

} 
// end of function tableshow