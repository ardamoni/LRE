<?php

	// STARTING PAGE
	/*
	 *	Start the Session
	 */
	session_start();
	/*
	 *	Set The Flag that This is the Parent File
	 *	-----------------------------------------------------------------------
	 */
	define( 'VALID_REVENUE', 1 );


	/*
	 *	Include the Library Code
	 *	-----------------------------------------------------------------------
	 */
    require_once("lib/initialize.php");

	$User = new User;

	/*
	 *	Login Function
	 */
	if( isset( $_POST['user'] ) )
		$User->UserLogin($_POST['user'], $_POST['pass']);

	/*
	 *	Header File
	 */
	//include("header.php");


	/*
	 *	Main Content DIV
	 */
	echo "<title>Local Revenue for Districts</title>";

	echo "<link rel='stylesheet' href='lib/OpenLayers/theme/default/style.css' type='text/css'>";
    echo "<link rel='stylesheet' href='css/styles.css' type='text/css'>";

	echo "<div id = 'sysContent'><div style = 'height: 500px;'>";

		/*
		 *	Login Form File
		 */
		echo "<div id = 'system-login'>  <h1 id = 'title1'> "
			 . "Login to Ntobua- dLRev"
			 . "</h1>";
		echo "<center id='district1'></center>";
		include("login2.php");
		echo "</div>";

	echo "</div>";


	/*
	 *	Footer File
	 */
	//include("footer.php");



?>