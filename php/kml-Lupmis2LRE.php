<?php

error_reporting(E_ALL);
set_time_limit(0);
ob_start(); // prevent adding duplicate data with refresh (F5)
session_start();

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>Upload geo information to database</title>

</head>
<body>

<h1>KML upload</h1>
<?php
		require_once( "../lib/configuration.php"	);

// var_dump($_POST);
// echo '<br>';
// var_dump($_FILES);
// echo '<br>';
// echo filesize('/Users/ekke/Documents/GOPA/Ghana/LocalRevenueEnhancement/work/FromLOGODEP/Map Data/Tarkwa/Tools/Tarkwagood_status_pdb.kml');
// echo '<br>';
$a_upload_district=explode(" ",$_POST['district']);
$upload_district=$a_upload_district[0];
//If you have received a submission.
    if ($_POST['submit'] == "Upload File"){
      $goodtogo = true;
     } 
      //Check for a blank submission.

      try {
        if ($_FILES['uploadedfile']['size'] == 0){
          $goodtogo = false;
        throw new exception ("Sorry, you must upload a KML file (.KML)");
        }
      } catch (exception $e) {
        echo $e->getmessage();
      }
      //Check for the file size.
      try {
		if ($_FILES['uploadedfile']['size'] > 10000000){
		$goodtogo = false;
		//Echo an error message.
		throw new exception ("Sorry, the file is too big at approx: " . intval ($_FILES['uploadedfile']['size'] / 1000) . "KB");
        }
      } catch (exception $e) {
        echo $e->getmessage();
      }
      //Ensure that you have a valid mime type.

	$allowedmimes = array ("application/vnd.google-earth.kml+xml","text/kml");
      try {
		if (!in_array ($_FILES['uploadedfile']['type'],$allowedmimes)){ $goodtogo = false;
		throw new exception ("Sorry, the file must be of type .kml. Yours is: " . $_FILES['uploadedfile']['type'] . "");
        }
       } 
       catch (exception $e) {
        echo $e->getmessage ();
      }
      //If you have a valid submission, move it, then show it.
      if ($goodtogo){
			try {
			if (!move_uploaded_file ($_FILES['uploadedfile']['tmp_name'],"../kml/".$_FILES['uploadedfile']['name'])){
				$goodtogo = false;
				throw new exception ("There was an error moving the file.");
			  }
			} catch (exception $e) {
			  echo $e->getmessage ();
			} 
		}

if ($goodtogo){


// Where the file is going to be placed 
$target_path = "../kml/";

/* Add the original filename to our target path.  
Result is "uploads/filename.extension" */
$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 
//$target_path = $target_path . "bogoso.kml"; //basename( $_FILES['uploadedfile']['name']); 

$completeurl = $target_path; // "../kml/Prestea_status_igf_prop.kml";

 print("Start import into database, please have some patience");
//  print($upload_district.' - '.$completeurl);
//  print('<br>File exists? '.file_exists($completeurl));
//  break;

if (file_exists($completeurl)) { 
$xml = simplexml_load_file($completeurl, 'SimpleXMLElement', LIBXML_NOCDATA);
$districtid=$upload_district; //$_SESSION['user']['districtid'];//130;
$tmp4='';
//print_r($xml);
// $con=mysqli_connect("localhost","root","root","revenue");
// // Check connection
// if (mysqli_connect_errno())
//   {
//   echo "Failed to connect to MySQL: " . mysqli_connect_error();
//   }
// 
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
    var_dump($upn);
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
//Check whether this record already exists in KML_from_LUPMIS
		$run ="SELECT * from KML_from_LUPMIS WHERE `upn`='".$upn."';";
		$found='';
		$found = mysql_query($run, $con) or die ('Error updating database: ' . mysql_error());
//if the upn already exist, then do an UPDATE, if not do an INSERT
  print("<br>Found: ".$found);

//		if (!empty($found))
		if (mysql_affected_rows()>0)
		{
			$run ="UPDATE KML_from_LUPMIS SET boundary='".$cor_d1."', LUPMIS_color='".$styleUrl."', UPN='".$upn."', Address='".$address."', Landuse='".$landuse."', ParcelOf='".$parcelOf."', districtid='".$districtid."' WHERE UPN='".$upn."';";
			print_r($run);
			 mysql_query($run) or die ('UPDATE - Error updating database: ' . mysql_error());
		 }else
		{       
			$query .='\''.$cor_d1.'\', \''.$styleUrl.'\', \''.$upn.'\', \''.$address.'\', \''.$landuse.'\', \''.$parcelOf.'\', \''.$districtid.'\'';
//			echo $query;
			$run ="INSERT INTO KML_from_LUPMIS (boundary, LUPMIS_color, UPN, Address, Landuse, ParcelOf, districtid) VALUES (".$query." );";
			print_r($run);
			 mysql_query($run) or die ('Error updating database: ' . mysql_error());
		}
// 		break;
	  }

   }else {
 
   exit('Failed to open file '.$completeurl);
  }
  print("<br>Import into database successful - Great!!!");
	//   mysqli_close($con);    
} //end if goodtogo
?>