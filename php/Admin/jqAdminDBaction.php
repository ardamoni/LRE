<?php
//    require_once("../lib/initialize.php");

error_reporting(E_ALL);
set_time_limit(0);
ob_start(); // prevent adding duplicate data with refresh (F5)
session_start();

date_default_timezone_set('Europe/London');

require_once( "../../lib/configuration.php"	);

	$dbaction = $_POST['dbaction'];
	$id = $_POST['id'];
	$variable = $_POST['variable'];
	$value = $_POST['value'];

  if ($dbaction=='getSysteminfo'){getSysteminfo($id);}

  if ($dbaction=='getUserinfo'){getUserinfo($id);}

  if ($dbaction=='updateSyscon'){updateSyscon($id, $variable, $value);}

//var_dump($_POST);
//----------end of loader -------------------------------------------------------------------



function getSysteminfo($id)
{

		$bind = array(
			":id" => $id
		);

		$conn = new PDO(cDsn, cUser, cPass);
		$stmt = $conn->prepare(" SELECT * FROM `system_config`");
		$stmt->execute();
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

//   	$results = $conn->query('SELECT * FROM `system_config`');
//		$results = $pdo->select('system_config');

 		$json['variable']			= $results[$id-1]['variable'];
		$json['value']				= $results[$id-1]['value'];
		$json['id']					= $id;

		$data[] 					= $json;

// debug_to_console( "Test:".$id.' '.$json['returnval'] );

	header("Content-type: application/json");
	echo json_encode($data);
}

function getUserinfo($id)
{

		$bind = array(
			":id" => $id
		);

		$conn = new PDO(cDsn, cUser, cPass);
		$stmt = $conn->prepare(" (SELECT t1.`id`, t4.`regionid`, t4.`region_name`, t3.`districtid`, t3.`district_name`,  t1.`username`, t1.`name`, t1.`title`, t1.`position`, t1.`email`,
       				t1.`phone`, t1.`baselanguage`, t1.`activestatus`, t1.`pass`, t1.`loged`
       				FROM `usr_users` t1
       				inner join `usr_user_district` t2 on t1.`username`=t2.`username`
       					inner join `area_district` t3 on t2.`districtid`=t3.`districtid`
       					inner join `area_region` t4 on t3.`regionid`=t4.`regionid`)
       				UNION
					(SELECT t5.`id`, t6.`regionid`, 'n.a', t6.`districtid`, 'n.a',  t5.`username`, t5.`name`, t5.`title`, t5.`position`, t5.`email`,
       				t5.`phone`, t5.`baselanguage`, t5.`activestatus`, t5.`pass`, t5.`loged`
       				FROM `usr_users` t5
       				inner join `usr_user_district` t6 on t5.`username`=t6.`username`
       				WHERE t6.`districtid`>900)
       				order by `regionid`, `districtid`, `username`;");
		$stmt->execute();
		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
// $results = $pdo->select('usr_users INNER JOIN usr_user_district ON usr_users.username = usr_user_district.username
// 								INNER JOIN area_district ON usr_user_district.districtid = area_district.districtid
// 								INNER JOIN area_region ON area_district.regionid = area_region.regionid', 'usr_users.id = :id', $bind);


 		$json['username']			= $results[$id-1]['username'];
		$json['pass']				= $results[$id-1]['pass'];
		$json['title']				= $results[$id-1]['title'];
		$json['fullname']			= $results[$id-1]['name'];
		$json['position']			= $results[$id-1]['position'];
		$json['email']				= $results[$id-1]['email'];
		$json['phone']				= $results[$id-1]['phone'];
		$json['baselanguage']		= $results[$id-1]['baselanguage'];
		$json['activestatus']		= $results[$id-1]['activestatus'];
		$json['loged']				= $results[$id-1]['loged'];
		$json['districtid']			= $results[$id-1]['districtid'];
		$json['regionid']			= $results[$id-1]['regionid'];
		$json['districtname']		= $results[$id-1]['district_name'];
		$json['regionname']			= $results[$id-1]['region_name'];
		$json['id']					= $id;

		$data[] 					= $json;

// debug_to_console( "Test:".$id.' '.$json['returnval'] );

	header("Content-type: application/json");
	echo json_encode($data);
}

function updateSyscon($id, $variable, $value)
{

		$update = array(
			'variable' => $variable,
			'value' => $value
		);

		$bind = array(
			":variable" => $variable
		);

 		$conndb = new db(cDsn, cUser, cPass);
// 		$stmt = $conn->prepare(" SELECT * FROM `system_config`");
// 		$stmt->execute();
// 		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

 		$result = $conndb->update("system_config", $update, "variable = :variable", $bind);

//   	$results = $conn->query('SELECT * FROM `system_config`');
//		$results = $pdo->select('system_config');

 		$json['variable']			= $variable;
		$json['value']				= $value;
		$json['id']					= $id;
		$json['rowsaffected']		= $results;

		$data[] 					= $json;

// debug_to_console( "Test:".$id.' '.$json['returnval'] );

	header("Content-type: application/json");
	echo json_encode($data);
}



?>