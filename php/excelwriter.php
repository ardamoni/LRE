<?php
/**
 * ExcelWriter
 * this script requires two _POST parameters, and does not return any value. The script creates an XLSX file and store is with the sfile name in ../tmp
 * 1. $_POST['squery'] = a string of any SQL statement
 * 2. $_POST['sfile'] = a string for a filename without the path and the extension (both are added in this script)
 * THIS SOFTWARE IS PROVIDED BY THE FREEBSD PROJECT "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE FREEBSD PROJECT OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

session_start();

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

// Set properties
//echo date('H:i:s') . " Set properties\n";
$objPHPExcel->getProperties()->setCreator("LRE SfDR/GIZ");
$objPHPExcel->getProperties()->setLastModifiedBy("Automated Generation supporting districts IGF");
$objPHPExcel->getProperties()->setTitle("Office 2007 XLSX LRE Document");
$objPHPExcel->getProperties()->setCompany("funded by GIZ");
$objPHPExcel->getProperties()->setSubject("Office 2007 XLSX Document");
$objPHPExcel->getProperties()->setDescription("Document for Office 2007 XLSX, generated using PHP classes by dLREV.");

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

//set the create date, the creator, and affected rows
$rowCount=$rowCount+2;
$objSheet->getCell('A'.$rowCount)->setValue('Created by: '.$_SESSION['user']['name']);
$createDate = date("Y-m-d");
$rowCount++;
$objSheet->getCell('A'.$rowCount)->setValue('On: '.$createDate);
$rowCount++;
$objSheet->getCell('A'.$rowCount)->setValue('Affected rows: '.($rowCount-5));
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

//show preview


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