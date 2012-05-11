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
	
	public static function getTableName() {
		return 'url_host';
	}
}