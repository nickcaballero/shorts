<?php
/**
 *
 * The short class encapsulates the short and its properties
 *
 * @author nick
 */
class Short_Short extends Short_Model
{

	/**
	 * Real url
	 *
	 * @var string
	 */
	public $url_id;

	/**
	 * Name of short
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Date on which shortener was created
	 *
	 * @var string
	 */
	public $created;

	/**
	 * Id of shortener
	 *
	 * @var id
	 */
	public $id;

	/**
	 * Instance of url object
	 *
	 * @var Short_Url
	 */
	public $_url;

	/**
	 * Constructor
	 *
	 * @param array $options
	 */
	public function __construct($options = null) {
		parent::__construct($options);
		require_once 'Short/Url.php';
		$this->_url = Short_Url::loadByProperty($this->url_id);
	}

	/**
	 * Generates a name for the short
	 *
	 */
	public function generateName() {
		
		//Generate name
		$alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
		$name = '';
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, strlen($alphabet)-1);
			$name .= $alphabet[$n];
		}
		
		//Ensure name has not been used
		$select = $this->getSelect();
		$select->reset();
		$select->from($this->getTableName(), 'COUNT(*) as num');
		$select->where('name = ?', $name);
		$count = $this->getTable()->fetchRow($select)->num;
		
		if($count > 0) {
			$this->generateName();
		} else {
			$this->name = $name;
		}
	}

	/**
	 * Get url for short
	 */
	public function toUrl() {
		return 'http://'.$_SERVER['HTTP_HOST'].'/'.$this->name;
	}

	/**
	 * Set short url
	 *
	 * @param string $url
	 */
	public function setUrl($url) {

		//Parse url
		$parts = parse_url($url);

		$scheme = 	isset($parts['scheme'])?$parts['scheme']:'http';
		$port = 	isset($parts['port'])?$parts['port']:'0';
		$path = 	isset($parts['path'])?ltrim($parts['path'], '/'):'|';
		$host = 	isset($parts['host'])?$parts['host']:'|';

		//Load-create url parts
		require_once 'Short/UrlScheme.php';
		require_once 'Short/UrlHost.php';
		list($scheme) = Short_UrlScheme::loadByProperties(array(
				'scheme'=>$scheme),1,true);
		list($host) = Short_UrlHost::loadByProperties(array(
				'host'=>$host,
				'port'=>$port,
				'scheme_id'=>$scheme->id),1,true);
		list($url) = Short_Url::loadByProperties(array(
				'path'=>$path,
				'host_id'=>$host->id),1,true);

		$this->url_id = $url->id;
		$this->_url = $url;
	}

	public static function getTableName() {
		return 'short';
	}
}
?>