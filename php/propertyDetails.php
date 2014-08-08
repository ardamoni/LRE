<?php
//    require_once("../lib/initialize.php");

error_reporting(E_ALL);
set_time_limit(0);
ob_start(); // prevent adding duplicate data with refresh (F5)
session_start();

date_default_timezone_set('Europe/London');

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Update Details Information</title>
<link rel="stylesheet" href="css/ex.css" type="text/css" />
</head>
<body>

<h1>Form Submission Result</h1>

<?php
$upn=$_POST['upn'];
$subupn=$_POST['subupn'];

echo "<pre>";
var_dump($_POST);
echo "</pre>";
?>

<p>&nbsp;</p>
</body>
</html>