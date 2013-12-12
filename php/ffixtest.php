<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Demo Form Result</title>
<link rel="stylesheet" href="css/ex.css" type="text/css" />
</head>
<body>

<h1>Form Submission Result</h1>

<?php
echo "<pre>";
var_dump($_POST);
$sheetData[]=$data;
var_dump($sheetData);
		echo "<table class='demoTbl' border='1' cellpadding='10' cellspacing='1' bgcolor='#FFFFFF'>";
		//echo "<tr>";
foreach ($sheetData as $cellData) {
 			 echo "<tr>";
			foreach ($cellData as $key => $value){
				  echo "<td>" . $key . $value . "</td>";
				  }
		   echo "</tr>";
 		}
	
		   echo "</table>";
		echo '</ol>';

		echo '</li>';
		echo '<hr />';

echo "</pre>";
?>

<p>&nbsp;</p>
</body>
</html>