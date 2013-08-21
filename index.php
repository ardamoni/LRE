<?php
	
	// STARTING PAGE

	/*
	 *	Set The Flag that This is the Parent File
	 *	-----------------------------------------------------------------------
	 */ 
	define( 'VALID_REVENUE', 1 );
		
	/*
	 *	Include the Library Code
	 *	-----------------------------------------------------------------------
	 */
	require_once("lib/configuration.php");			// Configuration File
	require_once("lib/user.php");					// User Functions

	
	
	$User = new User;
	


	/*
	 *	Start the Session
	 *	-----------------------------------------------------------------------
	 */
	session_start();
	
	
		
	 
	/*
	 *	Login Function
	 *	-----------------------------------------------------------------------
	 */
	if( isset( $_POST['user'] ) )		
		$User->UserLogin($_POST['user'], $_POST['pass']);
	

	/*
	 *	Header File
	 *	-----------------------------------------------------------------------
	 */ 
	//include("header.php");


	/*
	 *	Main Content DIV
	 *	-----------------------------------------------------------------------
	 */	
	echo "<div id = 'sysContent'><div style = 'height: 500px;'>";		

		/*
		 *	Login Form File
		 *	-----------------------------------------------------------------------
		 */
		echo "<div id = 'system-login'>  <h1 id = 'title1'> " 
			 . "Login Form"
			 . "</h1>";
		include("login.php");
		echo "</div>";		
		
	echo "</div>";
	
		
	/*
	 *	Footer File
	 *	-----------------------------------------------------------------------
	 */
	//include("footer.php");
	


?>