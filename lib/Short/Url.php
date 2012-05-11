<?php
/**
 *
 * The url row
 *
 * @author nick
 *
 */
class Short_Url extends Short_Model
{
	/**
	 * Id of url
	 *
	 * @var int
	 */
	public $id;

	/**
	 * Path of url without leading slash
	 *
	 * @var string
	 */
	public $path;

	/**
	 * Id of host
	 *
	 * @var int
	 */
	public $host_id;

	/**
	 * Host object
	 *
	 * @var Short_UrlHost
	 */
	public $_host;

	/**
	 * Constructor
	 *
	 * @param array $options
	 */
	public function __construct($options=null) {
		parent::__construct($options);
		require_once 'Short/UrlHost.php';
		$this->_host = Short_UrlHost::loadByProperty($this->host_id);
	}

	/**
	 * Concatenate real url from parts
	 *
	 * @return string
	 */
	public function getUrl() {

		//Scheme
		$url = $this->_host->_scheme->scheme;

		//Authority
		$has_authority = $this->_host->host != '|';

		$url.= !$has_authority ? ':':'://'.$this->_host->host;
		$url.= !$has_authority || empty($this->_host->port) ?'':':'.$this->_host->port;
		$url.= !$has_authority ? '':'/';

		//Path
		$url.= $this->path == '|' ? '':$this->path;
		return $url;
	}

	public static function getTableName() {
		return 'url';
	}
}