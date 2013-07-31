// 24. Juli 2013 12:16:13 GMT working on getting polygons on a map
// test with github sync
var projWGS84 = new OpenLayers.Projection("EPSG:4326");
var proj900913 = new OpenLayers.Projection("EPSG:900913");

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
	
	
function init(){

//make drawing tools invisible
 document.getElementById("controls").style.visibility="hidden";

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
      var kml =  new OpenLayers.Layer.Vector("Payment Status", {
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
      var kmlsub =  new OpenLayers.Layer.Vector("Local Plan", {
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
	map.addLayer(kmlsub);
	map.addLayer(kml);
	map.addLayer(fromjson);
	map.addLayer(colzones);
				
// polygon drawing for collector zones  	
	if (console && console.log) {
		function report(event) {
			console.log(event.type, event.feature ? event.feature.id : event.components);
		}
		colzones.events.on({
			"beforefeaturemodified": report,
			"featuremodified": report,
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

   select = new OpenLayers.Control.SelectFeature([kml, kmlsub, fromjson, colzones]); 
            kml.events.on({
                "featureselected": onFeatureSelect,
                "featureunselected": onFeatureUnselect
            });
			kmlsub.events.on({
                "featureselected": onFeatureSelectSub,
                "featureunselected": onFeatureUnselect
            });
			fromjson.events.on({
                "featureselected": onFeatureSelectFJ,
                "featureunselected": onFeatureUnselect
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
    
    OpenLayers.Event.observe(map.div, 'mousemove', mouseMoveListener);
    

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


    function mouseMoveListener(event) {
        var lonlat = map.getLonLatFromPixel(event.xy);
                
        OpenLayers.Util.getElement("lon1").innerHTML = lonlat.lon;
        OpenLayers.Util.getElement("lat1").innerHTML = lonlat.lat;
        
        lonlat.transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
        OpenLayers.Util.getElement("lon2").innerHTML = lonlat.lon;
        OpenLayers.Util.getElement("lat2").innerHTML = lonlat.lat;
    }

  

function onFeatureSelect(evt) {
                feature = evt.feature;
                var request = OpenLayers.Request.POST({
                    url: "php/connection.php", 
                    data: OpenLayers.Util.getParameterString({clickfeature: feature.attributes.UPN.value,
															  sub: "false"}),
					headers: {
						"Content-Type": "application/x-www-form-urlencoded"
					},
					callback: handler
                });
            } 
            
function onFeatureSelectSub(evt) {
                feature = evt.feature;
                var request = OpenLayers.Request.POST({
                    url: "php/connection.php", 
                    data: OpenLayers.Util.getParameterString({clickfeature: feature.attributes.description,
															   sub: "true"}),
					headers: {
						"Content-Type": "application/x-www-form-urlencoded"
					},
					callback: handler
                });
            } 
function onFeatureSelectFJ(evt) {
                feature = evt.feature;
//                alert('features: '+feature.attributes.upn);
                var request = OpenLayers.Request.POST({
                    url: "php/connection.php", 
                    data: OpenLayers.Util.getParameterString({clickfeature: feature.attributes.upn,
															  sub: "false"}),
					headers: {
						"Content-Type": "application/x-www-form-urlencoded"
					},
					callback: handler
                });
            } 

 function onFeatureSelectcz(evt) {
                feature = evt.feature;
                content = 'Collector ID: '+feature.id.substring(feature.id.indexOf('_')+1,feature.id.length)+'<br>Area: '+(feature.geometry.getGeodesicArea(proj900913)/1000000)+'sq km';
    var popup = new OpenLayers.Popup.FramedCloud("featurePopup",
                                         feature.geometry.getBounds().getCenterLonLat(),
                                         new OpenLayers.Size(100,100),
                                         content, 
                                         null, true, onPopupClose);
                feature.popup = popup;
                popup.feature = feature;
                map.addPopup(popup, true);
            } 
               
  function handler(request) {
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

	var html = '';

    // build html for each feed item
    for (var i = 0; i < feed.length; i++) {

        html += '<h3>Owner: '+ feed[i]['owner'] +'</h3>';
        html += '<p>Street: '+ feed[i]['streetname'] +'</p>';
        html += '<p>House Nr: '+ feed[i]['housenumber'] +'</p>';
        html += '<p>UPN: '+ feed[i]['upn'] +'</p>';
        html += '<p>SUBUPN: '+ feed[i]['subupn'] +'</p>';
        html += '<div><strong>Revenue Balance: '+ feed[i]['revenue_balance'] +'</strong></div>';
        html += '<p>Payment Status: '+ feed[i]['pay_status'] +'</p>';
        html += '<p>Payment Due: '+ feed[i]['revenue_due'] +'</p>';
        html += '<p>Owner Address: '+ feed[i]['owneraddress'] +'</p>';
        html += '<p>Owner Tel: '+ feed[i]['owner_tel'] +'</p>';
        html += '<p>Owner Email: '+ feed[i]['owner_email'] +'</p>';
        html += '<hr />';
		}
    var popup = new OpenLayers.Popup.FramedCloud("featurePopup",
                                         feature.geometry.getBounds().getCenterLonLat(),
                                         new OpenLayers.Size(100,100),
                                         html, 
                                         null, true, onPopupClose);
                feature.popup = popup;
                popup.feature = feature;
                map.addPopup(popup, true);
	  }
	}

// function to draw polygones from the boundaries stored in the table KML_From_LUPMIS
function getpolygons() {  
//   alert("inside getpolygones");
   var jsonVisible = fromjson.getVisibility();
   if (jsonVisible) {
	  spinner.spin(target);
		var request = OpenLayers.Request.POST({
			url: "php/getlocalplan.php", 
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			callback: polyhandler
		});
     }else{spinner.stop()};           

} //end of function getpolygons

//function to take the feedback from getlocalplan.php within the function getpolygons

  function polyhandler(request) {
//   alert("inside polyhandler: ");

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

//   alert("inside polyhandler: "+feed[1]['upn']);
       
//       OpenLayers.Util.getElement("epsg1").innerHTML = feed[1]['boundary'];

   	var boundary = [];
var i = 0
    // build geometry for each feed item
	for (var i = 0; i < feed.length; i++) {
       	boundary = feed[i]['boundary'];
       	
		var coordinates = boundary.split(" ");
		var polypoints = [];
		for (var j=0;j < coordinates.length; j++) {
			points = coordinates[j].split(",");
//       OpenLayers.Util.getElement("epsg1").innerHTML = coordinates[0]+' '+points[0]+' '+points[1];
			point = new OpenLayers.Geometry.Point(points[0], points[1]).transform(projWGS84,proj900913);
			polypoints.push(point);
		}
		// create some attributes for the feature
	  var attributes = {upn: feed[i]['upn']};
//       OpenLayers.Util.getElement("epsg2").innerHTML = polypoints.length+ ' ' +polypoints;

		var linear_ring = new OpenLayers.Geometry.LinearRing(polypoints);
		switch(parseInt(feed[i]['status'])) {
			case 1:
				var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, styleRed);		
			  break;
			case 9:  
				var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, styleGreen);		
			  break;
			default:  
				var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes, styleNeutral);		
		}
		
		
		fromjson.addFeatures([polygonFeature]);

	  } // end of for 
	  fromjson.redraw();
	}
   		spinner.stop();

 } // end of function polyhandler

function onPopupClose(evt) {
        select.unselectAll();
  }
	
  function onFeatureUnselect(event) {
            var feature = event.feature;
            if(feature.popup) {
                map.removePopup(feature.popup);
                feature.popup.destroy();
                delete feature.popup;
            }
            }   
            
  function onSketchComplete(event) {
        var feature = event.feature;
        if(colzonecolor!=''){
          colzonecolor = colors[icolor];
          ++icolor;
          feature.style = {fill: true, fillColor: colzonecolor, fillOpacity: 0.2};
        }else{
        feature.style = {fill: true, fillColor: startcolor, fillOpacity: 0.2};
        colzonecolor=startcolor;
        }
        colzones.redraw();
}      

   function onVisibiltyChangedcz(){
      var czVisible = colzones.getVisibility();
     if (czVisible) {
     document.getElementById("controls").style.visibility="visible";
     }else{
     document.getElementById("controls").style.visibility="hidden";
     }
     }
 		
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