<?php
/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../lib/PHPExcel179/Classes/');

include 'PHPExcel.php';

//require('../lib/PHPExcel179/Classes/PHPExcel.php');
//set the memory to work with large files
ini_set('memory_limit', "1024M");
ini_set('max_execution_time', 800);

// create new PHPExcel object
$objPHPExcel = new PHPExcel;
// set default font
$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
// set default font size
$objPHPExcel->getDefaultStyle()->getFont()->setSize(8);
// create the writer
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
//$objWriterPDF = PHPExcel_IOFactory::createWriter($objPHPExcel, "PDF");

// DB connection
require_once("../lib/configuration.php");
//$pdo->setAttribute(array(PDO::MYSQL_USE_BUFFERED_QUERY=>TRUE)); 

//var_dump($_POST);
//echo $_GET['squery'];

$statement = $pdo->query($_POST['squery']);

$rs1 = $pdo->query('SELECT FOUND_ROWS()');
$rowCount = (int) $rs1->fetchColumn(); 
// echo $rowCount.'<br>';

/**
 * Define currency and number format.
 */
// currency format, € with < 0 being in red color
$currencyFormat = '#,#0.## \€;[Red]-#,#0.## \€';
// number format, with thousands separator and two decimal points.
$numberFormat = '#,#0.##;[Red]-#,#0.##';
$cellCol='A';
$cellRow='1';
$cellCoord=$cellCol.$cellRow;
//echo $cellCoord;

$styleArray = array(
'font' => array(
'bold' => true,
'size' => 10,
),
'alignment' => array(
'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
),
'borders' => array(
'top' => array(
'style' => PHPExcel_Style_Border::BORDER_THIN,
), ),
'fill' => array(
'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
'rotation' => 90,
'startcolor' => array(
'argb' => 'FFA0A0A0',
),
'endcolor' => array(
'argb' => 'FFFFFFFF',
), ),
);

// writer already created the first sheet for us, let's get it
$objSheet = $objPHPExcel->getActiveSheet();
// rename the sheet
$objSheet->setTitle('LRE-xls');

// let's bold and size the header font and write the header
// as you can see, we can specify a range of cells, like here: cells from A1 to A4
//echo $statement->columnCount();

// write header
for ($i = 0; $i < $statement->columnCount(); $i++) {
$col = $statement->getColumnMeta($i);
$objSheet->getCell($cellCol.$cellRow)->setValue($col['name']);
$objSheet->getStyle($cellCol.$cellRow)->getFont()->setBold(true)->setSize(12);
$cellCol++;
}
$lastColumn = $objSheet->getHighestColumn();
$cellRow='2';
$index_ligne='2';

// write the table content to the spreadsheet
while($row = $statement->fetch(PDO::FETCH_NUM)) {
// if ($cellRow<200){
// use array_walk() to utf-encode each value in the row
array_walk($row, 'utf8_encode');
// write the entire row to the current worksheet
$objSheet->fromArray($row, NULL, 'A' . $cellRow);
// increment row number
// }
$cellRow++;
}
// increment $rowCount by one to adjust it to the number of spreadsheet rows
$rowCount++;
// bold and resize the font of the last row
//$objSheet->getStyle('A2:'.$lastColumn.$cellRow)->getFont()->setBold(true)->setSize(9);
//$objPHPExcel->getActiveSheet()->getStyle('A2:'.$lastColumn.$cellRow)->applyFromArray($styleArray);
$objPHPExcel->getActiveSheet()->getStyle('A2:'.$lastColumn.$rowCount)->applyFromArray($styleArray);

// set number and currency format to columns
// $objSheet->getStyle('B2:B5')->getNumberFormat()->setFormatCode($numberFormat);
// $objSheet->getStyle('C2:D5')->getNumberFormat()->setFormatCode($currencyFormat);

// create some borders
// first, create the whole grid around the table
$objSheet->getStyle('A1:'.$lastColumn.'1')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
// create medium border around the table
$objSheet->getStyle('A1:'.$lastColumn.'1')->getBorders()->getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
// create a double border above total line
//$objSheet->getStyle('A5:'.$lastColumn.'5')->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);
// create a medium border on the header line
$objSheet->getStyle('A1:'.$lastColumn.'1')->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

// autosize the columns
for ($column = 'A'; $column != $lastColumn; $column++) {
$objSheet->getColumnDimension($column)->setAutoSize(true);
// Do what you want with the cell
}
$objSheet->getColumnDimension($lastColumn)->setAutoSize(true);


// write the file
$objWriter->save("../tmp/".$_POST['sfile'].".xlsx");
//$objWriterPDF->save("../tmp/".$_POST['sfile'].".pdf");
//Redirect output to a client’s web browser (Excel5)
//$sfile=$_SERVER["DOCUMENT_ROOT"].'/LRE/tmp/01atest.xlsx';
//$objWriter->save($sfile); // __FILE__));

// header('Content-Type: application/vnd.ms-excel');
// header('Content-Disposition: attachment;filename="01LREexport.xlsx"');
// header('Cache-Control: max-age=0');
// ob_clean();
// ob_end_flush();
// $objWriter->save('php://output');
?>