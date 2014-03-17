<?
$completeurl = "../kml/regions.kml";
 print("Start import into database, please have some patience");

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

  $placemarks = $xml->Document->Folder->Document->Placemark;
  for ($i = 0; $i < sizeof($placemarks); $i++) {
    $regionname = $placemarks[$i]->name;
echo "<br> dn: ".$regionname."<br>"; //    break;


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
//Get geo coordinates
  		$query .='\''.$cor_d1.'\', \''.$regionname.'\'';
  		echo $query;
		$run .="INSERT INTO KML_from_regions (boundary, regionname) VALUES (".$query." );";
//		print($i);
//		if ($iCount2 < 25) {
//		echo "i ".$i." - "; 
//		}else{
//		echo "i ".$i." - <br>";
//		}
//		$iCount2++;
		 mysqli_query($con,$run) or die ('Error updating database: ' . mysqli_error());
	  }

  }else {
 
   exit('Failed to open file '.$completeurl);
  }
  print("<br>Import into database successful - Great!!!");
   mysqli_close($con);    

?>