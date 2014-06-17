<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Table view</title>
<link rel="stylesheet" href="../css/ex.css" type="text/css" />
<style type="text/css">

table.demoTbl {
    border-collapse: collapse;
    border-spacing: 0;
}

table.tblCap {
    font-weight:bold;
    margin:1em auto .4em;
}

table.demoTbl .title {
    width:200px;
}
table.demoTbl .prices {
    width:120px;
}

tr:nth-of-type(even) {
      background-color:#ccc;
    }

table.demoTbl td, table.demoTbl th {
    padding: 6px;
}

table.demoTbl th.first {
    text-align:left;
    background-color:green; 
    }
table.demoTbl td.num {
    text-align:right;
    }
    
table.demoTbl td.foot {
    text-align: center;
}

</style>
</head>
<body>
<!-- Progress bar holder -->
<div id="progress" style="width:500px;border:1px solid #ccc;"></div>
<!-- Progress information -->
<div id="information" style="width">Please be patient, process is time consuming!</div>

<?php
	// DB connection
	require_once( "../lib/configuration.php"	);
	if (empty($_GET['districtid'])) {
		$statement = $pdo->query("SELECT * from property");
		$nRows = $pdo->query("select count(*) from property")->fetchColumn(); 
	}else{
		$statement = $pdo->query("SELECT * from property WHERE `districtid`='".$_GET['districtid']."'");
		$nRows = $pdo->query("select count(*) from property WHERE `districtid`='".$_GET['districtid']."'")->fetchColumn(); 
	}
	$total = $nRows; //10;
	$j=1;
	$r=1;

// This is for the buffer achieve the minimum size in order to flush data
// echo str_repeat(' ',1024*64);
// print( "The time is " . date("h:i:sa")." - Affected rows: ".$total);    
//flush();

//	$row = $statement->fetch(PDO::FETCH_ASSOC);
// Total processes

     
session_start();
	// match UPN
// include PHPExcel
require('../lib/PHPExcel179/Classes/PHPExcel.php');
// create new PHPExcel object
$objPHPExcel = new PHPExcel;
// set default font
$objPHPExcel->getDefaultStyle()->getFont()->setName('Calibri');
// set default font size
$objPHPExcel->getDefaultStyle()->getFont()->setSize(8);
// create the writer
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");

/**
 * Define currency and number format.
 */
// currency format, € with < 0 being in red color
$currencyFormat = '#,#0.## \€;[Red]-#,#0.## \€';
// number format, with thousands separator and two decimal points.
$numberFormat = '#,#0.##;[Red]-#,#0.##';

// writer already created the first sheet for us, let's get it
$objSheet = $objPHPExcel->getActiveSheet();
// rename the sheet
$objSheet->setTitle('My sales report');

// let's bold and size the header font and write the header
// as you can see, we can specify a range of cells, like here: cells from A1 to A4
$objSheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(12);
$cellCol=chr(65);
$cellRow='1';
// write header
// for ($i = 1; $i < $statement->columnCount(); $i++) {
// 	$objSheet->getCell($cellCol.$cellRow)->setValue($statement->getColumnMeta($i));
// }

// write header
$objSheet->getCell('A1')->setValue('Product');
$objSheet->getCell('B1')->setValue('Quantity');
$objSheet->getCell('C1')->setValue('Price');
$objSheet->getCell('D1')->setValue('Total Price');
// we could get this data from database, but for simplicty, let's just write it
$objSheet->getCell('A2')->setValue('Motherboard');
$objSheet->getCell('B2')->setValue(10);
$objSheet->getCell('C2')->setValue(5);
$objSheet->getCell('D2')->setValue('=B2*C2');

$objSheet->getCell('A3')->setValue('Processor');
$objSheet->getCell('B3')->setValue(6);
$objSheet->getCell('C3')->setValue(3);
$objSheet->getCell('D3')->setValue('=B3*C3');

$objSheet->getCell('A4')->setValue('Memory');
$objSheet->getCell('B4')->setValue(10);
$objSheet->getCell('C4')->setValue(2.5);
$objSheet->getCell('D4')->setValue('=B4*C4');

$objSheet->getCell('A5')->setValue('TOTAL');
$objSheet->getCell('B5')->setValue('=SUM(B2:B4)');
$objSheet->getCell('C5')->setValue('-');
$objSheet->getCell('D5')->setValue('=SUM(D2:D4)');

// bold and resize the font of the last row
$objSheet->getStyle('A5:D5')->getFont()->setBold(true)->setSize(12);

// set number and currency format to columns
$objSheet->getStyle('B2:B5')->getNumberFormat()->setFormatCode($numberFormat);
$objSheet->getStyle('C2:D5')->getNumberFormat()->setFormatCode($currencyFormat);

// create some borders
// first, create the whole grid around the table
$objSheet->getStyle('A1:D5')->getBorders()->
getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
// create medium border around the table
$objSheet->getStyle('A1:D5')->getBorders()->
getOutline()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);
// create a double border above total line
$objSheet->getStyle('A5:D5')->getBorders()->
getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_DOUBLE);
// create a medium border on the header line
$objSheet->getStyle('A1:D1')->getBorders()->
getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_MEDIUM);

// autosize the columns
$objSheet->getColumnDimension('A')->setAutoSize(true);
$objSheet->getColumnDimension('B')->setAutoSize(true);
$objSheet->getColumnDimension('C')->setAutoSize(true);
$objSheet->getColumnDimension('D')->setAutoSize(true);


// write the file
// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="01simple.xlsx"');
header('Cache-Control: max-age=0');
$objWriter->save('php://output');
break;

// 	echo "<table class='demoTbl' border='1' cellpadding='10' cellspacing='1' bgcolor='#FFFFFF'>
// 			<tr'>";
// 
// 			echo '</tr>';
// 
// 
// //mysql_fetch_array( $query,MYSQLI_NUM ) ) 
// 	while( $row = $statement->fetch(PDO::FETCH_BOTH)) 
// 	{
// 	 echo "<tr>";
// 	 for ($x=1; $x<=$statement->columnCount(); $x++)
//   		{
// 		  echo "<td>" . $row[$x] . "</td>";
// 		  
// //    $percent = intval($j/$total * 100)."%";   
//     // Javascript for updating the progress bar and information
//      if (is_int($r/1000)){
// // 		echo '<script language="javascript">
// // // 		document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
// //  		document.getElementById("information").innerHTML="'.$r.' rows processed!";
// // 		</script>';
//       }
// 		 } 
// 	  echo "</tr>";
// 	  if ($j<=$total) {$j++;}
// 	  $r++;
// 	// Send output to browser immediately
//     flush();
// 	  }
// // This is for the buffer achieve the minimum size in order to flush data
// //     echo str_repeat(' ',1024*64);
// 
//     
// // Send output to browser immediately
// //     flush();
// 
//     
// // Sleep one second so we can see the delay
// //    sleep(1);
// 	
//    echo "</table>";
//    // Tell user that the process is completed
// echo '<script language="javascript">document.getElementById("information").innerHTML="Process completed"</script>';

?>


<p>Back to <a href="index.html">Index</a></p>

</body>
</html>