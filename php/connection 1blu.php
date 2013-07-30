<?php
$dataFromJS=$_POST['clickfeature'];
//$clickupn=$_GET['clickfeature'];
//$clickupn='574-0600-1620';
$username = "s5348_1908682";
$password = "12lupmis34!";
$database = "db5348x1908682";
$url = "mysql12.1blu.de";
if ($_POST['sub'] == "true") {
	$upn = strstr($dataFromJS,'UPN: ');
	$upn=substr($upn,9,13);
	}else{
	$upn=$dataFromJS;
	}
//echo $clickupn.": clickupn<br>".$upn.": upn<br>".$dataFromJS.": data<br>";
$con = mysql_connect($url, $username , $password);
if (!$con)
{
die('Could not connect:' . mysql_error());
}

mysql_select_db($database, $con) ;
$data = array();
// query your DataBase here looking for a match to $input
$query = mysql_query("SELECT * FROM property WHERE upn='".$upn."'");
//echo $query.": query<br>";
while ($row = mysql_fetch_assoc($query)) {
$json = array();
$json['id'] = $row['id'];
$json['upn'] = $row['upn'];
$json['subupn'] = $row['subupn'];
$json['pay_status'] = $row['pay_status'];
$json['revenue_due'] = $row['revenue_due'];
$json['revenue_collected'] = $row['revenue_collected'];
$json['revenue_balance'] = $row['revenue_balance'];
$json['streetname'] = $row['streetname'];
$json['housenumber'] = $row['housenumber'];
$json['owner'] = $row['owner'];
$json['owneraddress'] = $row['owneraddress'];
$json['owner_tel'] = $row['owner_tel'];
$json['owner_email'] = $row['owner_email'];
$data[] = $json;
//echo $row["upn"];
}
header("Content-type: application/json");
echo json_encode($data);
//echo "ID: ".$json['id']."<br>UPN: ".$json['upn']."<br>Revenue Balance: ".$json['revenue_balance'];
//"From db: ".$json['id']."<br>UPN: ".$json['upn']."<br>Balance: ".$json['revenue_balance'];
mysql_close($con);
?>