<?php
require_once("class.db.php");
$version = "1.0.2";
$released = "December 31, 2014";

global $pdo;
/*
	This file is used for connecting to DB only !!!
*/

	// details of the connection
	const cHost		=	"localhost";
	const cUser	=	"root";
	const cPass	=	"root";
	const cDsn	=	'mysql:host=localhost;dbname=revenue'; //dsn = Data Source Name
	const cDb 		=	"revenue"; // revenue

try {
    $pdo = new db(cDsn, cUser, cPass); // also allows an extra parameter of configuration
} catch(PDOException $e) {
    die('Could not connect to the database:<br/>' . $e->getMessage());
}
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	// Connect to the Server
	$con = mysql_connect(cHost, cUser, cPass);
	$consqli = mysqli_connect(cHost, cUser, cPass, cDb);
	// test the connection
	if (mysqli_connect_errno($con))
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	// Connect to the DB
	$conDB = mysql_select_db(cDb) or die("cannot select DB");


	/*
	 *	Error Handling
	 */
	//ini_set('error_reporting', E_ALL);		// error handling in development environment.
	ini_set('error_reporting', 0);				// error handling in production environment

	//session_set_cookie_params(86400);
	session_cache_expire(86400);				// minutes

	//$cache_expire = session_cache_expire();
	set_time_limit(600);  						// seconds
?>