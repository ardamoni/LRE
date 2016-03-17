 <?php
//You should test your script, once confirmed all working, add '@' in front of all 'mysqli' commands to stop any errors being displayed.
require_once( "../../lib/configuration.php");
//
//Script Variables
$compression = false;
$BACKUP_PATH = "../../tmp/";

echo $BACKUP_PATH;
//
//I have this in a file elsewhere, makes it easier to keep consistent across pages.
$user = "";
$pass = "";
$host = "localhost";
$db   = "";
$conn = mysqli_connect(cHost,cUser,cPass,cDb);
//
$tables = array(
	'0' => 'KML_from_LUPMIS',
	'1' => 'business',
	'2' => 'business_balance',
	'3' => 'business_due',
	'4' => 'business_payments',
	'5' => 'fee_fixing_business',
	'6' => 'fee_fixing_property',
	'7' => 'property',
	'8' => 'property_balance',
	'9' => 'property_due',
	'10' => 'property_payments'
	);

$usr_tables = array(
	'11' => 'usr_user_district',
	'12' => 'usr_user_region',
	'13' => 'usr_user_role',
	'14' => 'usr_users',
	'15' => 'usr_user_accesslog'
	);

$districtid = $_GET['districtid'];

backup_tables($conn, $tables, $districtid);

backup_usr_tables($conn, $usr_tables, $districtid);

/* backup the whole db by default ('*') OR a single table ('tableName') */
function backup_tables($conn,$tables = '*', $districtid) {
$BACKUP_PATH = "../../tmp/".$districtid."_";
	//get all of the tables
	if($tables == '*') {
		$tables = array();
		$result = mysqli_query($conn,'SHOW TABLES');
		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
			$tables[] = $row[0];
		}
	} else {
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}
//
// var_dump($tables);
	//cycle through data
	$return = "";
	foreach($tables as $table) {
echo '<br>'.$table;
		$result = mysqli_query($conn,"SELECT * FROM `".$table."` WHERE `districtid`='".$districtid."'");
		$num_fields = mysqli_num_fields($result);
//
		$return.= 'DROP TABLE IF EXISTS '.$districtid.'_'.$table.';';
		$row2 = mysqli_fetch_row(mysqli_query($conn,'SHOW CREATE TABLE '.$table));
		$createtable1 = substr($row2[1],0,strpos($row2[1],$table)-1);
		$createtable2 = substr($row2[1],strpos($row2[1],$table)+strlen($table)+1,strlen($row2[1]));
		$createtable3 = $createtable1.'`'.$districtid.'_'.$table.'`'.$createtable2;
		$return.= "\n\n".$createtable3.";\n\n";
//		$return.= "\n\n".$row2[1].";\n\n";
//
		$return.= 'INSERT INTO '.$districtid.'_'.$table." (";
		$cols = mysqli_query($conn,"SHOW COLUMNS FROM ".$table);
		$count = 0;
		while ($rows = mysqli_fetch_array($cols, MYSQLI_NUM)) {
			$return.= $rows[0];
			$count++;
			if ($count < mysqli_num_rows($cols)) {
				$return.= ",";
			}
		}
		$return.= ")".' VALUES';
		for ($i = 0; $i < $num_fields; $i++) {
			$count = 0;
			while($row = mysqli_fetch_row($result)) {
				$return.= "\n\t(";
				for($j=0; $j<$num_fields; $j++) {
					$row[$j] = addslashes($row[$j]);
					//$row[$j] = preg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) {
						$return.= '"'.$row[$j].'"' ;
					} else {
						$return.= '""';
					}
					if ($j<($num_fields-1)) {
						$return.= ',';
					}
				}
				$count++;
				if ($count < mysqli_num_rows($result)) {
					$return.= "),";
				} else {
				$return.= ");";
				}
			}
		}
		$return.="\n\n\n";
	}
//
	//save file
	if ($compression) {
		$zp = gzopen($BACKUP_PATH . 'db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql.gz', "w9");
		gzwrite($zp, $return);
		gzclose($zp);
	} else {
	echo '<br> in no compression';
		$handle = fopen($BACKUP_PATH . 'db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
		fwrite($handle,$return);
		fclose($handle);
	}
}

function backup_usr_tables($conn,$usr_tables = '*', $districtid) {
$BACKUP_PATH = "../../tmp/".$districtid."_";
	//get all of the tables
	if($usr_tables == '*') {
		$usr_tables = array();
		$result = mysqli_query($conn,'SHOW TABLES');
		while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
			$usr_tables[] = $row[0];
		}
	} else {
		$usr_tables = is_array($usr_tables) ? $usr_tables : explode(',',$usr_tables);
	}
//
// var_dump($usr_tables);
	//cycle through data
	$return = "";
	$usr_name = "ekke";
	$usr_names = array();

			$result = mysqli_query($conn,"SELECT * FROM `usr_user_district` WHERE `districtid`='".$districtid."'");
			while ($usr_row = mysqli_fetch_assoc($result)) {
				$usr_names[] = $usr_row['username'];
				}
var_dump($usr_names);

	foreach($usr_tables as $table) {
echo '<br>'.$table;

		if ($table == 'usr_user_district') {
			$result = mysqli_query($conn,"SELECT * FROM `".$table."` WHERE `districtid`='".$districtid."'");
		} else {
			$result = mysqli_query($conn,"SELECT * FROM `".$table."` WHERE `username`='".$usr_name."'");
		}
// var_dump($usr_row);

//	$usr_names = $usr_row['username'];

// echo '<br>'.$usr_name;


		$num_fields = mysqli_num_fields($result);
//
		$return.= 'DROP TABLE IF EXISTS '.$districtid.'_'.$table.';';
		$row2 = mysqli_fetch_row(mysqli_query($conn,'SHOW CREATE TABLE '.$table));
		$createtable1 = substr($row2[1],0,strpos($row2[1],$table)-1);
		$createtable2 = substr($row2[1],strpos($row2[1],$table)+strlen($table)+1,strlen($row2[1]));
		$createtable3 = $createtable1.'`'.$districtid.'_'.$table.'`'.$createtable2;
		$return.= "\n\n".$createtable3.";\n\n";
//		$return.= "\n\n".$row2[1].";\n\n";
//
		foreach($usr_names as $usr_name) {
		$return.= 'INSERT INTO '.$districtid.'_'.$table." (";
		$cols = mysqli_query($conn,"SHOW COLUMNS FROM ".$table);
		$count = 0;
		while ($rows = mysqli_fetch_array($cols, MYSQLI_NUM)) {
			$return.= $rows[0];
			$count++;
			if ($count < mysqli_num_rows($cols)) {
				$return.= ",";
			}
		}
		$return.= ")".' VALUES';
		for ($i = 0; $i < $num_fields; $i++) {
			$count = 0;
			while($row = mysqli_fetch_row($result)) {
				$return.= "\n\t(";
				for($j=0; $j<$num_fields; $j++) {
					$row[$j] = addslashes($row[$j]);
					//$row[$j] = preg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) {
						$return.= '"'.$row[$j].'"' ;
					} else {
						$return.= '""';
					}
					if ($j<($num_fields-1)) {
						$return.= ',';
					}
				}
				$count++;
				if ($count < mysqli_num_rows($result)) {
					$return.= "),";
				} else {
				$return.= ");";
				}
			}
		}
		$return.="\n\n\n";
	}
	}
//
	//save file
	if ($compression) {
		$zp = gzopen($BACKUP_PATH . 'db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql.gz', "w9");
		gzwrite($zp, $return);
		gzclose($zp);
	} else {
	echo '<br> in no compression';
	echo $BACKUP_PATH . 'db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql';
		$handle = fopen($BACKUP_PATH . 'db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
		fwrite($handle,$return);
		fclose($handle);
	}
}

?>