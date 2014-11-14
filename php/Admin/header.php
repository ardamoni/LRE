<?php
	/*
	 *	Start the Session
	 */
	session_start();


	/*
	 *	Include the Library Code
	 *	-----------------------------------------------------------------------
	 */
	require_once( "../../lib/configuration.php" );
	require_once( "../../lib/System.php" );
		
	$System = new System;
	$currentYear = $System->GetConfiguration("RevenueCollectionYear");
?>
<html>
	<head>
		<title>Local Revenue Enhancment</title>
	</head>
	
	<script type='text/javascript' src='js/jquery.js'></script>
    <script type='text/javascript' src='js/jquery.ui.js'></script>
<body>
<div align = "center" id = 'head'>
	
		<h2 style = 'margin:0px;'>System Administration</h2>	    
		</br><b>Collection Year: </b><?php echo $currentYear; ?>
    
</div>

<div align = "center" id="toolbar">
	
	<ol>
		<a href = 'menu.php?id=1' target = 'menu'><span>Districts</span></a>
		<a href = 'menu.php?id=2' target = 'menu'><span>Users</span></a>
		<a href = 'menu.php?id=3' target = 'menu'><span>Functions</span></a>
		<a href = 'menu.php?id=4' target = 'menu'><span>Labels</span></a>
	
		<a class="right" href="right.php" target = 'content'><span>Config</span></a>
	</ol>
	
</div>



