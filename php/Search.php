<?php

	if ($searching =="yes") 
	{ 
		echo "<h2>Results</h2><p>"; 
 
		// If they did not enter a search value we give them an error 
		if( $find == "" ) 
		{ 
			echo "<p>You forgot to enter a search term"; 
			exit; 
		} 
		
		$q = mysql_query( "SELECT * FROM 	`property` 
									WHERE 	`upn` like '".$find."' OR 
											`subupn` like '".$find."' ");
		$r = mysql_fetch_array($q);
		$anymatches = mysql_num_rows($data); 
		if( $anymatches == 0 ) 
		{ 
			echo "Sorry, but we can not find an entry to match your query <br>"; 
		} 
		
		//And we display the results 
		while( $r = mysql_fetch_array( $data ) ) 
		{ 
			echo "UPN: ", $r['upn'], "<br>"; 
			echo "SUBUPN: ", $r['subupn'], "<br>"; 
			echo "<br>"; 
			echo "<br>"; 
		} 
		
	}
?>

	<h2><center>Search</center></h2> 
	<form name="search" method="post" action="<?=$PHP_SELF?>">
		Seach for: <input type="text" name="find" /> 
		<input type="hidden" name="searching" value="yes" />
		<input type="submit" name="search" value="Search" />
	</form>