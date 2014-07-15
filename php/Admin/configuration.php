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
	$adminYear = $System->GetConfiguration("AdminYear");

?>
<h1 align = 'left' >Collection Year Change</h1>
<br />

<form method = "POST" action = "index.php" target = '_parent'>
	
<select name = "AdminBaseyear">
	<option value = '<?php echo ($currentYear - 4); ?>'<?php if( $adminYear == ($currentYear - 4)) echo " selected"; ?>>
		<?php echo ($currentYear - 4); ?>
	</option>
	
	<option value = '<?php echo ($currentYear - 3); ?>'<?php if( $adminYear == ($currentYear - 3)) echo " selected"; ?>>
		<?php echo ($currentYear - 3); ?>
	</option>

	<option value = '<?php echo ($currentYear - 2); ?>'<?php if( $adminYear == ($currentYear - 2)) echo " selected"; ?>>
		<?php echo ($currentYear - 2); ?>
	</option>

	<option value = '<?php echo ($currentYear - 1); ?>'<?php if( $adminYear == ($currentYear - 1)) echo " selected"; ?>>
		<?php echo ($currentYear - 1); ?>
	</option>
	
	<option value = '<?php echo ($currentYear - 0); ?>'<?php if( $adminYear == ($currentYear - 0)) echo " selected"; ?>>
		<?php echo ($currentYear - 0); ?>
	</option>

	<option value = '<?php echo ($currentYear + 1); ?>'<?php if( $adminYear == ($currentYear + 1)) echo " selected"; ?>>
		<?php echo ($currentYear + 1); ?>
	</option>
	
</select> &nbsp; 
<input type = "submit" value = "Save" />
</form>






