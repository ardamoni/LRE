$username = "root";
$password = "root";
$database = "LUPMIS";
$url = "localhost";
//echo $clickupn.": clickupn<br>".$upn.": upn<br>".$dataFromJS.": data<br>";
$dbcon = mysql_connect($url, $username , $password);
if (!$con)
{
die('Could not connect:' . mysql_error());
}

mysql_select_db($database, $con) ;

 // Return streets as GeoJSON
   $geojson = array(
      'type'      => 'FeatureCollection',
      'features'  => array()
   );
$run = "SELECT DISTINCT d1.UPN, d1.boundary, d1.id, d2.pay_status from `KML_from_LUPMIS` d1, property d2 WHERE d1.`UPN` = d2.`upn`;";
$query = mysql_query($run);

   // Add edges to GeoJSON array
   while($edge=pg_fetch_assoc($query)) {

      $feature = array(
         'type' => 'Feature',
         'geometry' => json_decode($edge['boundary'], true),
         'crs' => array(
            'type' => 'EPSG',
            'properties' => array('code' => '4326')
         ),
         'properties' => array(
            'id' => $edge['id'],
            'upn' => $edge['UPN']
         )
      );

      // Add feature array to feature collection array
      array_push($geojson['features'], $feature);
   }

   // Close database connection
   pg_close($dbcon);

   // Return  result
   header('Content-type: application/json',true);
   echo json_encode($geojson);
