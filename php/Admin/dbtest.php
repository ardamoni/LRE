<?php
require_once( "../../lib/configuration.php"	);

$conndb = new db(cDsn, cUser, cPass);
$username = 'techiman';
		$stmt = $conndb->prepare(" SELECT t1.`id`, t3.`districtid`, t3.`district_name`,  t1.`username`, t1.`name`, t1.`title`, t1.`position`, t1.`email`,
       				t1.`phone`, t1.`baselanguage`, t1.`activestatus`, t1.`pass`, t1.`loged`
       				FROM `usr_users` t1
       				inner join `usr_user_district` t2 on t1.`username`=t2.`username`
       					inner join `area_district` t3 on t2.`districtid`=t3.`districtid`
								WHERE 	t1.`username` = :username");
//
	if (!$stmt->execute(array('username' => $username)))
		  throw new Exception('[' . $stmt->errorCode() . ']: ' . $stmt->errorInfo());
// 		$stmt = $conndb->prepare(" SELECT * FROM `system_config`");
// 		$stmt->execute();
 		$results = $stmt->fetchAll(PDO::FETCH_BOTH);

 		$rowid = $results[0]['id'];

 		echo '<br>'.$rowid;

 		var_dump($results);
?>