<?php
/**
 * 
 * A simple model with a table backend
 * 
 * @author nick
 *
 */
abstract class Short_Model
{

	/**
	 * An array of tables for each extending model.
	 * 
	 * Class names are used as keys. Late static bindings are used to get class name
	 * 
	 * @var array
	 */
	private static $tables;

	/**
	 * Constructor
	 *
	 * @param array $options
	 */
	public function __construct($options = null) {

		//Ensure this class has an id
		if(!property_exists($this, 'id')) {
			throw new Exception('Class has no id.');
		}

		//Set object options
		if(is_array($options)) {
			$this->setOptions($options);
		}
	}

	/**
	 * Get a select object from the table
	 *
	 * @return Zend_Db_Table_Select
	 */
	public static function getSelect() {
		return self::getTable()->select();
	}

	/**
	 * Get the table for this object
	 *
	 * @return Zend_Db_Table
	 */
	public static function getTable() {
		if(!isset(self::$tables[static::getTableName()])) {
			self::$tables[static::getTableName()] = new Zend_Db_Table(static::getTableName());
		}
		return self::$tables[static::getTableName()];
	}

	/**
	 * Set the options for this object with an array
	 *
	 * @param array $options
	 */
	public function setOptions($options) {
		$vars = $this->toArray();
		foreach($vars as $var=>$value) {
			if(isset($options[$var])) {
				$this->$var = $options[$var];
			}
		}
	}

	/**
	 * Load single object by property and value
	 * 
	 * @param mixed $value Value of property
	 * @param string $property Name of property. Defaults to id
	 */
	public static function loadByProperty($value, $property='id') {
		
		//Delegate
		$data = static::loadByProperties(array($property=>$value), 1, false);
		if(!empty($data)) {
			return $data[0];
		} else {
			return null;
		}
	}

	/**
	 * Load objects matching properties. Uses AND for where statements.
	 * 
	 * The create flag can be passed to create an object with the given properties
	 * if no objects matching those were found.
	 * 
	 * @param array $props Associative array of properties and values
	 * @param int $count The number of objects to return
	 * @param boolean $create
	 */
	public static function loadByProperties($props, $count=10, $create=false) {
		
		$results = array();
		
		//Prepare statement
		$select = static::getSelect();
		$select->limit($count, 0);
		
		//Create where
		foreach($props as $prop=>$value) {
			if($prop == null || $value == null) {
				return $results;
			}
			$select->where($prop.' = ?', $value);
		}
		
		$data = static::getTable()->fetchAll($select);
		
		//Place results in objects
		$class = get_called_class();
		foreach($data as $rowData) {
			$results[] = new $class($rowData->toArray());
		}
		
		//Create entry if no results and create flag true
		if($create && empty($results)) {
			$obj = new $class($props);
			$obj->create();
			$results[] = $obj;
		}
		return $results;
	}

	/**
	 * Insert row in database
	 *
	 */
	public function create() {
		$this->id = $this->getTable()->insert($this->toArray());
	}

	/**
	 * Array of model properties.
	 * Properties prefixed with an underscore will not be returned
	 *
	 * @return array
	 */
	public function toArray() {
		$props = get_object_vars($this);
		foreach($props as $prop=>$value) {
			if(strpos($prop, '_') === 0) {
				unset($props[$prop]);
			}
		}
		return $props;
	}

	/**
	 * Get name of table
	 */
	abstract static public function getTableName();
}