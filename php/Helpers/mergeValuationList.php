<?php
	require_once( "../../lib/configuration.php" );

	$valuecDsn	=	'mysql:host=localhost;dbname=lre-values';

	$dblocalplan = new db(cDsn, cUser, cPass);

	try {
 	   $dbvalue = new db($valuecDsn, cUser, cPass); // also allows an extra parameter of configuration
	} catch(PDOException $e) {
    	die('Could not connect to the database:<br/>' . $e->getMessage());
	}

	$reslocalplan = $dblocalplan->select('KML_from_LUPMIS', 'districtid = 198');
	$resvalue = $dbvalue->select('values_suhum2'); //, 'districtid = 198');

	echo 'localplan affected rows: '.count($reslocalplan).'<br>';
	echo 'values affected rows: '.count($resvalue).'<br>';
//	var_dump($reslocalplan);


?>