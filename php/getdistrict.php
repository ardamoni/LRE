<?php
	require_once( "../lib/configuration.php"	);

	$district    = $_GET['district'];
//var_dump($_GET);
	$sql        = "SELECT * FROM area_district WHERE district_name LIKE '$district%' ORDER BY district_name";

	$conn = new PDO(cDsn, cUser, cPass);
	$stmt = $conn->prepare($sql);
	if (!$stmt->execute())
//	  echo '[' . $stmt->errorCode() . ']: ' . $stmt->errorInfo());
	  throw new Exception('[' . $stmt->errorCode() . ']: ' . $stmt->errorInfo());
	$count = $stmt->rowCount();
//echo $count;
//
// //	$res        = $pdo->select($sql);
	$r = $stmt->fetchAll(PDO::FETCH_ASSOC);
//	$count = $stmt->rowCount();
//var_dump($r);
	$i=0;
// 	if($count)
// 		echo $pdo->setErrorCallbackFunction("myErrorHandler ".$res); //mysqli_error($db);
// 	else
// 		while( $r = $stmt->fetchAll(PDO::FETCH_ASSOC))
//			while ($i < $count) {
			for ($x = 0; $x < $count; $x++) {
//				echo 'option value inside loop '.$x.' '.$count.$r[$x]["district_name"];
				echo "<option value='".$r[$x]["district_name"]."'>";
			}
//
//			echo ;
?>

</option>