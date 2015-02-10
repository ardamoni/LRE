<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Table view</title>
<link rel="stylesheet" href="../css/ex.css" type="text/css" />
<link rel="stylesheet" href="../css/flatbuttons.css" type="text/css" />
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

//var_dump($_GET);

$displayText = $_GET['displayText'];
$statement = $pdo->query($_GET['squery']);
$rs1 = $pdo->query('SELECT FOUND_ROWS()');
$rowCount = (int) $rs1->fetchColumn();

$total = $rowCount; //10;
$j=1;
$r=1;

print( "The time is " . date("h:i:sa")." - Affected rows: ".$total);

session_start();
	// match UPN
	echo $displayText.'<br><br>';

	echo "<table class='demoTbl' border='1' cellpadding='10' cellspacing='1' bgcolor='#FFFFFF'>
			<tr'>";
			for ($i = 0; $i < $statement->columnCount(); $i++) {
				$col = $statement->getColumnMeta($i);
			//    echo '<th style="width:'.$col['len'].'em">'.$col['name'].'</th>';
				echo '<th width="500em">'.$col['name'].'</th>';
			}
			echo '</tr>';


//mysql_fetch_array( $query,MYSQLI_NUM ) )
	while( $row = $statement->fetch(PDO::FETCH_BOTH))
	{
	 echo "<tr>";
	 for ($x=0; $x<$statement->columnCount(); $x++)
  		{
		  echo "<td>" . $row[$x] . "</td>";
	    }
	  echo "</tr>";
	// Send output to browser immediately
    flush();
	  }
// This is for the buffer achieve the minimum size in order to flush data
//     echo str_repeat(' ',1024*64);


// Send output to browser immediately
//     flush();


// Sleep one second so we can see the delay
//    sleep(1);

   echo "</table>";
   // Tell user that the process is completed
echo '<script language="javascript">document.getElementById("information").innerHTML="Process completed"</script>';
flush();
?>

<p><input type="button" a href="javascript:;" onclick="parent.window.close();" class="orange-flat-small" value="Close Preview"></a></p>

</body>
</html>