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

		$port = 	isset($parts['port']) ? (int) $parts['port'] :'0';

		//Normalize url parts
		$scheme = 	isset($parts['scheme'])	? strtolower($parts['scheme'])		:'http';
		$host = 	isset($parts['host'])	? strtolower($parts['host'])		:'|';
		$path = 	isset($parts['path'])	? ltrim($parts['path'], '/')		:'|';

		//Load-create url parts
		/**
		* This could be optimized but performance increase would be minimal.
		* The probability that the database already has a url is very low.
		*
		* If the website handles many short creations, this could use a performance boost.
		*/
		require_once 'Short/Url.php';
		require_once 'Short/UrlHost.php';
		require_once 'Short/UrlScheme.php';
		
		list($scheme) = Short_UrlScheme::loadByProperties(array(
				'scheme'=>$scheme),1,true);
		list($host) = Short_UrlHost::loadByProperties(array(
				'host'=>$host,
				'port'=>$port,
				'scheme_id'=>$scheme->id),1,true);
		list($url) = Short_Url::loadByProperties(array(
				'path'=>$path,
				'host_id'=>$host->id),1,true);

		$host->_scheme = $scheme;
		$url->_host = $host;

		$this->_url = $url;
		$this->url_id = $url->id;
	}

	/**
	 * Load the short and its url parts
	 *
	 * @param string $name
	 */
	public static function loadShort($name) {
		
		require_once 'Short/Url.php';
		require_once 'Short/UrlHost.php';
		require_once 'Short/UrlScheme.php';

		$items = array();

		//Prepare select
		$select = static::getSelect();
		$select->setIntegrityCheck(false)->reset();
		
		//Prepare the join
		$select->from(array(
				'short'=>static::getTableName()));
		$select->join(array(
				'url'=>Short_Url::getTableName()),
				'short.url_id = url.id', array('path','host_id'));
		$select->join(array(
				'url_host'=>Short_UrlHost::getTableName()),
				'url.host_id = url_host.id', array('host', 'port', 'scheme_id'));
		$select->join(array(
				'url_scheme'=>Short_UrlScheme::getTableName()),
				'url_host.scheme_id = url_scheme.id', array('scheme'));
		$select->where('short.name = ? ', $name);
		
		$data = static::getTable()->fetchRow($select);
		
		if(!$data) {
			return null;
		} else {
			$data = $data->toArray();
		}
		
		$short = new Short_Short($data);
		
		$short->_url = new Short_Url($data);
		$short->_url->id = $short->url_id;
		
		$short->_url->_host = new Short_UrlHost($data);
		$short->_url->_host->id = $short->_url->host_id;
		
		$short->_url->_host->_scheme = new Short_UrlScheme($data);
		$short->_url->_host->_scheme->id = $short->_url->_host->scheme_id;
		
		return $short;
	}

	public static function getTableName() {
		return 'short';
	}
}
?>