<?php
	require_once( "../lib/configuration.php"	);

	$regionid    = $_POST['region_id'];
//var_dump($_POST);
	$sql        = "SELECT * FROM area_district WHERE regionid = '".$regionid."' ORDER BY district_name";

	$conn = new PDO(cDsn, cUser, cPass);
	$stmt = $conn->prepare($sql);
	if (!$stmt->execute())
//	  echo '[' . $stmt->errorCode() . ']: ' . $stmt->errorInfo());
	  throw new Exception('[' . $stmt->errorCode() . ']: ' . $stmt->errorInfo());
	$count = $stmt->rowCount();
	$r = $stmt->fetchAll(PDO::FETCH_ASSOC);
	$i=0;
//  		echo '<label>District: ';
// 		echo '<select name="district" id="district">';
	$HTML="";
	$HTML.='<label for="district">District: ';
	$HTML.='<select name="district" id="dropdistrict">';
		for ($x = 0; $x < $count; $x++) {
		  $HTML.= 	"<option value=".$r[$x]["districtid"].">".$r[$x]["district_name"]."</option>";
//				echo "<option value='".$r[$x]["district_name"]."'></option>";
		}
// 				"<option value='".$row['id']."'>".$row['1']."</option>";
			$HTML.=" </label></select>";
   echo $HTML;

//    echo	'<option value="">Please Select</option>';
// 			for ($x = 0; $x < $count; $x++) {
// 				echo "<option value='".$r[$x]["district_name"]."'></option>";
// 			}
?>


