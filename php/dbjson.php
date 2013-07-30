<?php
$username = "root";
$password = "root";
$database = "LUPMIS";
$url = "localhost";
//echo $clickupn.": clickupn<br>".$upn.": upn<br>".$dataFromJS.": data<br>";
$dbcon = mysql_connect($url, $username , $password);
if (!$dbcon)
{
die('Could not connect:' . mysql_error());
}

mysql_select_db($database, $dbcon) ;

 // Return streets as GeoJSON
   $geojson = array(
      'type'      => 'FeatureCollection',
      'features'  => array()
   );

$run = "SELECT DISTINCT d1.UPN, d1.boundary, d1.id, d2.pay_status from `KML_from_LUPMIS` d1, property d2 WHERE d1.`UPN` = d2.`upn`;";
$query = mysql_query($run);

   // Add row to GeoJSON array
while ($row = mysql_fetch_assoc($query)) {
      $feature = array(
         'type' => 'Feature',
         'geometry' => array(
			'type' => "Polygon", 
            'coordinates' => 'test' // $row['boundary']
            ),
         'properties' => array(
            'id' => $row['id'],
            'upn' => $row['UPN']
         )
      );
      // Add feature array to feature collection array
      array_push($geojson['features'], $feature);
   }
   mysql_close($dbcon);

   // Return  result
   header("Content-type: application/json");
   echo json_encode($geojson);
   // Close database connection
?>