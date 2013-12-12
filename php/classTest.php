<?php

	/*	
	 * 	this file is used to insert the revenue collection into tables
	 */
    require_once("../lib/initialize.php");

	$ffix = new FeefixProperty;

//$class = Feefix::find_by_code(122);	
//echo $class->class."<br />";

// $ffix->districtid=999;
// $ffix->code=999;
// $ffix->class='Test Class by Ekke';
// $ffix->category='Test Category ';
// $ffix->rate=99;
// $ffix->year=2014;
// $ffix->unit='test';
// $ffix->rate_impost=0.99999;
// $ffix->comments='This is the first INCLUDE';
// 
// $ffix->save();
	
    $fromClassall = FeefixProperty::find_all() ;    
    foreach($fromClassall as $record) {
	  echo "Id: ". $recordr->id ."<br />";
	  echo "Code: ". $record->code ."<br />";
	  echo "Class: ". $record->class ."<br />";
}

//    var_dump($fromClassall);
    echo "<br>test<br>";
    
?>