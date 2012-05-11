<?php
/**
 * 
 * The url host row
 * 
 * @author nick
 *
 */
class Short_UrlHost extends Short_Model
{
	
	/**
	 * Id of url host
	 * 
	 * @var int
	 */
	public $id;
	
	/**
	 * Host. Can be empty
	 * 
	 * @var string
	 */
	public $host;
	
	/**
	 * Port
	 * 
	 * @var int
	 */
	public $port;
	
	/**
	 * Id of scheme
	 * 
	 * @var int
	 */
	public $scheme_id;
	
	/**
	 * 
	 * @var Short_UrlScheme
	 */
	public $_scheme;
	
	/**
	 * Constructor
	 * 
	 * @param array $options
	 */
	public function __construct($options=null) {
		parent::__construct($options);
		require_once 'Short/UrlScheme.php';
		$this->_scheme = Short_UrlScheme::loadByProperty($this->scheme_id);
	}
	
	public static function getTableName() {
		return 'url_host';
	}
}