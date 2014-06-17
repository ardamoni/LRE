<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Table view</title>
<link rel="stylesheet" href="css/ex.css" type="text/css" />
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

<?php
	// DB connection
	require_once( "../lib/configuration.php"	);
	$statement = $pdo->query("SELECT * from property limit 10");
//	$row = $statement->fetch(PDO::FETCH_ASSOC);
echo htmlentities($row['_message']);

session_start();
	// match UPN
//	$query = mysql_query( "SELECT * FROM property");	
	

	echo "<table class='demoTbl' border='1' cellpadding='10' cellspacing='1' bgcolor='#FFFFFF'>
			<tr'>";
for ($i = 1; $i < $statement->columnCount(); $i++) {
    $col = $statement->getColumnMeta($i);
    echo '<th>'.$col['name'].'</th>';
}
	echo '</tr>';

//mysql_fetch_array( $query,MYSQLI_NUM ) ) 
	while( $row = $statement->fetch(PDO::FETCH_BOTH)) 
	{
	 echo "<tr>";
	 for ($x=1; $x<=$statement->columnCount(); $x++)
  		{
		  echo "<td>" . $row[$x] . "</td>";
		 } 
	  echo "</tr>";
	  }
	
   echo "</table>";
?>

<p>Back to <a href="index.html">Index</a></p>

</body>
</html>