<?php

require_once('../lib/html_form.class.php');
require_once( "../lib/configuration.php"	);
require_once( "../lib/PropertyDetails.php"	);

//get the current database entries from property
$upn ="608-0615-0236";
$subupn = '';
$currentyear = '2013';
$districtid = '130';
	$Data = new propertyDetailsClass;
    $r = $Data->getPInfo( $upn, $subupn, $currentyear, $districtid ) ;
var_dump($r);
?>