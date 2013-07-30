<?php
//$dataFromJS=$_POST['clickfeature'];
//$clickupn=$_GET['clickfeature'];
//$clickupn='574-0600-1620';
$username = "root";
$password = "root";
$database = "LUPMIS";
$url = "localhost";
//echo $clickupn.": clickupn<br>".$upn.": upn<br>".$dataFromJS.": data<br>";
$con = mysql_connect($url, $username , $password);
if (!$con)
{
die('Could not connect:' . mysql_error());
}

mysql_select_db($database, $con) ;
$data = array();
// query your DataBase here looking for a match to $input
$run = "SELECT DISTINCT d1.UPN, d1.boundary, d1.id, d2.pay_status from `KML_from_LUPMIS` d1, property d2 WHERE d1.`UPN` = d2.`upn`;";
$query = mysql_query($run);
//echo $query.": query<br>";
while ($row = mysql_fetch_assoc($query)) {
$json = array();
$json['id'] = $row['id'];
$json['upn'] = $row['UPN'];
$json['boundary'] = $row['boundary'];
$json['status'] = $row['pay_status'];
$data[] = $json;
//echo $row["upn"];
}
header("Content-type: application/json");
echo json_encode($data);
mysql_close($con);
?>