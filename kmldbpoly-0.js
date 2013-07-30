var projWGS84 = new OpenLayers.Projection("EPSG:4326");
var proj900913 = new OpenLayers.Projection("EPSG:900913");

var options = {   
			  scales: [500, 1000, 2500, 5000, 10000],
			  numZoomLevels: 26,
			  allOverlays: true,
			  projection: new OpenLayers.Projection("EPSG:900913"),
			  displayProjection: new OpenLayers.Projection("EPSG:4326"),
			  controls:[
				new OpenLayers.Control.Navigation(),
				new OpenLayers.Control.PanZoomBar(),
				new OpenLayers.Control.LayerSwitcher({'ascending':false}), 
				new OpenLayers.Control.ScaleLine(),
				new OpenLayers.Control.MousePosition(),
			//	new OpenLayers.Control.OverviewMap(),
				new OpenLayers.Control.Attribution(),
				new OpenLayers.Control.KeyboardDefaults()],};

var map = new OpenLayers.Map('map', options);
var colzones, controls, colzonecolor='';
var startcolor = '#FF0000';

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
            visibility: true,
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
    			fillColor: "#0099FF",
    			lineColor: "#0033FF",
    			fillOpacity: 0.2});    

   var zoneselectStyle = new OpenLayers.Style({
    			fillColor: "#33ff33",
    			lineColor: "#FFFF33",
    			fillOpacity: 0.2});    
    			
	var vertexStyle = new OpenLayers.Style({
		strokeColor: "#FFFF00",
		fillColor: "#000000",
		strokeOpacity: 1,
		strokeWidth: 2,
		pointRadius: 5,
		graphicName: "cross"
	});    			

    zoneStyleMap = new OpenLayers.StyleMap({
   "default": OpenLayers.Feature.Vector.style['default'],
 //  				   'default': zoneStyle,
                         'select': zoneselectStyle,
                         'vertex': vertexStyle},{extendDefault: true});    			

 //modifies the default "default" style settings of OpenLayers
    			
	colzones = new OpenLayers.Layer.Vector("Collector Zones", {
		renderers: renderer,
		styleMap: zoneStyleMap });
		
    map.addControl(new OpenLayers.Control.ModifyFeature(colzones, {vertexRenderIntent: "vertex"}));    

// Add Layers
	map.addLayer(mapnik);
	map.addLayer(gmap);
	map.addLayer(kmlsub);
	map.addLayer(kml);
	map.addLayer(colzones);
//            map.addLayer(markers);
				
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
			"sketchcomplete": onSketchComplete
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

   select = new OpenLayers.Control.SelectFeature([kml, kmlsub, colzones]); 
            kml.events.on({
                "featureselected": onFeatureSelect,
                "featureunselected": onFeatureUnselect
            });
			kmlsub.events.on({
                "featureselected": onFeatureSelectSub,
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
    

    map.addControl(new OpenLayers.Control.LayerSwitcher());

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

 function onFeatureSelectcz(evt) {
                feature = evt.feature;
                content = feature.id;
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
          colzonecolor = doMath(colzonecolor);
          feature.style = {fill: true, fillColor: colzonecolor, fillOpacity: 0.2};
        }else{
        feature.style = {fill: true, fillColor: startcolor, fillOpacity: 0.2};
        colzonecolor=startcolor;
        }
        colzones.redraw();
}          
  function doMath(hexcol) {
  		red=parseInt(hexcol.substr(1,2),16);
  		green=parseInt(hexcol.substr(3,2),16);
  		blue=parseInt(hexcol.substr(5,2),16);
  		blue=Number(blue)+32;
		hexcol = (red+green+blue).toString(16); //hex   
		alert(red+' '+green+' '+blue+' '+hexcol);
		return hexcol; //'#0078FF'; //hexcol.hexaddans.value;
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
            