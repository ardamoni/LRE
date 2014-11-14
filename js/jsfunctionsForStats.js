// 24. Juli 2013 12:16:13 GMT working on getting polygons on a map
// 01.08.13 11:30 created the LREinit from dbgeojsonpoly.js and placed all the functions in lib/jsfunctions.js
// 27. September 2013 10:10:43 GMT moved previous code from LREinit and created init() in order to keep file count low
 
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
	var globalpropertychanged = false;
	var globalbusinesschanged = false;

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
  top: '50%', // Top position relative to parent in px
  left: '50%' // Left position relative to parent in px
};
var target = document.getElementById('map');
var spinner = new Spinner(spinopts); //.spin(target);


//define the vector layers
//M&E layers
   var districtmap = new OpenLayers.Layer.Vector("Districts from Database", {	
	    visibility: false,
		eventListeners: {"visibilitychanged": getDistrictPoly,
						 }
		});

   var regionmap = new OpenLayers.Layer.Vector("Regions from Database", {	
	    visibility: false,
		eventListeners: {"visibilitychanged": getRegionPoly,
						 }   		
	    });



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


   var sm = new OpenLayers.StyleMap({
    			fillColor: "#666666",
    			lineColor: "#0033FF"});

//    			'default': mystyle,
//Mapnik
      var mapnik =  new OpenLayers.Layer.OSM("OpenStreetMap");

//Google Streets      
  	  var gmap = new OpenLayers.Layer.Google(
					"Google Hybrid",
					{type: google.maps.MapTypeId.HYBRID,
// 					"Google Streets", // the default
// 					{numZoomLevels: 20,
 					visibility: false
					});
    var renderer = OpenLayers.Util.getParameters(window.location.href).renderer;
    renderer = (renderer) ? [renderer] : OpenLayers.Layer.Vector.prototype.renderers;

    
var districtStyleDefault = new OpenLayers.Style({
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

   var districttemporaryStyle = new OpenLayers.Style({
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

var styleDistricts = { 
		// style_definition
		 strokeColor: "#0000FF",
            strokeOpacity: 0.6,
            strokewidth: 1,
//            fillColor: "#C0C0C0",
            fillOpacity: 0.0
	};
    
    
    zoneStyleMap = new OpenLayers.StyleMap({
				 'default': districtStyleDefault,
				 'select': districtselectStyle,
				 'temporary': districttemporaryStyle}); 
				 
	var regionStyleMap = new OpenLayers.StyleMap({
		 'default': styleDistricts,
		 'select': zoneselectStyle,
		 'temporary': temporaryStyle});  
				 

 //modifies the default "default" style settings of OpenLayers

//define the collector zone vector layer    			
	colzones = new OpenLayers.Layer.Vector("Collector Zones", {
		renderers: renderer,
		 visibility: false,
         hover: true,
         styleMap: zoneStyleMap });
         
    
    districtmap.styleMap = zoneStyleMap;
    regionmap.styleMap = regionStyleMap;


// Add Layers
	map.addLayer(mapnik);
	map.addLayer(gmap);
	map.addLayer(regionmap);
	map.addLayer(districtmap);
	
   select = new OpenLayers.Control.SelectFeature([districtmap, regionmap],{
                hover: false,
                highlightOnly: false,
                renderIntent: "temporary",
            }); 
			districtmap.events.on({
                "featureselected": onFeatureSelectDistrict,
                "featureunselected": onFeatureUnselect,

            });
			regionmap.events.on({
			"featureselected": onFeatureSelectRegion,
			"featureunselected": onFeatureUnselect,
            });
            map.addControl(select);
            select.activate();   
	
            
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
    
    var ghana = new OpenLayers.LonLat(-1.175,7.8).transform(new OpenLayers.Projection("EPSG:4326"),map.getProjectionObject());
//    var ghana = new OpenLayers.LonLat(-1.1759874280090854,8.173345828918867).transform(new OpenLayers.Projection("EPSG:4326"),map.getProjectionObject());
    map.setCenter(ghana, 7);

	getSessionUser();

} //end startNormalUser()


  
//-----------------------------------------------------------------------------
		//function onPopupClose() 
		//used to close the popups in various onSelectFeature events
		//
//-----------------------------------------------------------------------------
function onPopupClose(evt) {
        select.unselectAll();
}


//-----------------------------------------------------------------------------
		//function onFeatureSelectDistrict() 
		//is the onclick function for the district map
		//it calls districtinfohandler
//-----------------------------------------------------------------------------
function onFeatureSelectDistrict(evt) {
	feature = evt.feature;
spinner.spin(target);	
//	alert(feature.attributes.districtid);
	var request = OpenLayers.Request.POST({
		url: "php/getdistrictsForStats.php", 
		data: OpenLayers.Util.getParameterString(
		{districtid: feature.attributes.districtid}),
		headers: {
			"Content-Type": "application/x-www-form-urlencoded"
		},
		callback: districtinfohandler
	});
} 
// end of function onFeatureSelectDistrict

//-----------------------------------------------------------------------------
		//function districtinfohandler() 
		//is the handler for the onclick function onFeatureSelectDistrict 
		//for the district map
//-----------------------------------------------------------------------------
function districtinfohandler(request) {
// the browser's parser may have failed
if(!request.responseXML) {
	var html ='';
	var userdistrict='';
	var userdistrictname='';
	var districtboundary='';
	var numberOfParcels='';
	var numberOfProperty='';
	var numberOfProperty_valued='';
	
// 		html = feature.attributes.districtname +
// 		'<br>Area: '+(feature.geometry.getGeodesicArea(proj900913)/1000000).toFixed(2)+' sq km';
// 		document.getElementById("debug1").innerHTML=html;
	
	var numberOfBusiness='';
		// get the response from php and read the json encoded data
		feed=JSON.parse(request.responseText);
		for (var i = 0; i < feed.length; i++) {
			html = '<br>District: '+feature.attributes.districtname;			
			html += '<br><strong>Property: </strong>';
			html += '<li> Balance: '+number_format(feed[i]['sumPropertyBalance'], 0, '.', ',')+' GHC</li>';
			html += '<li> Paid: '+number_format(feed[i]['sumPropertyPaid'], 0, '.', ',')+' GHC</li>';
			html += '<li> Due: '+number_format(feed[i]['sumPropertyDue'], 0, '.', ',')+' GHC</li>';
			html += '<br><strong>Business: </strong>';
			html += '<li> Balance: '+number_format(feed[i]['sumBusinessBalance'], 0, '.', ',')+' GHC</li>';
			html += '<li> Paid: '+number_format(feed[i]['sumBusinessPaid'], 0, '.', ',')+' GHC</li>';
			html += '<li> Due: '+number_format(feed[i]['sumBusinessDue'], 0, '.', ',')+' GHC</li>';			
	 		html += '<br>Area: '+(feature.geometry.getGeodesicArea(proj900913)/1000000).toFixed(2)+' sq km';

		var popup = new OpenLayers.Popup.FramedCloud(
										"featurePopup",
                                        feature.geometry.getBounds().getCenterLonLat(),
                                        new OpenLayers.Size(100,100),
                                        html, 
                                        null, true, onPopupClose);

		feature.popup = popup;
		popup.feature = feature;
		popup.panMapIfOutOfView = true;
		map.addPopup(popup, true);		

// 		document.getElementById("debug1").innerHTML=html;
		}
	}
spinner.stop();
}
// end of function districtinfohandler

//-----------------------------------------------------------------------------
		//function onFeatureSelectRegion() 
		//is the onclick function 
		//for the regions map
//-----------------------------------------------------------------------------
function onFeatureSelectRegion(evt) {
feature = evt.feature;

   // add code to create tooltip/popup
   popup = new OpenLayers.Popup(
	  "region",
	  feature.geometry.getBounds().getCenterLonLat(),
	  new OpenLayers.Size(300,150),
	  "<div class='p'>"+feature.attributes.regionname+
		'<br>Area: '+(feature.geometry.getGeodesicArea(proj900913)/1000000).toFixed(2)+' sq km'+
		'<br>Nr. of Districts: '+ feature.attributes.nrofdistricts+
		'<p>Property Rates Potential: '+ feature.attributes.totalexpprop+
		'<br>Property Rates Balance: '+ feature.attributes.totalbalprop+
		'<br>Business License Potential: '+ feature.attributes.totalexpbus+
		'<br> Business License Balance: '+ feature.attributes.totalbalbus+
	"</p></div>",
	  null,
	  true,
	  null);

   feature.popup = popup;
	feature.popup.displayClass='p';	
	feature.popup.contentDisplayClass='p';	
	feature.popup.opacity=0.8;	
	feature.popup.backgroundColor='LightYellow';	
   map.addPopup(popup);
   // return false to disable selection and redraw
   // or return true for default behaviour
   return true;
}
// end of function onFeatureSelectRegion

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
    url: "php/getsessionForStats.php", 
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
	var numberOfProperty_valued='';
	var numberOfBusiness='';
		// get the response from php and read the json encoded data
		feed=JSON.parse(request.responseText);
		for (var i = 0; i < feed.length; i++) {
			html += feed[i]['username'];
			userdistrict += feed[i]['userdistrict'];
			userrole = feed[i]['userrole'];
			userdistrictname += feed[i]['userdistrictname'];
			numberOfParcels += feed[i]['numberOfParcels'];
			numberOfProperty += feed[i]['numberOfProperty'];
			numberOfProperty_valued += feed[i]['numberOfProperty_valued'];
			numberOfBusiness += feed[i]['numberOfBusiness'];
			districtboundary += feed[i]['districtboundary']};

	document.getElementById("wcUser").innerHTML='Welcome: '+html;
	globaldistrictid=userdistrict;
	globaluserrole=userrole;
	document.getElementById("districtname").innerHTML=userdistrictname;
	document.getElementById("stat1").innerHTML=number_format(numberOfParcels, 0, '.', ',');
	document.getElementById("stat2").innerHTML=number_format(numberOfProperty, 0, '.', ',')+'/v:'+number_format(numberOfProperty_valued, 0, '.', ',');
	document.getElementById("stat3").innerHTML=number_format(numberOfBusiness, 0, '.', ',');
}
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
    url: "php/getfiscalstatsForStats.php", 
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
		//function xlsexport() 
		//opens a window to export tabular data to Excel
		//
//-----------------------------------------------------------------------------
function xlsexport() {
  	var w = 1000;
	var h = 550;
    var left = (screen.width/2)-(w/2);
    var top = (screen.height/2)-(h/2);
	var pageURL = 'php/openXLSreportsForStats.php?districtid='+globaldistrictid;

  
 		popupWindow = window.open(pageURL,"Excel Reports", 'border=0, status=0, width='+w+', height='+h+', top='+top+', left='+left+', resizable=no,location=no,menubar=no,status=no,toolbar=no');	
   
	if(popupWindow && !popupWindow.closed)
	{
		popupWindow.focus();
	}

	return false;

} 
// end of function xlsexport


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
   
	if(popupWindow && !popupWindow.closed)
	{
		popupWindow.focus();
	}

	return false;

} 
// end of function tableshow

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
	 }
	else
	{ 
	 document.getElementById("myCanvas").style.visibility="hidden";
 	 document.getElementById("legend").style.visibility="hidden";
	}
}

//-----------------------------------------------------------------------------
		//function polyhandler() 
		//is the callback handler for getpolygons()
		//it takes the request feed from getlocalplan.php and creates polygones on the Layer fromjson
//-----------------------------------------------------------------------------
function getDistrictPoly() {

//     var ghana = new OpenLayers.LonLat(-1.1759874280090854,8.173345828918867).transform(new OpenLayers.Projection("EPSG:4326"),map.getProjectionObject());
// 
//     map.setCenter(ghana, 7);
// 

//alert('inside');

   if (districtmap.features.length<1) {
	  spinner.spin(target);
    

	var request = OpenLayers.Request.POST({
			url: "php/dbaction.php", 
			data: OpenLayers.Util.getParameterString(
			{dbaction: "getdistrictmap"}),
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			callback: handlerDistrictMap
		});
		 }else{
		spinner.stop()
};           

}
function getRegionPoly() {
//     var ghana = new OpenLayers.LonLat(-1.1759874280090854,8.173345828918867).transform(new OpenLayers.Projection("EPSG:4326"),map.getProjectionObject());
// 
//     map.setCenter(ghana, 7);

   if (regionmap.features.length<1) {
	  spinner.spin(target);
    

	var regionrequest = OpenLayers.Request.POST({
			url: "php/dbaction.php", 
			data: OpenLayers.Util.getParameterString(
			{dbaction: "getregionmap"}),
			headers: {
				"Content-Type": "application/x-www-form-urlencoded"
			},
			callback: handlerRegionMap
		});
     }else{
			spinner.stop()
	};           
}
//-----------------------------------------------------------------------------
		//function polyhandler() 
		//is the callback handler for getpolygons()
		//it takes the request feed from getlocalplan.php and creates polygones on the Layer fromjson
//-----------------------------------------------------------------------------

function handlerDistrictMap(request) {
	// the server could report an error
	if(request.status == 500) {
	   alert('Server reports an error. Please retry in a couple of minutes');
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
			boundary = feed[i]['boundary'].trim();       	
			var coordinates = boundary.split(" ");
			var polypoints = [];
			for (var j=0;j < coordinates.length; j++) {
				points = coordinates[j].split(",");
				point = new OpenLayers.Geometry.Point(points[0], points[1]).transform(projWGS84,proj900913);
				polypoints.push(point);
			}
			// create some attributes for the feature
		var attributes = {districtname: feed[i]['districtname'],districtid: feed[i]['districtid']};
		    // create a linear ring by combining the just retrieved points
		var linear_ring = new OpenLayers.Geometry.LinearRing(polypoints);
		    //the switch checks on the payment status and 
				var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes);//, styleDistricts);		
		  districtmap.addFeatures([polygonFeature]);
		  } // end of for 
		spinner.stop();
		  districtmap.redraw();
	}
}	
function handlerRegionMap(regionrequest) {
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
			boundary = feed[i]['boundary'].trim();       	
			var coordinates = boundary.split(" ");
			var polypoints = [];
			for (var j=0;j < coordinates.length; j++) {
				points = coordinates[j].split(",");
				point = new OpenLayers.Geometry.Point(points[0], points[1]).transform(projWGS84,proj900913);
				polypoints.push(point);
			}
			// create some attributes for the feature
		var attributes = {	regionname: feed[i]['regionname'], 
							regionid: feed[i]['regionid'], 
							nrofdistricts: feed[i]['NrOfDistricts'],
							totaldueprop: feed[i]['TotalPropertyDue'],
							totalexpprop: feed[i]['TotalPropertyExpected'],
							totalpayprop: feed[i]['TotalPropertyPayments'],
							totalbalprop: feed[i]['TotalPropertyBalance'],
							totalduebus: feed[i]['TotalBusinessDue'],
							totalexpbus: feed[i]['TotalBusinessExpected'],
							totalpaybus: feed[i]['TotalBusinessPayments'],
							totalbalbus: feed[i]['TotalBusinessBalance']
							};
		    // create a linear ring by combining the just retrieved points
		var linear_ring = new OpenLayers.Geometry.LinearRing(polypoints);
		    //the switch checks on the payment status and 
				var polygonFeature = new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Polygon([linear_ring]), attributes);//, styleDistricts);		
		  regionmap.addFeatures([polygonFeature]);
		  } // end of for 

		spinner.stop();
		regionmap.redraw();
	}
} // end of function polyhandler