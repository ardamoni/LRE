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


   var sm = new OpenLayers.StyleMap({
    			fillColor: "#666666",
    			lineColor: "#0033FF"});

//    			'default': mystyle,
//Mapnik
      var mapnik =  new OpenLayers.Layer.OSM("OpenStreetMap");

//Google Streets      
  	  var gmap = new OpenLayers.Layer.Google(
					"Google Streets", // the default
					{numZoomLevels: 20, visibility: false}
				);
//KML      
      var kml =  new OpenLayers.Layer.Vector("LRE-pilots", {
            strategies: [new OpenLayers.Strategy.Fixed()],
            visibility: true,
            styleMap: sm,
            projection: map.displayProjection,
            protocol: new OpenLayers.Protocol.HTTP({
                url: "kml/LRE-pilots.kml",
                format: new OpenLayers.Format.KML({
                    extractStyles: true, 
                    extractAttributes: true,
                    maxDepth: 2
                })
            })
        });
// Add Layers
			map.addLayer(mapnik);
            map.addLayer(gmap);
            map.addLayer(kml);
				
 //Add Markers as Vectors

            var size = new OpenLayers.Size(60,60);
            var offset = new OpenLayers.Pixel(-(size.w/3), -(size.h-5));
			var iconG = new OpenLayers.Icon('../art/FlagGreenGIZ.png',size,offset);
			var iconR = new OpenLayers.Icon('../art/FlagRedGIZ.png',size,offset);
			var iconGoo = new OpenLayers.Icon('../art/GoogleMapsMarker.png',size,offset);

			var vectors = new OpenLayers.Layer.Vector("Locations", {visibility: true});
//set the points			
            var Tamale = new OpenLayers.Geometry.Point(-0.8422669823759924, 9.434411835330053).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
            var Bolga = new OpenLayers.Geometry.Point(-0.8538433964843765, 10.7989093967214).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
            var Wa = new OpenLayers.Geometry.Point(-2.4969968255157418, 10.06233287530349).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
            var Techiman = new OpenLayers.Geometry.Point(-1.937370007812528, 7.598024400966826).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
            var Kumasi = new OpenLayers.Geometry.Point(-1.622843449889848, 6.685917315314827).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
            var Suhum = new OpenLayers.Geometry.Point(-0.45970887500003504, 6.046333290050134).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
            var Ho = new OpenLayers.Geometry.Point(0.4726055209045417, 6.608520274168122).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
            var KetuSouth = new OpenLayers.Geometry.Point(1.1475565974120614, 6.100956581800806).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
            var AgonaWest = new OpenLayers.Geometry.Point(-0.6998845990295168, 5.520394692267452).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
            var CCMA = new OpenLayers.Geometry.Point(-1.2397381718750167, 5.115907590402356).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
            var KEEA = new OpenLayers.Geometry.Point(-1.341962521850319, 5.100158762709024).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());
            var STMA = new OpenLayers.Geometry.Point(-1.7160019333950578, 4.939656520671736).transform(new OpenLayers.Projection("EPSG:4326"), map.getProjectionObject());

//set the styles for the points
			var markerStyleRed = {externalGraphic: "../art/FlagRedGIZ.png", graphicWidth: 60, graphicHeight: 60, graphicYOffset: -50, graphicXOffset: -20, graphicOpacity: 0.9};
			var markerStyleGreen = {externalGraphic: "../art/FlagGreenGIZ.png", graphicWidth: 60, graphicHeight: 60, graphicYOffset: -50, graphicXOffset: -20, graphicOpacity: 0.9};

//add features to the marker layer
			vectors.addFeatures([new OpenLayers.Feature.Vector(Tamale, {title: 'Tamale'}, markerStyleRed)]);
			vectors.addFeatures([new OpenLayers.Feature.Vector(Bolga, {title: 'Bolgatanga'}, markerStyleRed)]);
			vectors.addFeatures([new OpenLayers.Feature.Vector(Wa, {title: 'Wa'}, markerStyleRed)]);
			vectors.addFeatures([new OpenLayers.Feature.Vector(Techiman, {title: 'Techiman'}, markerStyleRed)]);
			vectors.addFeatures([new OpenLayers.Feature.Vector(Kumasi, {title: 'Kumasi'}, markerStyleRed)]);
			vectors.addFeatures([new OpenLayers.Feature.Vector(Suhum, {title: 'Suhum'}, markerStyleRed)]);
			vectors.addFeatures([new OpenLayers.Feature.Vector(Ho, {title: 'Ho'}, markerStyleGreen)]);
			vectors.addFeatures([new OpenLayers.Feature.Vector(KetuSouth, {title: 'Ketu South'}, markerStyleRed)]);
			vectors.addFeatures([new OpenLayers.Feature.Vector(AgonaWest, {title: 'Agona West'}, markerStyleGreen)]);
			vectors.addFeatures([new OpenLayers.Feature.Vector(CCMA, {title: 'Cape Coast'}, markerStyleGreen)]);
			vectors.addFeatures([new OpenLayers.Feature.Vector(KEEA, {title: 'KEEA'}, markerStyleRed)]);
			vectors.addFeatures([new OpenLayers.Feature.Vector(STMA, {title: 'Sekondi-Takoradi'}, markerStyleRed)]);

//now add the layer to the map
			map.addLayer(vectors);
 
// kml.feature

var select = new OpenLayers.Control.SelectFeature([kml, vectors]); 
			kml.events.on({
                "featureselected": onFeatureSelect,
                "featureunselected": onFeatureUnselect
            });
				vectors.events.on({
                "featureselected": onFeatureSelectMark,
                "featureunselected": onFeatureUnselect
            });
            map.addControl(select);
            select.activate();   
//kml.feature end
            
    OpenLayers.Util.getElement("epsg1").innerHTML = map.getProjection();
    OpenLayers.Util.getElement("epsg2").innerHTML = "EPSG:4326";
    
    OpenLayers.Event.observe(map.div, 'mousemove', mouseMoveListener);
    

    map.addControl(new OpenLayers.Control.LayerSwitcher());

    var ghana = new OpenLayers.LonLat(-1.1759874280090854,8.173345828918867).transform(new OpenLayers.Projection("EPSG:4326"),map.getProjectionObject());

    map.setCenter(ghana, 7);

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
  

  function onFeatureSelect(event) {
            var feature = event.feature;
					var content = "<h2>"+feature.attributes.CAPTION.value + "</h2>" 
										+ "Assembly: " + feature.attributes.Name.value + "<br>" 
										+ "Status of SNPN: " + feature.attributes.Description.value + "<br>" 
										+ "Capital: " + feature.attributes.CAPTION.value + "<br>" 
            if (content.search("<script") != -1) {
                content = "Content contained Javascript! Escaped content below.<br>" + content.replace(/</g, "&lt;");
            }
            popup = new OpenLayers.Popup.FramedCloud("chicken", 
                                     feature.geometry.getBounds().getCenterLonLat(),
                                     new OpenLayers.Size(100,100),
                                     content,
                                     null, true, onPopupClose);
            feature.popup = popup;
            map.addPopup(popup);
  }  
  
  function onFeatureSelectMark(event) {
            var feature = event.feature;
			var content = "Local Revenue Enhancement Project is in <b>"+feature.attributes.title;
            if (content.search("<script") != -1) {
                content = "Content contained Javascript! Escaped content below.<br>" + content.replace(/</g, "&lt;");
            }
            popup = new OpenLayers.Popup.FramedCloud("chicken", 
                                     feature.geometry.getBounds().getCenterLonLat(),
                                     new OpenLayers.Size(100,100),
                                     content,
                                     null, true, onPopupClose);
            feature.popup = popup;
            map.addPopup(popup);
           }
 
 function onFeatureUnselect(event) {
            var feature = event.feature;
            if(feature.popup) {
                map.removePopup(feature.popup);
                feature.popup.destroy();
                delete feature.popup;
            }
            }            