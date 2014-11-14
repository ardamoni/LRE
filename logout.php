<?php

	require_once("lib/configuration.php");

	/*
	 *	Start the Session
	 */	
	session_start();

	// close the log
	// mysql_query("UPDATE `usr_users` SET `loged` = '0' WHERE `username` = '".$_SESSION['user']['user']."'");			
			

	
	/*
	 *	If the user is logged in, unset the session
	 */ 
	if ( isset( $_SESSION['sys']['login'] ) )
   		 unset( $_SESSION['sys']['login'] ) ;

	// destroy the session
	session_destroy();		
	
	/*
	 *	The user is logged out, go to Login Page
	 */ 
	header('Location: index.php');
	exit;

?>

