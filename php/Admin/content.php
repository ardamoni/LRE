<?php

 	/*
	 *	Include the Library Code
	 *	-----------------------------------------------------------------------
	 */
	require_once("../../lib/configuration.php");		
	require_once( "../../lib/System.php" );
		
	$System = new System;
	$currentYear = $System->GetConfiguration("RevenueCollectionYear");
?>

<html>
	<head>
	<title><?php echo "Content" ?></title>
	
    <link rel = "stylesheet" type = "text/css" href = "Admin.css">
    

	<script>
		function DisableEnableForm(xForm,xHow){
			objElems = xForm.elements;
			for(i=0;i<objElems.length;i++){
				objElems[i].disabled = xHow;
			}
		}
		
		function mini(val,x){
			maxlen = x;
			if(val.length < maxlen){
				alert('Length of code should be 6');
			document.chars.tests.value = val.substring(0,maxlen);

			}
		}
		
    	window.onload = function(){
  			DisableEnableForm(document.edit, true);
			DisableEnableForm(document.disable, true);
		}

    </script>
</head>
<body>
<?php
	echo $currentYear;
	
	/* Include Local Functions */
	if( file_exists ( $_GET['mod'] . ".php" ) )
	{			
		include ( $_GET['mod'] . ".php" );
	}
?>
</body>

