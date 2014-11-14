<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
//require_once(LIB_PATH.DS.'database.php');
require_once('database.php');
require_once('database_object.php');

class ScanDataBusiness extends DatabaseObject {
	
	protected static $table_name="business";

	protected static $db_fields = array('id', 'upn', 'subupn', 'id_property', 'year', 'town', 'locpl', 'pay_status', 'revenue_due', 'revenue_collected', 'revenue_balance', 'collector', 'collector_id', 'date_payment', 'streetname', 'housenumber', 'unit_planning', 'zone_revenue', 'locality_code', 'da_no', 'business_certif', 'employees', 'business_name', 'year_establ', 'landmark1', 'landmark2', 'owner', 'owneraddress', 'owner_tel', 'owner_email', 'business_class', 'comments', 'utm_x', 'utm_y', 'area_m2', 'district', 'lastentry_person', 'lastentry_date', 'districtid', 'subdistrictid', 'zoneid', 'floor', 'doornumber', 'ownerid', 'date_start', 'date_end', 'activestatus', 'category_code', 'class_code', 'subclass_code', 'pumps_no', 'storage_capacity', 'business_nature', 'environment_impact', 'business_type', 'business_code', 'business_code_name', 'assessed');

	 public $id;
	 public $upn;
	 public $subupn;
	 public $id_property;
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
	 public $streetname;
	 public $housenumber;
	 public $unit_planning;
	 public $zone_revenue;
	 public $locality_code;
	 public $da_no;
	 public $business_certif;
	 public $employees;
	 public $business_name;
	 public $year_establ;
	 public $landmark1;
	 public $landmark2;
	 public $owner;
	 public $owneraddress;
	 public $owner_tel;
	 public $owner_email;
	 public $business_class;
	 public $comments;
	 public $utm_x;
	 public $utm_y;
	 public $area_m2;
	 public $district;
	 public $lastentry_person;
	 public $lastentry_date;
	 public $districtid;
	 public $subdistrictid;
	 public $zoneid;
	 public $floor;
	 public $doornumber;
	 public $ownerid;
	 public $date_start;
	 public $date_end;
	 public $activestatus;
	 public $category_code;
	 public $class_code;
	 public $subclass_code;
	 public $pumps_no;
	 public $storage_capacity;
	 public $business_nature;
	 public $environment_impact;
	 public $business_type;
	 public $business_code;
	 public $business_code_name;
	 public $assessed;

  public static function find_by_upn($upn='') {
    $result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE upn={$upn} LIMIT 1");
//    $result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE upn='".$upn."'");
//	return !empty($result_array) ? array_shift($result_array) : false;
		return !empty($result_array) ? $result_array : false;
  }
  public static function find_all_upn() {
//    $result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE upn={$upn} LIMIT 1");
    $result_array = static::find_by_sql("SELECT DISTINCT id, upn FROM ".static::$table_name."");
//		return !empty($result_array) ? array_shift($result_array) : false;
		return !empty($result_array) ? $result_array : false;
  }
    public static function find_by_upn_subupn($upn="", $subupn="") {
	if( $subupn != "" || $subupn != NULL || $subupn != "0" )
	{							
		$result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE upn={$upn} AND subupn={$subupn} LIMIT 1");
	//		return !empty($result_array) ? array_shift($result_array) : false;
			return !empty($result_array) ? 'found' : 'not found';
		}
		else
		{
		$result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE upn={$upn}  LIMIT 1");
	//		return !empty($result_array) ? array_shift($result_array) : false;
			return !empty($result_array) ? 'found' : 'not found';
		}
	}


  public static function find_dups_of_upn($districtid='') {
//    $result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE upn={$upn} LIMIT 1");
    $result_array = static::find_by_sql("select `id`,`upn`, `subupn`, `districtid` 
    										FROM ".static::$table_name." where (`upn`) in ( select `upn` 
    										from ".static::$table_name." group by `upn` having count(1) > 1 ) 
    										AND `districtid`='".$districtid."' order by `upn`");
//		return !empty($result_array) ? array_shift($result_array) : false;
		return !empty($result_array) ? $result_array : false;
  }
  
  public function tell_table_name (){
    return self::$table_name;
    }
	

	public function update_by_id($id,$subupn) {
//	  global $database;
		// Don't forget your SQL syntax and good habits:
		// - UPDATE table SET key='value', key='value' WHERE condition
		// - single-quotes around all values
		// - escape all values to prevent SQL injection
		$sql = "UPDATE ".static::$table_name." SET ";
		$sql .= "subupn=".$subupn;
		$sql .= " WHERE id=". $id;
	  $database->query($sql);
	  return ($database->affected_rows() == 1) ? true : false;
	}

}

$sdBusiness = new ScanDataBusiness();
?>