<?
// Where the file is going to be placed 
$target_path = "../kml/";

/* Add the original filename to our target path.  
Result is "uploads/filename.extension" */
$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 

$completeurl = $target_path; // "../kml/Prestea_status_igf_prop.kml";

$getDistrictid = 125; //$_GET['getdistrictid'];

 print("Start import into database, please have some patience");
 print($getDistrictid.' - '.$completeurl);
// break;

if (file_exists($completeurl)) {
$xml = simplexml_load_file($completeurl, 'SimpleXMLElement', LIBXML_NOCDATA);
$districtid=123;
$tmp4='';
//print_r($xml);
$con=mysqli_connect("localhost","root","root","revenue");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

  $placemarks = $xml->Document->Folder->Placemark;
  for ($i = 0; $i < sizeof($placemarks); $i++) {
    $coordinates = $placemarks[$i]->name;
//Get Info out of CDATA
    $cdata = $placemarks[$i]->description;
    $parcelOf=substr($cdata[0], 0, strpos($cdata[0], ":"));
    $address=strstr($cdata[0],'Address: ');
    $pos=strpos($address,'</b>');
    $address=strip_tags(substr($address,13,strpos($address,'</b>')-13));
    $status=strstr($cdata[0],'Status: ');
	$status=strip_tags(substr($status,8,strpos($status,'</b>')));
    $landuse=strstr($cdata[0],'Use: ');
    $landuse=strip_tags(substr($landuse,9,strpos($landuse,'</b>')-1));
    $upn = strstr($cdata[0],'UPN: ');
    $upn=substr($upn,9,13);
//End Get Infor out of CDATA
    $styleUrl = $placemarks[$i]->styleUrl;

//echo " parcelOf: ".$parcelOf."<br> color: ".$styleUrl."<br> address: ".$adddress."<br> use: ".$landuse."<br> status: ".$status."<br> UPN: ".$upn;    break;

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
  		$query .='\''.$cor_d1.'\', \''.$styleUrl.'\', \''.$upn.'\', \''.$address.'\', \''.$landuse.'\', \''.$parcelOf.'\', \''.$districtid.'\'';
  		echo $query;
		$run .="INSERT INTO KML_from_LUPMIS (boundary, LUPMIS_color, UPN, Address, Landuse, ParcelOf, districtid) VALUES (".$query." );";
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