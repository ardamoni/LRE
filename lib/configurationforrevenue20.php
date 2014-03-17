<?php

/*
	This file is used for connecting to DB only !!!
*/

	// details of the connection
	$host		=	"localhost";
	$user		=	"root"; 
	$pass		=	"root"; 
	$db 		=	"revenue20"; // revenue 
	
	
	// Connect to the Server
	$con = mysql_connect($host, $user, $pass);
	// test the connection
	if (mysqli_connect_errno($con))
	{
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}

	// Connect to the DB
	$conDB = mysql_select_db($db) or die("cannot select DB"); 
	
	
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