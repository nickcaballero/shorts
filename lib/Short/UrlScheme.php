<?php
/**
 * 
 * The url scheme row
 * 
 * @author nick
 *
 */
class Short_UrlScheme extends Short_Model
{
	
	/**
	 * Id of url scheme
	 * 
	 * @var int
	 */
	public $id;
	
	/**
	 * Scheme
	 * 
	 * @var string
	 */
	public $scheme;
	
	public static function getTableName() {
		return 'url_scheme';
	}
}