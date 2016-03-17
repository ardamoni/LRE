<?php
	require_once( "../../lib/configuration.php" );

	$SourceDB	= $_GET['sdb'];
	$districtid = $_GET['districtid'];

echo $SourceDB.' '.$districtid.' '.cUser.' '.cPass.'<br>';

	$SourceDsn	=	'mysql:host=localhost;dbname='.$SourceDB;
	$TargetDsn	=	'mysql:host=localhost;dbname=revenue';

$tables = array(
// 	'0' => 'KML_from_LUPMIS',
	'1' => 'business',
	'2' => 'business_balance',
	'3' => 'business_due',
	'4' => 'business_payments',
// 	'5' => 'fee_fixing_business',
// 	'6' => 'fee_fixing_property',
	'7' => 'property',
	'8' => 'property_balance',
	'9' => 'property_due',
	'10' => 'property_payments'
	);

	try {
 	   $dbSource = new db($SourceDsn, cUser, cPass); // also allows an extra parameter of configuration
	} catch(PDOException $e) {
    	die('Could not connect to the Source database:<br/>' . $e->getMessage());
	}
	try {
 	   $dbTarget = new db($TargetDsn, cUser, cPass); // also allows an extra parameter of configuration
	} catch(PDOException $e) {
    	die('Could not connect to the Target database:<br/>' . $e->getMessage());
	}


	foreach($tables as $table) {
			echo '<br><strong>'.$table.'<br></strong>';
			$totalinserted = 0;

		 	$resSourceTable = $dbSource->select($table, 'districtid = '.$districtid);
		 	$resTargetTable = $dbTarget->select($table, 'districtid = '.$districtid);
			echo 'Sourcetable: '.$table.' affected rows: '.count($resSourceTable).'<br>';
			echo 'Targettable: '.$table.' affected rows: '.count($resTargetTable).'<br>';
		//
		//
		//
			$count = $dbTarget->exec("DELETE FROM ".$table." WHERE districtid = '".$districtid."'");
			echo 'Targettable: '.$table.' deleted rows: '.$count.'<br>';

			$cols = $dbTarget->query("SHOW COLUMNS FROM ".$table);
// 		 	var_dump($cols);
			$fields = '';
			foreach ($cols as $row) {
				$fields .= $row['Field'] . ",";
// 		     var_dump($fields);
			}
//			echo 'Field list for table: '.$table.'<br>'.$fields.'<br>';
			foreach ($resSourceTable as $row){
	// 		     var_dump($row);
					foreach ($row as $fieldvalue){
	// 		     var_dump($fieldvalue);
						if (strchr($fieldvalue,"''")){
						$fieldvalue = substr($fieldvalue,0,strpos($fieldvalue,"''"))."-".substr($fieldvalue,strpos($fieldvalue,"''")+2);
						}
						if (strchr($fieldvalue,'"')){
 							$fieldvalue = substr($fieldvalue,0,strpos($fieldvalue,'"'))."-".substr($fieldvalue,strpos($fieldvalue,'"')+1);
						}
						if (strpos($fieldvalue,"\\") > 0){
							$fieldvalue = substr($fieldvalue,0,strpos($fieldvalue,"\\")-1)."-".substr($fieldvalue,strpos($fieldvalue,"\\")+1);
						}
						if (strchr($fieldvalue,"'")){
							if (strpos($fieldvalue,"'") == 0){
							$fieldvalue = "\\".substr($fieldvalue,strpos($fieldvalue,"'"));
							} else {
							$fieldvalue = substr($fieldvalue,0,strpos($fieldvalue,"'")-1)."\\".substr($fieldvalue,strpos($fieldvalue,"'"));
							}
						}
						$values .= "'".$fieldvalue . "',";
					}

				$fieldslist = substr($fields,0,strlen($fields)-1);
				$valueslist = substr($values,0,strlen($values)-1);
				$valueslist = substr($valueslist,strpos($valueslist,',')+1);
				$valueslist = 'NULL,'.$valueslist;

				$countinserted = $dbTarget->run("INSERT INTO ".$table." (".$fieldslist.") VALUES (".$valueslist.");");
				if ($countinserted == 0) {echo '<strong>'.$valueslist.'</strong> <br>';}

				$totalinserted = $totalinserted + $countinserted;
				$values = '';
			}
 			echo $totalinserted." inserted INTO ".$table."<br>";

	}
// 	var_dump($resSourceTable);


?>