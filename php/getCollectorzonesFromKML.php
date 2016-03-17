<?
$completeurl = "../kml/150Nkoranza-collectorzones-LatLon.kml";
$districtid=150;
 print("Start import into database, please have some patience. File: ".$completeurl);

$colors = ['#1FCB4A', 	'#59955C', 	'#48FB0D', 	'#2DC800', 	'#59DF00', 	'#9D9D00', 	'#B6BA18',
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

var_dump($colors);
if (file_exists($completeurl)) {
$xml = simplexml_load_file($completeurl, 'SimpleXMLElement', LIBXML_NOCDATA);

$tmp4='';
//print_r($xml);
$con=mysqli_connect("localhost","root","root","revenue");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }
$zcol_counter=0;
//$zonecolour='#F5E8E2';
$zonecolour=$colors[$zcol_counter];
  $placemarks = $xml->Document->Folder->Document->Placemark;
  for ($i = 0; $i < sizeof($placemarks); $i++) {
		$districtname = $placemarks[$i]->name;
		echo "<br> dn: ".$districtname."<br>"; //    break;


		//Get geo coordinates
			 $cor_d  =  explode(' ', $placemarks[$i]->Polygon->outerBoundaryIs->LinearRing->coordinates);
			 $cor_d1='';
			 $query = '';
			 $run='';
			 $iCount2 = 1;
		//			  echo "cor_d "; print_r($cor_d); echo "<br>";
			  for ($j = 0; $j < sizeof($cor_d); $j++) {
				foreach($cor_d as $value){
					$tmp2 = explode(',',$value);
					$iCount=1;
					foreach($tmp2 as $value2){
					   $subval= substr($value2,0,2);
						if ($subval == "0".chr(10)) {  //check for carriage return
						   $tmp3=substr($value2,2,strlen($value2)); //here we extract the coordinates without the NewLIne
						   } else
						   {
						   $tmp3=$value2;
						   }
		//			  enter seperator for coordinates, if looks for even or odd numbers and sets the seperator accordingly
					  if($iCount&1) {
							$cor_d1=$cor_d1 . $tmp3 . ',';
							} else {
							$cor_d1=$cor_d1 . $tmp3 . ' ';
							}
					  $iCount++;
					}
				}
			  }
				$cor_d1 = substr($cor_d1,1,strlen($cor_d1)-2);
				$j=$i+1;
		//Get geo coordinates
				$query .='\''.$cor_d1.'\', \''.$districtid.'\', \''.$j.'\', \''.$zonecolour.'\'';
				echo $query;
				$run .="INSERT INTO collectorzones (polygon, districtid, colzonenr, zone_colour) VALUES (".$query." );";
		//		print($i);
		//		if ($iCount2 < 25) {
		//		echo "i ".$i." - ";
		//		}else{
		//		echo "i ".$i." - <br>";
		//		}
		//		$iCount2++;
				 mysqli_query($con,$run) or die ('Error updating database: ' . mysqli_error());
				 $zcol_counter++;
				 $zonecolour=$colors[$zcol_counter];

	  }

  }else {

   exit('Failed to open file '.$completeurl);
  }
  print("<br>Import into database successful - Great!!!");
   mysqli_close($con);

?>