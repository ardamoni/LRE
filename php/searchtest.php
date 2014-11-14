<?php
	// DB connection
		require_once( "../lib/configuration.php"	);

	$districtid=$_POST['districtid'];
	$starget = $_POST['starget'];
	$sString = $_POST['sString'];
	$searchlayer = $_POST['searchlayer'];
	
// var_dump($_POST);

//searchOther($districtid, $starget, $sString, $searchlayer);

// function searchOther($districtid, $starget, $sString, $searchlayer) 
// {

//debug_to_console( "Test:".$starget.' '.$sString.' '.$searchlayer );

  if($searchlayer=='property'){
   if ($starget=='owner'){
	$run = "SELECT * FROM property WHERE districtid = '".$districtid."' AND `owner` LIKE '%".$sString."%';";
	}
   if ($starget=='street'){
	$run = "SELECT * FROM property WHERE districtid = '".$districtid."' AND `streetname` LIKE '%".$sString."%';";
	}
  }
  if($searchlayer=='business'){
   if ($starget=='owner'){
	$run = "SELECT * FROM business WHERE districtid = '".$districtid."' AND `owner` LIKE '%".$sString."%';";
	}
   if ($starget=='street'){
	$run = "SELECT * FROM business WHERE districtid = '".$districtid."' AND `streetname` LIKE '%".$sString."%';";
	}
  }
	$query = mysql_query($run);  
	$data 				= array();
	
//   if (!empty($query)){
	$json 				= array();
		while ($row = mysql_fetch_assoc($query)) {
			$json['upn'] 				= $row['upn'];
			$data[] 					= $json;
		}
	
//	 }//end if
//	}//end else 
	header("Content-type: application/json");
	echo json_encode($data);
// }
?>