<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
//require_once(LIB_PATH.DS.'database.php');
require_once('database.php');
require_once('database_object.php');

class FeefixBusiness extends DatabaseObject {
	
	protected static $table_name="fee_fixing_business";

	protected static $db_fields = array('id', 'districtid', 'code', 'class', 'category', 'rate', 'year', 'unit', 'assessed', 'rate_impost', 'code_of_zone', 'name_of_zone', 'comments');

	public $id;
	public $districtid;
	public $code;
	public $class;
	public $category;
	public $rate;
	public $year;
	public $unit;
	public $assessed;
	public $rate_impost;
	public $code_of_zone;
	public $name_of_zone;
	public $comments;
	
	
  public static function find_by_code($code=0) {
    $result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE code={$code} LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
  }
  
  public function tell_table_name (){
    return self::$table_name;
    }
	
}

$feefixb = new FeefixBusiness();
?>