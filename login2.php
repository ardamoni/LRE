<?php

  	/*
	 *	No Direct Access To This File
	 *	-----------------------------------------------------------------------
	 */ 
	defined( 'VALID_REVENUE' ) or die( 'STOP' );
 
?>
<style type="text/css">
        .olControlAttribution { 
            bottom: 0px;
            left: 2px;
            right: inherit;
            width: 400px;
        }        

			 #mapLogin {
				width: 720px;
				height: 640px;
				  }
</style>

<form name = "login" action = "index.php" method = "POST">

<table class="maplogin_area">
 	<tr>
		<td> <div id="mapLogin" class="smallmap"></div> </td>
		<td>
			<table width = "300" align = "center" border = "0">
				<tr>
					<td><p id = "form-desc"><?php echo "USERNAME"; ?>:</p>
					<input type = "text" id = "txt1" name = "user" size = "30"><br /><br /></td>
				</tr>
				<tr>
					<td><p id = "form-desc"><?php echo "PASSWORD"; ?>:</p>
					<input type = "password" id = "txt1" name = "pass" size = "30"><br /><br /></td>
				</tr>
				<tr>
					<td colspan = "2">		
						<input type = "submit" value = "<?php echo "SIGN IN"; ?>" id = "btn1">		
					</td>
				</tr>
			</table>
		</td>
	</tr>	
</table>

<script src="lib/OpenLayers/lib/OpenLayers.js"></script> 

<script type="text/javascript">
var mapLogin;

var options = {   
			  scales: [500, 1000, 2500, 5000, 10000],
			  numZoomLevels: 26,
			  allOverlays: true,
			  projection: new OpenLayers.Projection("EPSG:900913"),
			  displayProjection: new OpenLayers.Projection("EPSG:4326"),
			  controls:[
//				new OpenLayers.Control.Navigation(),
//				new OpenLayers.Control.PanZoomBar(),
//				new OpenLayers.Control.LayerSwitcher({'ascending':false}), 
//				new OpenLayers.Control.ScaleLine(),
				new OpenLayers.Control.MousePosition(),
			//	new OpenLayers.Control.OverviewMap(),
				new OpenLayers.Control.Attribution(),
				new OpenLayers.Control.KeyboardDefaults()],};

var sm = new OpenLayers.StyleMap({
			fillColor: "#666666",
			lineColor: "#0033FF"});


var mapLogin = new OpenLayers.Map('mapLogin', options);
//Mapnik
  var mapnik =  new OpenLayers.Layer.OSM("OpenStreetMap");

//KML-Districts      
      var kmldistricts =  new OpenLayers.Layer.Vector("Districts in Ghana", {
            strategies: [new OpenLayers.Strategy.Fixed()],
            visibility: true,
            styleMap: sm,
            projection: mapLogin.displayProjection,
            protocol: new OpenLayers.Protocol.HTTP({
                url: "kml/Ghana_districts.kml",
                format: new OpenLayers.Format.KML({
                    extractStyles: true, 
                    extractAttributes: true,
                    maxDepth: 2
                })
            })
        });
	mapLogin.addLayer(mapnik);     
	mapLogin.addLayer(kmldistricts);
    var ghana = new OpenLayers.LonLat(-1.1759874280090854,8.173345828918867).transform(new OpenLayers.Projection("EPSG:4326"),mapLogin.getProjectionObject());

    mapLogin.setCenter(ghana, 7);

</script>
</form>