<?php
    set_time_limit(0);
    date_default_timezone_set('America/Caracas');
    backup_tables('localhost','root','1234','database_name');
    backup_views('localhost','root','1234','database_name');
    function backup_tables($host,$user,$pass,$name,$tables = '*'){
    	$link = mysql_connect($host,$user,$pass);
    	mysql_select_db($name,$link);
    	//get all of the tables
    	if($tables == '*'){
                $tables = array();
                $result = mysql_query('SHOW FULL TABLES WHERE TABLE_TYPE != \'VIEW\';');
                while($row = mysql_fetch_row($result)){
                    array_push($tables, $row[0]);
                }
    	}else{
                $tables = is_array($tables) ? $tables : explode(',',$tables);
    	}
    	//cycle through
            $return = "SET FOREIGN_KEY_CHECKS=0;";
            $type="tables";
            $date = date('YmdHi');
            //drop and create tables
    	foreach($tables as $table){
                $return .= 'DROP TABLE IF EXISTS '.$table.';';
                $row = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
                $return.= "\n\n".$row[1].";\n\n";
    	}
            $return .= "SET FOREIGN_KEY_CHECKS=1;";
            //save file and create gzip
            create_gzip(create_sql("dbbackup-$type $date structures.sql",$return));

            $return = "";
    	foreach($tables as $table){
                $result = mysql_query('SELECT * FROM '.$table);
                $num_fields = mysql_num_fields($result);
                for ($i = 0; $i < $num_fields; $i++){
                    while($row = mysql_fetch_row($result)){
                        $return.= 'INSERT INTO '.$table.' VALUES(';
                        for($j=0; $j<$num_fields; $j++){
                            $row[$j] = addslashes($row[$j]);
                            $row[$j] = str_replace("\n","\\n",$row[$j]);
                            if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
                            if ($jaddFile($file);
        $gzhandler->compress(Phar::GZ);
        unset($gzhandler);
        unlink($file);
        unlink($gzpath);
    }
?>