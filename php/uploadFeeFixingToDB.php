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

<title>PHPExcel Upload Fee Fixing information to database</title>

</head>
<body>

<h1>PHPExcel Workbook Upload</h1>
<h2>Fee Fixing Upload</h2>
<?php
require_once('../lib/configuration.php');
//require_once('../lib/feefixingClass.php');
//global $feefixp;

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

	if ($_POST['ifproperty']=='1'){
//		$targetTable = $feefixp->tell_table_name();
	}elseif ($_POST['ifproperty']=='0'){
//		$targetTable = $feefixb->tell_table_name();
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
//var_dump($cellData);
		$readData = array(
			'code' => $cellData['A'],
			'class' => $cellData['B'],
			'category' => $cellData['C'],
			'rate' => $cellData['D'],
			'unit' => $cellData['E'],
			'assessed' => $cellData['F'],
			'rate_impost' => $cellData['G'],
			'code_of_zone' => $cellData['H'],
			'name_of_zone' => $cellData['I'],
			'comments' => 'Uploaded by: '.$_SESSION['user']['name'].' - at: '.gmdate(DATE_RFC822).' - Comment: '.$cellData['J'],
			'districtid' => $_POST['districtid'],
			'year' => $_POST['year']
			);
		$bind = array(
			":code" => $cellData['A'],
			":districtid" => $_POST['districtid'],
			":year" => $_POST['year']
		);
		if ($firstrow > 1){
			$stmt = $pdo->prepare('SELECT * FROM fee_fixing_property WHERE districtid = :districtid AND code = :code AND year = :year');
			$stmt->bindParam(':districtid', $_POST['districtid'], PDO::PARAM_STR);
			$stmt->bindParam(':code', $cellData['A'], PDO::PARAM_STR);
			$stmt->bindParam(':year', $_POST['year'], PDO::PARAM_STR);
			$stmt->execute();
//			$stmt = $pdo->select("feefixing_property", "districtid = :districtid AND code = :search AND year = :year", $bind); //prepare('SELECT * FROM feefixing_property WHERE ID=?');
			$row = $stmt->fetch(PDO::FETCH_ASSOC);

			if( ! $row)
			{
			$result = $pdo->insert("fee_fixing_property", $readData);
//				die('nothing found');
			}else {
			$result = $pdo->update("fee_fixing_property", $readData, "districtid = :districtid AND code = :search AND year = :year", $bind);
			}
		}

		$firstrow++;

		foreach ($cellData as $key => $value){
			  echo "<td>" . $value . "</td>";
			  }
		   echo "</tr>";
		} // end foreach sheetData as $cellData

		   echo "</table>";
		echo '</ol>';
		echo '</li>';
		echo '<hr />';

	} // end foreach worksheetData as $worksheet

?>
<!--	<form id="form1" name="form1" method="post" action="propertyDetails.php">
	<input name="inputFileName" type="hidden" id="inputFileName" value = "<?php echo $inputFileName;?>">
	Are you sure you want to upload this data into the database?: <input type="submit" id="Submit" name="Submit" value="Upload" />
	</form>
-->
<body>
</html>