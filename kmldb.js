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
			//	new OpenLayers.Control.LayerSwitcher({'ascending':false}), 
				new OpenLayers.Control.ScaleLine(),
				new OpenLayers.Control.MousePosition(),
			//	new OpenLayers.Control.OverviewMap(),
				new OpenLayers.Control.Attribution(),
				new OpenLayers.Control.KeyboardDefaults()],};




var map = new OpenLayers.Map('map', options);


   var sm = new OpenLayers.StyleMap({
    			fillColor: "#666666",
    			lineColor: "#0033FF"});

//    			'default': mystyle,
//Mapnik
      var mapnik =  new OpenLayers.Layer.OSM("OpenStreetMap");

//Google Streets      
  	  var gmap = new OpenLayers.Layer.Google(
					"Google Streets", // the default
					{numZoomLevels: 20}
				);
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

// Add Layers
			map.addLayer(mapnik);
            map.addLayer(gmap);
            map.addLayer(kmlsub);
            map.addLayer(kml);
//            map.addLayer(markers);
				
  	
// kml.feature

var select = new OpenLayers.Control.SelectFeature([kml, kmlsub]); 
            kml.events.on({
                "featureselected": onFeatureSelect,
                "featureunselected": onFeatureUnselect
            });
			kmlsub.events.on({
                "featureselected": onFeatureSelectSub,
                "featureunselected": onFeatureUnselect
            });
            map.addControl(select);
            select.activate();   
//kml.feature end
            
    OpenLayers.Util.getElement("epsg1").innerHTML = map.getProjection();
    OpenLayers.Util.getElement("epsg2").innerHTML = "EPSG:4326";
    
    OpenLayers.Event.observe(map.div, 'mousemove', mouseMoveListener);
    

    map.addControl(new OpenLayers.Control.LayerSwitcher());

    var bogoso = new OpenLayers.LonLat(-2.012644, 5.573958).transform(new OpenLayers.Projection("EPSG:4326"),map.getProjectionObject());

    map.setCenter(bogoso, 15);

    function mouseMoveListener(event) {
        var lonlat = map.getLonLatFromPixel(event.xy);
                
        OpenLayers.Util.getElement("lon1").innerHTML = lonlat.lon;
        OpenLayers.Util.getElement("lat1").innerHTML = lonlat.lat;
        
        lonlat.transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
        OpenLayers.Util.getElement("lon2").innerHTML = lonlat.lon;
        OpenLayers.Util.getElement("lat2").innerHTML = lonlat.lat;
    }

function onPopupClose(evt) {
        select.unselectAll();
  }
  

function onFeatureSelect(evt) {
                feature = evt.feature;
                var request = OpenLayers.Request.POST({
                    url: "http://localgis.local/examples/php/connection.php", 
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
                    url: "http://localgis.local/examples/php/connection.php", 
                    data: OpenLayers.Util.getParameterString({clickfeature: feature.attributes.description,
															   sub: "true"}),
					headers: {
						"Content-Type": "application/x-www-form-urlencoded"
					},
					callback: handler
                });
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
        // get ready for parsing by hand
       feed=JSON.parse(request.responseText);

var html = '';

    // build html for each feed item
    for (var i = 0; i < feed.length; i++) {

        html += '<h3>Owner: '+ feed[i]['owner'] +'</h3>';
        html += '<p>Street: '+ feed[i]['streetname'] +'</p>';
        html += '<p>Number: '+ feed[i]['housenumber'] +'</p>';
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
     popup = new OpenLayers.Popup.FramedCloud("featurePopup",
                                         feature.geometry.getBounds().getCenterLonLat(),
                                         new OpenLayers.Size(100,100),
                                         html, //JSON.parse(request.responseText),
                                         null, true, onPopupClose);
                feature.popup = popup;
                popup.feature = feature;
                map.addPopup(popup, true);
                      }
		}
  function onFeatureUnselect(event) {
            var feature = event.feature;
            if(feature.popup) {
                map.removePopup(feature.popup);
                feature.popup.destroy();
                delete feature.popup;
            }
            }            