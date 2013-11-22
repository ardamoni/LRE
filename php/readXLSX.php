<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Generic Excel Workbook Information</title>

</head>
<body>

<h1>PHPExcel Excel Workbook Information</h1>
<h2>Simple Excel Workbook Information</h2>
<?php

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../lib/PHPExcel179/Classes/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';
define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');


//	var_dump($_FILES);
//	echo '<br> <br>';
//	var_dump($_POST);
//$goodtogo = true;

//If you have received a submission.
    if ($_POST['submit'] == "Upload File"){
      $goodtogo = true;
     } 
      //Check for a blank submission.

      try {
        if ($_FILES['uploadedfile']['size'] == 0){
          $goodtogo = false;
        throw new exception ("Sorry, you must upload a spreadsheet (XLS, XLSX, CSV)");
        }
      } catch (exception $e) {
        echo $e->getmessage();
      }
      //Check for the file size.
      try {
		if ($_FILES['uploadedfile']['size'] > 500000){
		$goodtogo = false;
		//Echo an error message.
		throw new exception ("Sorry, the file is too big at approx: " . intval ($_FILES['uploadedfile']['size'] / 1000) . "KB");
        }
      } catch (exception $e) {
        echo $e->getmessage();
      }
      //Ensure that you have a valid mime type.

	$allowedmimes = array ("application/vnd.openxmlformats-officedocument.spreadsheetml.sheet","text/csv");
      try {
		if (!in_array ($_FILES['uploadedfile']['type'],$allowedmimes)){ $goodtogo = false;
		throw new exception ("Sorry, the file must be of type .xls. Yours is: " . $_FILES['uploadedfile']['type'] . "");
        }
       } 
       catch (exception $e) {
        echo $e->getmessage ();
      }
      //If you have a valid submission, move it, then show it.
      if ($goodtogo){
		try {
		if (!move_uploaded_file ($_FILES['uploadedfile']['tmp_name'],"../uploads/".$_FILES['uploadedfile']['name'])){
            $goodtogo = false;
            throw new exception ("There was an error moving the file.");
          }
        } catch (exception $e) {
          echo $e->getmessage ();
	} }
  
if ($goodtogo){

	// Where the file is going to be placed
	$target_path = "../uploads/" ;
	
	/* Add the original filename to our target path.
	Result is "uploads/filename.extension" */
	$target_path = $target_path . basename( $_FILES['uploadedfile']['name']);
	
//	echo '<br>'.$target_path.'<br>'.'<br>';
	
	$inputFileName = $target_path; //'../xls/Property.xlsx';
//$inputFileName = '../xls/Business.xlsx';

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
//		$filterrows = $worksheet['totalRows'];
		$filterrows = 10;
		$filterSubset = new MyReadFilter(1,$filterrows,range('A', '')); //$lastCL));
		$objReader->setReadFilter($filterSubset);
		$objPHPExcel = $objReader->load($inputFileName);
		$i=1;
		$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
		echo '<h3>Cell Content (First '.$filterrows.' row(s) only)</h3>';
		echo '<ol>';

		echo "<table class='demoTbl' border='1' cellpadding='10' cellspacing='1' bgcolor='#FFFFFF'>";
		//echo "<tr>";

		foreach ($sheetData as $cellData) {
			 echo "<tr>";
			foreach ($cellData as $key => $value)
				  echo "<td>" . $value . "</td>";
		   echo "</tr>";
		}
	
		   echo "</table>";
		echo '</ol>';

		echo '</li>';
		echo '<hr />';

	} // end foreach
} // if gooodtogo
//} else { echo "No POST";}

?>
outside
<body>
</html>