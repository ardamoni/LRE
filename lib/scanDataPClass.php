<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
//require_once(LIB_PATH.DS.'database.php');
require_once('database.php');
require_once('database_object.php');

class ScanDataProperty extends DatabaseObject {
	
	protected static $table_name="property";

	protected static $db_fields = array('id', 'upn', 'subupn', 'districtid', 'year', 'town', 'locpl', 'pay_status', 'revenue_due', 'revenue_collected', 'revenue_balance', 'collector', 'collector_id', 'date_payment', 'regnumber', 'streetname', 'housenumber', 'floor', 'unit_planning', 'zone_revenue', 'locality_code', 'business', 'structurecode', 'owner', 'ownerid', 'owneraddress', 'owner_tel', 'owner_email', 'rooms', 'year_construction', 'property_type', 'property_use', 'persons', 'roofing', 'ownership_type', 'constr_material', 'storeys', 'prop_value', 'prop_descriptor', 'planningpermit', 'planningpermit_no', 'buildingpermit', 'buildingpermit_no', 'comments', 'utm_x', 'utm_y', 'area_m2', 'district', 'lastentry_person', 'lastentry_date', 'subdistrictid', 'zoneid', 'doornumber', 'prop_descriptor_title', 'rate_code', 'rate_impost_code', 'property_type_title', 'property_use_title', 'ownership_type_title', 'constr_material_title', 'roofing_type_title', 'date_start', 'date_end', 'activestatus', 'assessed');

	public $id;
	public $upn;
	public $subupn;
	public $districtid;
	public $year;
	public $town;
	public $locpl;
	public $pay_status;
	public $revenue_due;
	public $revenue_collected;
	public $revenue_balance;
	public $collector;
	public $collector_id;
	public $date_payment;
	public $regnumber;
	public $streetname;
	public $housenumber;
	public $floor;
	public $unit_planning;
	public $zone_revenue;
	public $locality_code;
	public $business;
	public $structurecode;
	public $owner;
	public $ownerid;
	public $owneraddress;
	public $owner_tel;
	public $owner_email;
	public $rooms;
	public $year_construction;
	public $property_type;
	public $property_use;
	public $persons;
	public $roofing;
	public $ownership_type;
	public $constr_material;
	public $storeys;
	public $prop_value;
	public $prop_descriptor;
	public $planningpermit;
	public $planningpermit_no;
	public $buildingpermit;
	public $buildingpermit_no;
	public $comments;
	public $utm_x;
	public $utm_y;
	public $area_m2;
	public $district;
	public $lastentry_person;
	public $lastentry_date;
	public $subdistrictid;
	public $zoneid;
	public $doornumber;
	public $prop_descriptor_title;
	public $rate_code;
	public $rate_impost_code;
	public $property_type_title;
	public $property_use_title;
	public $ownership_type_title;
	public $constr_material_title;
	public $roofing_type_title;
	public $date_start;
	public $date_end;
	public $activestatus;
	public $assessed;

	
  public static function find_by_upn($upn=0) {
    $result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE upn={$upn} LIMIT 1");
//		return !empty($result_array) ? array_shift($result_array) : false;
		return !empty($result_array) ? 'found' : 'not found';
  }

  public static function find_by_upn_subupn($upn="", $subupn="") {
	if( $subupn != "" || $subupn != NULL || $subupn != "0" || strlen($subupn) != 0)
	{					
		$result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE `upn`='{$upn}' AND `subupn`='{$subupn}' LIMIT 1 ;");
	//		return !empty($result_array) ? array_shift($result_array) : false;
			return !empty($result_array) ? 'found' : 'not found';
		}
		else
		{
	echo "SELECT * FROM ".static::$table_name." WHERE upn='{$upn}' AND subupn='{$subupn}'";
		$result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE `upn`='".$upn."' LIMIT 1 ;");
	//		return !empty($result_array) ? array_shift($result_array) : false;
	echo $this->id;
			return !empty($result_array) ? 'found' : 'not found';
		}
	}

  public static function find_dups_of_upn($districtid='') {
    $result_array = static::find_by_sql("select `id`,`upn`, `subupn`, `districtid` 
    										FROM ".static::$table_name." where (`upn`) in ( select `upn` 
    										from ".static::$table_name." group by `upn` having count(1) > 1 ) 
    										AND `districtid`='".$districtid."' order by `upn`");
		return !empty($result_array) ? $result_array : false;
  }
  

  public function tell_table_name (){
    return self::$table_name;
    }
	
}

$sdProperty = new ScanDataProperty();
?>