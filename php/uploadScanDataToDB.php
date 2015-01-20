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

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../lib/PHPExcel179/Classes/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';
/** database configuration */
//require_once( "../lib/configuration.php"	);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

//var_dump($_POST);

//get the variables passed from parent	
	$inputFileName = $_POST['inputFileName'];
	$inputYear = $_POST['year'];
//check which tables need to be used

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
// 		$filterrows = 10;
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
			if ($firstrow > 1){	
	  		    storedata($cellData);				  
			} //end if firstrow	
     		$cellTemp=$cellData;
			$firstrow++;
		} //end foreeach ($sheetData as $cellData) {
		
		   echo "</table>";
		echo '</ol>';
		echo '</li>';
		echo '<hr />';

	} // end foreach
	
	setSubupn();
	
	
function storedata($cellTemp)
{
global $sdBusiness;
global $sdProperty;

$funnyChar = array('|'=>'1','I'=>'1','S'=>'5','O'=>'0','s'=>'5','o'=>'0','i'=>'1','D'=>'0', ' '=>'');

if ($_POST['ifproperty']=='1'){	    
	$targetTable = $sdProperty->tell_table_name();
}elseif ($_POST['ifproperty']=='0'){
	$targetTable = $sdBusiness->tell_table_name();
}
if (!empty($cellTemp['A'])){
 $upn=$cellTemp['A'];
//erase known funny characters in upn
 foreach ($funnyChar as $key => $value) {
		if (strpos($upn,$key)>=0) {
			$upn=str_replace($key,$value,$upn);
		}
  }
	//upload property data
	if ($_POST['ifproperty']=='1'){	    
		$sdProperty->upn=$upn; //without funny characters
		$sdProperty->subupn=$cellTemp['B'];
		$sdProperty->districtid=$_POST['districtid'];
		$sdProperty->streetname=$cellTemp['D'];
		$sdProperty->housenumber=$cellTemp['E'];
		$sdProperty->locality_code=$cellTemp['C'];
		$sdProperty->owner=$cellTemp['F'];
		$sdProperty->owneraddress=$cellTemp['G'];
		$sdProperty->owner_tel=$cellTemp['H'];
		$sdProperty->owner_email=$cellTemp['I'];
		$sdProperty->rooms=$cellTemp['J'];
		$sdProperty->year_construction=$cellTemp['K'];
		$sdProperty->property_type=$cellTemp['L'];
		$sdProperty->property_use=$cellTemp['M'];
		$sdProperty->persons=$cellTemp['N'];
		$sdProperty->roofing=$cellTemp['O'];
		$sdProperty->ownership_type=$cellTemp['P'];
		$sdProperty->constr_material=$cellTemp['Q'];
		$sdProperty->storeys=$cellTemp['R'];
		$sdProperty->prop_value=$cellTemp['S'];
		$sdProperty->prop_descriptor=$cellTemp['T'];
		$sdProperty->planningpermit=$cellTemp['U'];
		$sdProperty->planningpermit_no=$cellTemp['V'];
		$sdProperty->buildingpermit=$cellTemp['W'];
		$sdProperty->buildingpermit_no=$cellTemp['X'];
		$sdProperty->comments='Uploaded by: '.$_SESSION['user']['name'].' - at: '.gmdate(DATE_RFC822).' - Comment: '.$cellTemp['J'];
//Ascii Character 65=A to 90=Z
		$sdProperty->save(); 
		unset($sdProperty->id);

		//show table on screen
		foreach ($cellTemp as $key => $value){
			  echo "<td>" . $value . "</td>";
			  }
	   echo "</tr>";
	}
	//upload business data
	elseif ($_POST['ifproperty']=='0'){
		$sdBusiness->upn=$upn;
		$sdBusiness->subupn=$cellTemp['B'];
		$sdBusiness->streetname=$cellTemp['D'];
		$sdBusiness->housenumber=$cellTemp['E'];
		$sdBusiness->locality_code=$cellTemp['C'];
		$sdBusiness->da_no=$cellTemp['F'];
		$sdBusiness->business_certif=$cellTemp['G'];
		$sdBusiness->employees=$cellTemp['H'];
		$sdBusiness->business_name=$cellTemp['I'];
		$sdBusiness->year_establ=$cellTemp['J'];
		$sdBusiness->landmark1=$cellTemp['K'];
		$sdBusiness->landmark2=$cellTemp['L'];
		$sdBusiness->owner=$cellTemp['M'];
		$sdBusiness->owneraddress=$cellTemp['N'];
		$sdBusiness->owner_tel=$cellTemp['O'];
		$sdBusiness->owner_email=$cellTemp['P'];
		$sdBusiness->business_class=$cellTemp['Q'];
		$sdBusiness->comments='Uploaded by: '.$_SESSION['user']['name'].' - at: '.gmdate(DATE_RFC822).' - Comment: '.$cellTemp['J'];
		$sdBusiness->districtid=$_POST['districtid'];

			$sdBusiness->save();

			unset($sdBusiness->id);
		//show table on screen
		foreach ($cellTemp as $key => $value){
			  echo "<td>" . $value . "</td>";
			  }
	   echo "</tr>";
	} //end elseif
	} //end if !empty(upn)
}	// end function

function setSubupn() {

require_once( "../lib/configuration.php"	);
require_once( "../lib/System.php" );

global $sdBusiness;
global $sdProperty;

//check which table is affected 1=property, 0=business
if ($_POST['ifproperty']=='1'){	    
	$targetTable = $sdProperty->tell_table_name();
}elseif ($_POST['ifproperty']=='0'){
	$targetTable = $sdBusiness->tell_table_name();
}//end else if
	
//check which table is affected 1=property, 0=business
if ($_POST['ifproperty']=='1'){	    
//this is the part were the subupn is generated for the Property data
	$targetTable = $sdProperty->tell_table_name();	
	//get all duplicates from the table; 
	$resultdb = $sdProperty->find_dups_of_upn($_POST['districtid']);
// 	var_dump($resultdb);
	$a_temp= array();
	$a_t= array();
	$subupncounter=1;
	$inwhile=false;
	$loop=true;
//	We need to read the return values into a normal array
	foreach ($resultdb as $c) {
		foreach ($c as $key => $value) {
			 $a_temp[$key]=$value;
			 }
		 $a_t[] = $a_temp;
		 }
// now we can work with $a_t[]		 
	for ($x=0; $x<=count($a_t); $x++){
		$upn=$a_t[$x]['upn'];
		$i=0;
		if (!empty($a_t[$x]['id'])){
			while ($loop){
				$subupn=$a_t[$x]['upn'].chr(65+$i);
				$sql = "UPDATE property SET ";
				$sql .= "subupn='".$subupn."'";
				$sql .= " WHERE id='".$a_t[$x]['id']."'";
				$query = mysql_query($sql); 
				if ($query) {
					$loop=true;
				}else {
					$loop=false;
				}
				if ($a_t[$x]['upn']==$a_t[$x+1]['upn']) {
					$loop=true;
				 } else {
					$loop=false;
				 }
				$subupncounter++;
				 $x++;
				 $i++;
			} //end while
			$x=$x-1;
			$loop=true;
			$subupncounter=1;	
		} // end if empty
	} //end for
}elseif ($_POST['ifproperty']=='0'){
//this is the part were the subupn is generated for the Business data
	$targetTable = $sdBusiness->tell_table_name();	
	//get all duplicates from the table
	$resultdb = $sdBusiness->find_dups_of_upn($_POST['districtid']);
// 	var_dump($resultdb);
	$a_temp= array();
	$a_t= array();
	$subupncounter=1;
	$inwhile=false;
	$loop=true;
//	We need to read the return values into a normal array
	foreach ($resultdb as $c) {
		foreach ($c as $key => $value) {
			 $a_temp[$key]=$value;
			 }
		 $a_t[] = $a_temp;
		 }
// now we can work with $a_t[]		 
	for ($x=0; $x<=count($a_t); $x++){
		$upn=$a_t[$x]['upn'];
		$i=0;
		if (!empty($a_t[$x]['id'])){
		while ($loop){
 			$subupn=$a_t[$x]['upn'].'/'.$subupncounter;
 			$sql = "UPDATE business SET ";
 			$sql .= "subupn='".$subupn."'";
 			$sql .= " WHERE id='".$a_t[$x]['id']."'";
 			$query = mysql_query($sql); 
		if ($a_t[$x]['upn']==$a_t[$x+1]['upn']) {
 			$loop=true;
		 } else {
		    $loop=false;
		 }
		$subupncounter++;
		 $x++;
		}
		$x=$x-1;
		$loop=true;
		$subupncounter=1;
		}
	}
} //end else if
//op_flush();
//flush();
} //end function


?>
<!--	<form id="form1" name="form1" method="post" action="propertyDetails.php">
	<input name="inputFileName" type="hidden" id="inputFileName" value = "<?php echo $inputFileName;?>">
	Are you sure you want to upload this data into the database?: <input type="submit" id="Submit" name="Submit" value="Upload" />
	</form>
-->
<body>
</html>