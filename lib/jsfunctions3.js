//created by Ekke on 1. August 2013 10:44:55 GMT

//-----------------------------------------------------------------------------
		//function mouseMoveListener() 
		//displays lon lat information in two boxes above the map
		//
//-----------------------------------------------------------------------------
/*
function mouseMoveListener(event) {
	var lonlat = map.getLonLatFromPixel(event.xy);
			
	OpenLayers.Util.getElement("lon1").innerHTML = lonlat.lon;
	OpenLayers.Util.getElement("lat1").innerHTML = lonlat.lat;
	
	lonlat.transform(map.getProjectionObject(), new OpenLayers.Projection("EPSG:4326"));
	OpenLayers.Util.getElement("lon2").innerHTML = lonlat.lon;
	OpenLayers.Util.getElement("lat2").innerHTML = lonlat.lat;
}
*/
  
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
	// check each feature from layer below if intersecting with the collector zone polygon
		for( var i = 0; i < fromjson.features.length; i++ ) 
		{
			if (feature.geometry.intersects(fromjson.features[i].geometry)) { 
                var checkPoint = new OpenLayers.Geometry.Point(fromjson.features[i].geometry.getBounds().getCenterLonLat().lon,fromjson.features[i].geometry.getBounds().getCenterLonLat().lat);
				if (feature.geometry.containsPoint(checkPoint)){
// coulouring for testing 					fromjson.drawFeature(fromjson.features[i], {fillColor: "#99FF33", strokeColor: "#00ffff"});			
					intersectedUPNs++;
				}
			}			
		}
	content = 'Collector ID: '+feature.attributes.collectorid+
				'<br>District ID: '+feature.attributes.districtid+
				'<br>Area: '+(feature.geometry.getGeodesicArea(proj900913)/1000000)+'sq km'+
				'<br>Properties: '+intersectedUPNs.toString()+
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
			//html += '<p>YEAR: '+ feed[i]['year'] +'</p>';
			html += '<div><strong>Revenue Balance: '+ feed[i]['revenue_balance'] +'</strong></div>';
			html += '<p>Payment Due: '+ feed[i]['revenue_due'] +'</p>';
			html += '<p>Revenue Collected: '+ feed[i]['revenue_collected'] +'</p>';
			html += '<p>Date paid: '+ feed[i]['date_payment'] +'</p>';
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
		html += "<input type='button' value='UPN History' onclick='UPNHistoryOnClick()' >";	
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
		// function collectRevenueOnClick() 
		// on mouse-click activation to create the window for revenue payments

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


// TODO: refresh parent window
function parent_refresh() 
{
	if( popupWindow.closed )
	{
		// refresh parent window
	}	
}	

//-----------------------------------------------------------------------------
		//function getpolygons() 
		//is the onvisibilitychanged event for the Collector Zone Layer
		//it calls dbaction.php - getlocalplan() to retrieve geometry information to draws polygones 
		//from the boundaries stored in the table KML_From_LUPMIS
		//it calls a polyhandler() to actually create the polygones based on the returned data
//-----------------------------------------------------------------------------
function getpolygons() {  
//   alert("inside getpolygones");
   var jsonVisible = fromjson.getVisibility();
   if (fromjson.features.length<1) {
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
     		document.getElementById("debug2").innerHTML='Outstanding revenue: <br>'+global_out_property.toString()+' GHC';					
		}else{
     		document.getElementById("debug2").innerHTML=' - ';					
     		}
			spinner.stop()
	};           

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
			var attributes = {upn: feed[i]['upn']};
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
					document.getElementById("debug2").innerHTML='Outstanding revenue: <br>'+revbalance.toString()+' GHC';
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
		w.stop();  
		document.getElementById("debug1").innerHTML=w.toString();

//       alert(w.toString());
} // end of function polyhandler

//-----------------------------------------------------------------------------
		//function getBusiness() 
		//is the onvisibilitychanged event for the Collector Zone Layer
		//it calls dbaction.php - getlocalplan() to retrieve geometry information to draws polygones 
		//from the boundaries stored in the table KML_From_LUPMIS
		//it calls a polyhandler() to actually create the polygones based on the returned data
//-----------------------------------------------------------------------------
function getBusiness() {  
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
     		document.getElementById("debug2").innerHTML='Outstanding revenue: <br>'+global_out_business.toString()+' GHC';	
     		}else{
     		document.getElementById("debug2").innerHTML=' - ';					
     		}
			spinner.stop()};           

} //end of function getBusiness

//-----------------------------------------------------------------------------
		//function polyhandler() 
		//is the callback handler for getpolygons()
		//it takes the request feed from getlocalplan.php and creates polygones on the Layer fromjson
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
			var attributes = {upn: feed[i]['upn']};
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
					document.getElementById("debug2").innerHTML='Outstanding revenue: <br>'+revbalance.toString()+' GHC';					
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

function onVisibiltyChangedRedGreen(event) {
	var foundUPN=fromjson.getFeaturesByAttribute('upn', '609-0615-0746');
	var foundUPN2=fromjson.getFeaturesByAttribute('upn', '608-0615-0401');
	alert("ekke visibility "+foundUPN.length+" "+foundUPN[0].id+" "+map.getNumLayers());
	 fromjson.drawFeature(fromjson.getFeatureById(foundUPN[0].id), {fillColor: "#99FF33", strokeColor: "#00ffff"})
	 fromjson.drawFeature(fromjson.getFeatureById(foundUPN2[0].id), {fillColor: "#99FF33", strokeColor: "#00ffff"})
	 fromjson.addFeature(fromjson.getFeatureById(foundUPN[0].id), {fillColor: "#99FF33", strokeColor: "#00ffff"})
	 fromjson.redraw();
} 
// end of function onVisibiltyChangedRedGreen

//-----------------------------------------------------------------------------
		//function searchupn() 
		//is called by the SEARCH button in the main windows 
		//
//-----------------------------------------------------------------------------
function searchupn() {
	var s = document.getElementById('searchBox').value;
	s = s.trim();
	var jsonPVisible = fromjson.getVisibility();
	var jsonBVisible = fromBusiness.getVisibility();
	if (jsonPVisible){
   		var searchlayer=fromjson.id;
   }
   else if (jsonBVisible)
   {	var searchlayer=fromBusiness.id; }
   else
   { 	html = 'Please open either the Properties or the Business Map! \n Search for '+s+' is only possible in these two Maps';
   		alert(html);
   	}
   
   var checkentry =(s.match(/-/g)||[]).length; //this checks if two - signs are in the entry
   var checkentry2 =s.length; //this checks if two '-' signs are in the entry
   if ((checkentry != 2) || (checkentry2 < 13)){
    html = 'Please check your entry! \n'+s+'\nappears to be an incorrect UPN';
	alert(html);}
	
	foundUPN=map.getLayer(searchlayer).getFeaturesByAttribute('upn', s);
	if (foundUPN.length > 0){
	for (var i = 0; i < foundUPN.length; i++) {
			map.getLayer(searchlayer).drawFeature(map.getLayer(searchlayer).getFeatureById(foundUPN[i].id), {fillColor: "#99FF33", strokeColor: "#00ffff"});			
			}
	}else{
	html = 'UPN: '+s+' could not be found!\nPlease check your entry';
	alert(html);
	}		
} 
// end of function searchupn

