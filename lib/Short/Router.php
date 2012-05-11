<?php
/**
 *
 * The router class handles shorts.
 *
 * @author Nick Caballero
 */

class Short_Router
{
	/**
	 *
	 * @var Short_Short
	 */
	private $short;

	/**
	 * Router constructor for short
	 *
	 * @param Short_Short $short
	 */
	public function __construct($short) {
		$this->short = $short;
	}

	/**
	 * Evaluate state and short and redirect accordingly
	 */
	public function go() {
		if($this->isStats()) {
			$this->statistics();
		} else {
			$this->redirect();
		}
	}

	/**
	 * Redirect to the URL and update any relevant statistics
	 */
	private function redirect() {

		//Get geolocation of remote client using ip
		//$geo = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR']));

		//During local testing skip ip - this let's geoplugin resolve the ip
		$geo = unserialize(file_get_contents('http://www.geoplugin.net/php.gp'));

		//Create log entry
		require_once 'Short/LogEntry.php';
		$entry = new Short_LogEntry(array(
				'short_id' => $this->short->id,
				'country' => $geo['geoplugin_countryCode']
		));
		$entry->create();

		//301 redirect
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: ".$this->short->_url->getUrl());
	}

	/**
	 * Load statistics data and pass it to the statistics view
	 */
	private function statistics() {

		//Load log data
		require_once 'Short/LogEntry.php';
		$select = Short_LogEntry::getSelect();

		/**
		 * Load an intial data set that can be used by the frontend
		 */
		$select->where('short_id = ?', $this->short->id);
		$select->order('timestamp DESC');
		$select->limit(
				isset($_GET['count']) ? (int) $_GET['count'] : 100,
				isset($_GET['offset']) ? (int) $_GET['offset'] : 0);

		//Filter countries if passed
		if(isset($_GET['country']) && !empty($_GET['country'])) {
			$countries = explode('|', $_GET['country']);
			foreach($countries as $country) {
				$select->orWhere('country = ?', $country);
			}
		}

		//Load
		$log_entries = Short_LogEntry::getTable()->fetchAll($select)->toArray();

		/**
		 * Count the amount of clicks overall
		 */
		$select->reset();
		$select->from(Short_LogEntry::getTableName(), 'COUNT(*) as num');
		$select->where('short_id = ?', $this->short->id);
		$count = Short_LogEntry::getTable()->fetchRow($select)->num;

		//Render view
		$view = new Short_View('short/statistics.phtml');
		$view->log_entries = $log_entries;
		$view->short = $this->short;
		$view->count = $count;
		echo Short_View::wrapView($view);
	}

	/**
	 * Checks if the client is requesting statistics for a short
	 *
	 * @return boolean
	 */
	private function isStats() {
		return isset($_GET['stats']) && $_GET['stats'] == 'true';
	}

}
?>