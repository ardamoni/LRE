<?php
//    require_once("../lib/initialize.php");

error_reporting(E_ALL);
set_time_limit(0);
ob_start(); // prevent adding duplicate data with refresh (F5)
session_start();

date_default_timezone_set('Europe/London');
	//$ffix = new Feefix;

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Upload Scanned Information on Properties or Business to database</title>

</head>
<body>

<h1>PHPExcel Workbook Upload</h1>
<h2>Data Upload</h2>
<?php
require_once('../lib/scanDataPClass.php');
require_once('../lib/scanDataBClass.php');
global $sdBusiness;
global $sdProperty;

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../lib/PHPExcel179/Classes/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';
/** database configuration */
//require_once( "../lib/configuration.php"	);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

var_dump($_POST);

//get the variables passed from parent	
	$inputFileName = $_POST['inputFileName'];
	$inputYear = $_POST['year'];
//check which tables need to be used

	if ($_POST['ifproperty']=='1'){	    
		$targetTable = $sdProperty->tell_table_name();
	}elseif ($_POST['ifproperty']=='0'){
		$targetTable = $sdBusiness->tell_table_name();
	}
	

	//echo $inputFileName;

	class MyReadFilter implements PHPExcel_Reader_IReadFilter
	{
		private $_startRow = 0;

		private $_endRow = 0;

		private $_columns = array();

		public function __construct($startRow, $endRow, $columns) {
			$this->_startRow	= $startRow;
			$this->_endRow		= $endRow;
			$this->_columns		= $columns;
		}

		public function readCell($column, $row, $worksheetName = '') {
			if ($row >= $this->_startRow && $row <= $this->_endRow) {
				if (in_array($column,$this->_columns)) {
					return true;
				}
			}
			return false;
		}
	}


	/**  Identify the type of $inputFileName  **/
	$inputFileType = PHPExcel_IOFactory::identify($inputFileName);


	echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using IOFactory with a defined reader type of ',$inputFileType,'<br />';
	echo date('H:i:s') , " Started Excel dump" , EOL;
	$objReader = PHPExcel_IOFactory::createReader($inputFileType);
	//$objReader = PHPExcel_IOFactory::load($inputFileName);

	$worksheetData = $objReader->listWorksheetInfo($inputFileName);
	

	echo '<h3>Worksheet Information</h3>';
	echo '<ol>';

	foreach ($worksheetData as $worksheet) {
		echo '<li>', $worksheet['worksheetName'], '<br />';
		echo 'Rows: ', $worksheet['totalRows'], ' Columns: ', $worksheet['totalColumns'], '<br />';
		echo 'Cell Range: A1:', $worksheet['lastColumnLetter'], $worksheet['totalRows'];
		echo '<br>';
		echo 'Loading Sheet "',$worksheet['worksheetName'],'" only<br />';
		$objReader->setLoadSheetsOnly($worksheet);
	//	echo 'Loading Sheet using configurable filter<br />';
		$filterrows = $worksheet['totalRows'];
//		$filterrows = 4;
		$filterSubset = new MyReadFilter(1,$filterrows,range('A', '')); //$lastCL));
		$objReader->setReadFilter($filterSubset);
		$objPHPExcel = $objReader->load($inputFileName);
		$i=1;
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		echo '<h3>The following Cell Content of '.$filterrows.' row(s) was uploaded to the table "'.$targetTable.'" </h3>';
		echo '<ol>';
//var_dump($sheetData);
		echo "<table class='demoTbl' border='1' cellpadding='10' cellspacing='1' bgcolor='#FFFFFF'>";
		//echo "<tr>";
		//set count for first row, which most probably is the column descriptor and we don't want that in the db
		$firstrow=1;
		foreach ($sheetData as $cellData) {
			 echo "<tr>";

// if ($firstrow > 1){	
// echo '<br>UPN: '.$cellData['A'].' - ';
// $sdProperty->upn=$cellData['A'];
// $temp=$sdBusiness->find_by_upn(trim($cellData['A']));
// $previousUPN='';
// //var_dump($temp); 
// foreach ($temp as $c) {
// foreach ($c as $key => $value) {
//  if ($key=='id'){
//  echo '<br> <br> key: '.$key.' value: '.$value; 
//  }
//     // do something
// }
// }
// //break;
// }
//var_dump($cellData);
				if ($_POST['ifproperty']=='1'){	    
							$sdProperty->upn=$cellData['A'];
							$sdProperty->subupn=$cellData['B'];
							$sdProperty->districtid=$_POST['districtid'];
							$sdProperty->streetname=$cellData['D'];
							$sdProperty->housenumber=$cellData['E'];
							$sdProperty->locality_code=$cellData['C'];
							$sdProperty->owner=$cellData['F'];
							$sdProperty->owneraddress=$cellData['G'];
							$sdProperty->owner_tel=$cellData['H'];
							$sdProperty->owner_email=$cellData['I'];
							$sdProperty->rooms=$cellData['J'];
							$sdProperty->year_construction=$cellData['K'];
							$sdProperty->property_type=$cellData['L'];
							$sdProperty->property_use=$cellData['M'];
							$sdProperty->persons=$cellData['N'];
							$sdProperty->roofing=$cellData['O'];
							$sdProperty->ownership_type=$cellData['P'];
							$sdProperty->constr_material=$cellData['Q'];
							$sdProperty->storeys=$cellData['R'];
							$sdProperty->value_prop=$cellData['S'];
							$sdProperty->prop_descriptor=$cellData['T'];
							$sdProperty->planningpermit=$cellData['U'];
							$sdProperty->planningpermit_no=$cellData['V'];
							$sdProperty->buildingpermit=$cellData['W'];
							$sdProperty->buildingpermit_no=$cellData['X'];
							$sdProperty->comments='Uploaded by: '.$_SESSION['user']['name'].' - at: '.gmdate(DATE_RFC822).' - Comment: '.$cellData['J'];
//Ascii Character 65=A to 90=Z
							if ($firstrow > 1){	
							  $sdProperty->save(); 
							  } //($firstrow > 1){	
							$firstrow++;
								unset($sdProperty->id);
				
							foreach ($cellData as $key => $value){
								  echo "<td>" . $value . "</td>";
								  }
						   echo "</tr>";
				}elseif ($_POST['ifproperty']=='0'){

							$sdBusiness->upn=$cellData['A'];
							$sdBusiness->subupn=$cellData['B'];
							$sdBusiness->streetname=$cellData['D'];
							$sdBusiness->housenumber=$cellData['E'];
							$sdBusiness->locality_code=$cellData['C'];
							$sdBusiness->da_no=$cellData['F'];
							$sdBusiness->business_certif=$cellData['G'];
							$sdBusiness->employees=$cellData['H'];
							$sdBusiness->business_name=$cellData['I'];
							$sdBusiness->year_establ=$cellData['J'];
							$sdBusiness->landmark1=$cellData['K'];
							$sdBusiness->landmark2=$cellData['L'];
							$sdBusiness->owner=$cellData['M'];
							$sdBusiness->owneraddress=$cellData['N'];
							$sdBusiness->owner_tel=$cellData['O'];
							$sdBusiness->owner_email=$cellData['P'];
							$sdBusiness->business_class=$cellData['Q'];
							$sdBusiness->comments='Uploaded by: '.$_SESSION['user']['name'].' - at: '.gmdate(DATE_RFC822).' - Comment: '.$cellData['J'];
							$sdBusiness->districtid=$_POST['districtid'];

							if ($firstrow > 1){	$sdBusiness->save(); }

							$firstrow++;
								unset($sdBusiness->id);
				
							foreach ($cellData as $key => $value){
								  echo "<td>" . $value . "</td>";
								  }
						   echo "</tr>";
				} //end elseif

		} //end foreeach
		
		   echo "</table>";
		echo '</ol>';
		echo '</li>';
		echo '<hr />';

	} // end foreach
	
//	op_flush();
//	flush();
	
$upnlist=$sdBusiness->find_all_upn();
foreach ($upnlist as $aupn) {
	foreach ($aupn as $key => $value) {
	 if ($key=='id'){
	   $upnid = $value; 
	 }
	 if ($key=='upn'){
		$upn = $value; 
	 }
		$temp=$sdBusiness->find_dups_of_upn($upn,$_POST['districtid']);
		$previousUPN='';
		//var_dump($temp); 
		foreach ($temp as $c) {
			foreach ($c as $key => $value) {
				 if ($key=='id'){
				 echo '<br> <br> key: '.$key.' value: '.$value; 
				 }
				 if ($key=='upn'){
				 echo '<br> <br> key: '.$key.' value: '.$value; 
				 }
				 if ($key=='subupn'){
				 echo '<br> <br> key: '.$key.' value: '.$value; 
				 }
				 if ($key=='business_name'){
				 echo '<br> <br> key: '.$key.' value: '.$value; 
				 }
				// do something
			}
		}

	}//foreach ($aupn as $key => $value) {
}

//break;
	
	
?>
<!--	<form id="form1" name="form1" method="post" action="propertyDetails.php">
	<input name="inputFileName" type="hidden" id="inputFileName" value = "<?php echo $inputFileName;?>">
	Are you sure you want to upload this data into the database?: <input type="submit" id="Submit" name="Submit" value="Upload" />
	</form>
-->
<body>
</html>