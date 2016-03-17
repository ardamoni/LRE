<?php
   $dbhost = 'localhost:3036';
   $dbuser = 'root';
   $dbpass = 'root';

   $conn = mysql_connect($dbhost, $dbuser, $dbpass);

$districtid = $_GET['districtid'];

   if(! $conn )
   {
      die('Could not connect: ' . mysql_error());
   }

   $table_name = "property";
   $backup_file  = "./property".time().".sql";
   $sql = "SELECT * INTO OUTFILE '$backup_file' FROM $table_name WHERE districtid = $districtid";

   echo $sql;

   mysql_select_db('revenue');
   $retval = mysql_query( $sql, $conn );

   if(! $retval )
   {
      die('Could not take data backup: ' . mysql_error());
   }

   echo "Backedup $backup_file data successfully\n";

   mysql_close($conn);
?>
