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
//  	  var gmap = new OpenLayers.Layer.Google(
//					"Google Streets", // the default
//					{numZoomLevels: 20}
//				);
//Markers
//      var markers = new OpenLayers.Layer.Markers( "Markers" );
//KML      
      var kml =  new OpenLayers.Layer.Vector("Payment Status", {
            strategies: [new OpenLayers.Strategy.Fixed()],
            styleMap: sm,
            projection: map.displayProjection,
            protocol: new OpenLayers.Protocol.HTTP({
                url: "../kml/RedGreen.kml",
                format: new OpenLayers.Format.KML({
                    extractStyles: true, 
                    extractAttributes: true,
                    maxDepth: 2
                })
            })
        });
//KMLsub      
      var kmlsub =  new OpenLayers.Layer.Vector("Local Plan", {
            strategies: [new OpenLayers.Strategy.Fixed()],
            styleMap: sm,
            projection: map.displayProjection,
            protocol: new OpenLayers.Protocol.HTTP({
                url: "../kml/bogoso.kml",
                format: new OpenLayers.Format.KML({
                    extractStyles: true, 
                    extractAttributes: true,
                    kvpAttributes: false,
                    maxDepth: 2
                })
            })
        });

//OSM Layer
//      var osm =  new OpenLayers.Layer.Vector("OSM", {
//            strategies: [new OpenLayers.Strategy.Fixed()],
//            protocol: new OpenLayers.Protocol.HTTP({
//                url: "kml/Prestea.osm",
//                format: new OpenLayers.Format.OSM({
//                    checkTags: true
//                })
//            })
//        });

// Add Layers
			map.addLayer(mapnik);
//            map.addLayer(gmap);
            map.addLayer(kmlsub);
            map.addLayer(kml);
//            map.addLayer(osm);
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

    var bogoso = new OpenLayers.LonLat(-2.0110127385253826, 5.5680641649993285).transform(new OpenLayers.Projection("EPSG:4326"),map.getProjectionObject());

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
  

  function onFeatureSelect(event) {
            var feature = event.feature;
 //             if (feature.attributes.SubUPN.value != '') {
					var content = "<h2>"+feature.attributes.ParcelOf.value + "</h2>" 
										+ "<b>UPN: </b>" + feature.attributes.UPN.value + "<br>" 
										+ "<b>Owner: </b>" + feature.attributes.Owner.value + "<br>" 
										+ "<b>SubUPN: </b>" + feature.attributes.SubUPN.value + "<br>" 
										+ "<b>Payment Status: </b>" + feature.attributes.PayStatus.value + "<br>" 
										+ "<b>Revenue Due: </b>" + feature.attributes.RevenueDue.value + "<br>" 
										+ "<b>Revenue Collected: </b>" + feature.attributes.RevenueCollected.value + "<br>" 
										+ "<b>Revenue Balance: </b>" + feature.attributes.RevenueBalance.value + "<br>" 
										+ "<b>Payment Date: </b>" + feature.attributes.DatePayment.value + "<br>" 
										+ "<b>Street: </b>" + feature.attributes.StreetName.value + "<br>" 
										+ "<b>House Nr.: </b>" + feature.attributes.HouseNumber.value + "<br>" 
										+ "<b>Landuse: </b>" + feature.attributes.Landuse.value;

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
  function onFeatureSelectSub(event) {
            var feature = event.feature;
					var content = "<h2>"+feature.attributes.description + "</h2>"; 
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