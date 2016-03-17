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
  	$username = $_POST['username'];
  	$pass = $_POST['pass'];
  	$title = $_POST['title'];
  	$fullname = $_POST['fullname'];
  	$position = $_POST['position'];
  	$email = $_POST['email'];
  	$phone = $_POST['phone'];
  	$baselanguage = $_POST['baselanguage'];
  	$activestatus = $_POST['activestatus'];
  	$loged = $_POST['loged'];
  	if (!strpos($_POST['districtid'],'/ ')){
  	  	$districtid = $_POST['districtid'];
	}else{
	  	$districtid = substr($_POST['districtid'],strpos($_POST['districtid'],'/ ')+2);
  	}
  	if (!strpos($_POST['regionid'],'/ ')){
  	  	$regionid = $_POST['regionid'];
	}else{
	  	$regionid = substr($_POST['regionid'],strpos($_POST['regionid'],'/ ')+2);
  	}
	$roleid = $_POST['roleid'];

  if ($dbaction=='getSysteminfo'){getSysteminfo($id);}

  if ($dbaction=='getUserinfo'){getUserinfo($id);}

  if ($dbaction=='updateSyscon'){updateSyscon($id, $variable, $value);}

  if ($dbaction=='updateUser'){updateUser($id, $username, $pass, $title, $fullname, $position, $email, $phone, $baselanguage, $activestatus, $loged, $districtid, $regionid, $roleid);}

  if ($dbaction=='insertUser'){insertUser($id, $username, $pass, $title, $fullname, $position, $email, $phone, $baselanguage, $activestatus, $loged, $districtid, $regionid, $roleid);}

//var_dump($_POST);
//debug_to_console( $_POST );
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
       				t1.`phone`, t1.`baselanguage`, t1.`activestatus`, t1.`pass`, t1.`loged`, t5.`roleid`
       				FROM `usr_users` t1
       				inner join `usr_user_district` t2 on t1.`username`=t2.`username`
       					inner join `area_district` t3 on t2.`districtid`=t3.`districtid`
       					inner join `area_region` t4 on t3.`regionid`=t4.`regionid`
       					inner join `usr_user_role` t5 on t1.`username`=t5.`username`)
       				UNION
					(SELECT t5.`id`, t6.`regionid`, 'n.a', t6.`districtid`, 'n.a',  t5.`username`, t5.`name`, t5.`title`, t5.`position`, t5.`email`,
       				t5.`phone`, t5.`baselanguage`, t5.`activestatus`, t5.`pass`, t5.`loged`, t7.`roleid`
       				FROM `usr_users` t5
       				inner join `usr_user_district` t6 on t5.`username`=t6.`username`
       				inner join `usr_user_role` t7 on t5.`username`=t7.`username`
       				WHERE t7.`roleid`=200)
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
		$json['roleid']				= $results[$id-1]['roleid'];
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

function updateUser($id, $username, $pass, $title, $fullname, $position, $email, $phone, $baselanguage, $activestatus, $loged, $districtid, $regionid, $roleid)
{

 		$returnresult = 0;
 		$conndb = new db(cDsn, cUser, cPass);

		$stmt = $conndb->prepare(" SELECT t1.`id`, t3.`districtid`, t3.`district_name`,  t1.`username`, t1.`name`, t1.`title`, t1.`position`, t1.`email`,
       				t1.`phone`, t1.`baselanguage`, t1.`activestatus`, t1.`pass`, t1.`loged`
       				FROM `usr_users` t1
       				inner join `usr_user_district` t2 on t1.`username`=t2.`username`
       					inner join `area_district` t3 on t2.`districtid`=t3.`districtid`
								WHERE 	t1.`username` = :username AND t3.`districtid` = :districtid");

	if (!$stmt->execute(array('username' => $username, 'districtid' => $districtid)))
		  throw new Exception('[' . $stmt->errorCode() . ']: ' . $stmt->errorInfo());
// 		$stmt = $conn->prepare(" SELECT * FROM `system_config`");
// 		$stmt->execute();
 		$results = $stmt->fetchAll(PDO::FETCH_BOTH);

 		if ($results[0]['pass']!=$pass){
 			$pass = md5(mysql_real_escape_string(htmlentities($pass)));
 		}else {
 			$pass = $pass;}

 		$rowid = $results[0]['id'];

		$update = array(
		'username' => $username,
		'pass' => $pass,
		'title' => $title,
		'name' => $fullname,
		'position' => $position,
		'email' => $email,
		'phone' => $phone,
		'baselanguage' => $baselanguage,
		'activestatus' => $activestatus,
		'loged' => $loged,
		'districtid' => $districtid,
		'regionid' => $regionid
		);

		$bind = array(
			":username" => $username,
			":id"		=> $results[0]['id']
		);
// ,
// 			":districtid" => $districtid

 		$result = $conndb->update("usr_users", $update, "username = :username AND id = :id", $bind);
		$returnresult = $returnresult+$result;
//now update the usr_user_district table

		$update = array(
		'username' => $username,
		'districtid' => $districtid,
		'regionid' => $regionid
		);

		$bind = array(
			":username" => $username
		);

 		$result = $conndb->update("usr_user_district", $update, "username = :username", $bind);
		$returnresult = $returnresult+$result;
 		$result = $conndb->update("usr_user_region", $update, "username = :username", $bind);
		$returnresult = $returnresult+$result;

//now update the usr_user_role

		$update = array(
		'username' => $username,
		'roleid' => $roleid
		);

		$bind = array(
			":username" => $username
		);

 		$result = $conndb->update("usr_user_role", $update, "username = :username", $bind);
		$returnresult = $returnresult+$result;

		if ($returnresult>0){
			$returnresult = 1;}
		else {
			$returnresult = $returnresult;
		}

		$json['username']			= $username;
		$json['pass'] 				= $pass;
		$json['title'] 				= $title;
		$json['fullname'] 			= $fullname;
		$json['position'] 			= $position;
		$json['email'] 				= $email;
		$json['phone'] 				= $phone;
		$json['baselanguage'] 		= $baselanguage;
		$json['activestatus'] 		= $activestatus;
		$json['loged'] 				= $loged;
		$json['districtid'] 		= $districtid;
		$json['regionid'] 			= $regionid;
		$json['roleid'] 			= $roleid;
		$json['id']					= $id;
		$json['rowid']				= $rowid;
		$json['rowsaffected']		= $result;

		$data[] 					= $json;

// debug_to_console( "Test:".$id.' '.$json['returnval'] );

	header("Content-type: application/json");
	echo json_encode($data);
}

function insertUser($id, $username, $pass, $title, $fullname, $position, $email, $phone, $baselanguage, $activestatus, $loged, $districtid, $regionid, $roleid)
{
 		$returnresult = 0;

 		$conndb = new db(cDsn, cUser, cPass);

		$stmt = $conndb->prepare(" SELECT t1.`id`, t3.`districtid`, t3.`district_name`,  t1.`username`, t1.`name`, t1.`title`, t1.`position`, t1.`email`,
       				t1.`phone`, t1.`baselanguage`, t1.`activestatus`, t1.`pass`, t1.`loged`
       				FROM `usr_users` t1
       				inner join `usr_user_district` t2 on t1.`username`=t2.`username`
       					inner join `area_district` t3 on t2.`districtid`=t3.`districtid`
								WHERE 	t1.`username` = :username AND t3.`districtid` = :districtid");

	if (!$stmt->execute(array('username' => $username, 'districtid' => $districtid)))
		  throw new Exception('[' . $stmt->errorCode() . ']: ' . $stmt->errorInfo());
// 		$stmt = $conn->prepare(" SELECT * FROM `system_config`");
// 		$stmt->execute();
		$count = $stmt->rowCount();
		if ($count==0){
			$pass = md5(mysql_real_escape_string(htmlentities($pass)));

			$insert = array(
			'username' => $username,
			'pass' => $pass,
			'title' => $title,
			'name' => $fullname,
			'position' => $position,
			'email' => $email,
			'phone' => $phone,
			'baselanguage' => $baselanguage,
			'activestatus' => $activestatus,
			'loged' => $loged,
			'districtid' => $districtid,
			'regionid' => $regionid,
			'roleid' => $roleid
			);

			$result = $conndb->insert("usr_users", $insert);
			$returnresult = $returnresult+$result;
			$result = $conndb->insert("usr_user_district", $insert);
			$returnresult = $returnresult+$result;
			$result = $conndb->insert("usr_user_region", $insert);
			$returnresult = $returnresult+$result;
			$result = $conndb->insert("usr_user_role", $insert);
			$returnresult = $returnresult+$result;
		}

		if ($returnresult>0){
			$returnresult = 1;}
		else {
			$returnresult = $returnresult;
		}

		$json['username']			= $username;
		$json['pass'] 				= $pass;
		$json['title'] 				= $title;
		$json['fullname'] 			= $fullname;
		$json['position'] 			= $position;
		$json['email'] 				= $email;
		$json['phone'] 				= $phone;
		$json['baselanguage'] 		= $baselanguage;
		$json['activestatus'] 		= $activestatus;
		$json['loged'] 				= $loged;
		$json['districtid'] 		= $districtid;
		$json['regionid'] 			= $regionid;
		$json['id']					= $id;
		$json['rowid']				= $rowid;
		$json['rowsaffected']		= $result;

		$data[] 					= $json;

// debug_to_console( "Test:".$id.' '.$json['returnval'] );

	header("Content-type: application/json");
	echo json_encode($data);
}

//this is a helper function to get some info to be displayed within the console log
function debug_to_console( $data ) {

    if ( is_array( $data ) )
        $output = "<script>console.log( 'Debug Objects: " . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";

    echo $output;
}

?>