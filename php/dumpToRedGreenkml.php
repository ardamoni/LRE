<?
ini_set('display_errors','1');
require_once('helper.php');
$completeurl = "../kml/RedGreen.kml";
// print("Start import into database, please have some patience");

//if (file_exists($completeurl)) {
//    echo $completeurl." already exists, please choose another filename" ; break;
//    }else{
$toKML = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>'
                          .'<kml xmlns="http://www.opengis.net/kml/2.2">'
                          .'<Document></Document></kml>');
$con=mysqli_connect("localhost","root","root","LUPMIS");
// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

//$doc = $toKML->Document;
$folder=$toKML->Document->addChild('Folder');
$folder->addChild('name','Bogoso');
$schema=$folder->addChild('Schema');//
$schema->addAttribute('name','LUPMIS2KML');
$schema->addAttribute('id','LUPMIS2KML');

//SimpleField
$sField=$schema->addChild('SimpleField',' ');
$sField->addAttribute('name','ParcelOf');
$sField->addAttribute('type','string');
$sField2=$schema->addChild('SimpleField',' ');
$sField2->addAttribute('name','UPN');
$sField2->addAttribute('type','string');

$data = array();
// query your DataBase here looking for a match to $input
$run = "SELECT DISTINCT d1.UPN, d1.`boundary`, d1.`Landuse`, d1.`LUPMIS_color`, d1.`ParcelOf`, d2.* from `KML_from_LUPMIS` d1, `property` d2 WHERE d1.`UPN` = d2.`upn`;";
echo $run."<br>"; 
$query=mysqli_query($con,$run, MYSQLI_USE_RESULT) or die ('Error updating database: ' . mysqli_error());
$i=1;
$sub = false;
while ($row = mysqli_fetch_assoc($query)) {
//echo $i."<br>";
	$pm=$folder->addChild('Placemark');//
	$style=$pm->addChild('Style',' ');
	$linestyle=$style->addChild('LineStyle',' ');
	$color=$linestyle->addChild('color','3214B4FA');
	$polystyle=$style->addChild('PolyStyle',' ');
	$fill=$polystyle->addChild('fill','1');
	if (!empty($row['pay_status'])) {
	  //other colors are needed for other payment status indicators
		switch($row['pay_status']) {
			case 1:
			  $color=$polystyle->addChild('color','32143CE6');
			  break;
			case 9:  
			  $color=$polystyle->addChild('color','3200B414');
			  break;
		}
	}else{
		$color=$polystyle->addChild('color','23FF783C'); 
	}
	$polygon=$pm->addChild('Polygon',' ');
	$outerboundary=$polygon->addChild('outerBoundaryIs',' ');
	$linearring=$outerboundary->addChild('LinearRing',' ');
	$coordinates=$linearring->addChild('coordinates',trim($row['boundary']));

	$extendeddata=$pm->addChild('ExtendedData',' ');
		$schemadata=$extendeddata->addChild('SchemaData',' ');
			$schemadata->addAttribute('schemaUrl','#LUPMIS2KML');
				if (!empty($row['ParcelOf'])) {
				$sField=$schemadata->addChild('SimpleData',$row['ParcelOf']); }else{$sField=$schemadata->addChild('SimpleData',' ');}
				$sField->addAttribute('name','ParcelOf');
				if (!empty($row['upn'])) {
				$sField2=$schemadata->addChild('SimpleData',$row['upn']); }else{$sField2=$schemadata->addChild('SimpleData',' ');}
				$sField2->addAttribute('name','UPN');
	$i++;
}  	
//$toKML = utf8_encode($toKML);
$dom = new DOMDocument('1.0');
$dom->preserveWhiteSpace = false;
$dom->formatOutput = true;
$dom->loadXML($toKML->asXML());
//Echo XML - remove this and following line if echo not desired
echo $dom->saveXML();
//Save XML to file - remove this and following line if save not desired
$dom->save($completeurl);

//$toKML->asXML($completeurl);
print_r($toKML);
mysqli_close($con);    
//} //end if file
?>