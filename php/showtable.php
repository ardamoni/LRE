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
	
$statement = $pdo->query($_GET['squery']);
$rs1 = $pdo->query('SELECT FOUND_ROWS()');
$rowCount = (int) $rs1->fetchColumn(); 

// echo $rowCount.'<br>';

// 	if (empty($_GET['districtid'])) {
// 		$statement = $pdo->query("SELECT * from property");
// 		$nRows = $pdo->query("select count(*) from property")->fetchColumn(); 
// 	}else{
// 		$statement = $pdo->query("SELECT * from property WHERE `districtid`='".$_GET['districtid']."'");
// 		$nRows = $pdo->query("select count(*) from property WHERE `districtid`='".$_GET['districtid']."'")->fetchColumn(); 
// 	}
	$total = $rowCount; //10;
	$j=1;
	$r=1;

// This is for the buffer achieve the minimum size in order to flush data
echo str_repeat(' ',1024*64);
print( "The time is " . date("h:i:sa")." - Affected rows: ".$total); 
// echo '<p><input type="button" a href="javascript:;" onclick="window.close();" class="orange-flat-small" value="Close Preview"></a></p>';

//flush();

//	$row = $statement->fetch(PDO::FETCH_ASSOC);
// Total processes

     
session_start();
	// match UPN

	echo "<table class='demoTbl' border='1' cellpadding='10' cellspacing='1' bgcolor='#FFFFFF'>
			<tr'>";
for ($i = 0; $i < $statement->columnCount(); $i++) {
    $col = $statement->getColumnMeta($i);
    echo '<th>'.$col['name'].'</th>';
}

			echo '</tr>';


//mysql_fetch_array( $query,MYSQLI_NUM ) ) 
	while( $row = $statement->fetch(PDO::FETCH_BOTH)) 
	{
	 echo "<tr>";
	 for ($x=0; $x<$statement->columnCount(); $x++)
  		{
		  echo "<td>" . $row[$x] . "</td>";
		  
//    $percent = intval($j/$total * 100)."%";   
    // Javascript for updating the progress bar and information
     if (is_int($r/1000)){
// 		echo '<script language="javascript">
// // 		document.getElementById("progress").innerHTML="<div style=\"width:'.$percent.';background-color:#ddd;\">&nbsp;</div>";
//  		document.getElementById("information").innerHTML="'.$r.' rows processed!";
// 		</script>';
      }
		 } 
	  echo "</tr>";
	  if ($j<=$total) {$j++;}
	  $r++;
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

// <p><input type="button" a href="javascript:;" onclick="window.close();" class="orange-flat-small" value="Close Preview"></a></p>

</body>
</html>