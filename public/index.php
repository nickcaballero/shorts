<?php

//Ensure lib/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
		realpath(dirname(__FILE__) . '/../lib'),
		get_include_path()
)));

//Database configuration
$DB_CONFIG = array(
		'host' => 'localhost',
		'username' => 'short_user',
		'password' => 'shortpass',
		'dbname' => 'short');

//Setup db stuff
/**
 * Using Zend PDO MySQL adapter
 */
require_once 'Zend/Db.php';
require_once 'Zend/Loader.php';
require_once 'Zend/Registry.php';
require_once 'Zend/Db/Table.php';
$adapter = Zend_Db::factory('PDO_MYSQL', $DB_CONFIG);
Zend_Db_Table::setDefaultAdapter($adapter);

//Load view class
require_once 'Short/View.php';

//First get short name if any
list(, $name) = explode('/', parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH));

//If name passed and not post, try routing
if (!empty($name) && $_SERVER['REQUEST_METHOD'] != 'POST') {

	//Require model
	require_once 'Short/Model.php';
	require_once 'Short/Short.php';

	$short = Short_Short::loadShort($name);
	if ($short) {

		//Route the short
		require_once 'Short/Router.php';
		$router = new Short_Router($short);
		$router->go();

	} else {

		//Check if any static views match the short
		try{
			$view = new Short_View('static/'.$name.'.phtml');
			echo Short_View::wrapView($view);
		} catch(Exception $e) {

			//Prepare error view
			$static = new Short_View('static/error.phtml');

			//Check if view was created but failed to render
			if(isset($view)) {
				$static->message = 'Encountered error while rendering content. Sorry about that.';
			}

			echo Short_View::wrapView($static);
		}
	}
} else if($name == 'create' && $_SERVER['REQUEST_METHOD'] == 'POST') {
	/**
	 * Here we are assuming there is only ever one kind of POST
	 * Hence, the controller chunk is here
	 *
	 * However, it would be trivial to fully implement the MVC design pattern
	 */

	$view = new Short_View('static/create.phtml');

	if(isset($_POST['url']) && parse_url($_POST['url']) !== false) {
		require_once 'Short/Model.php';
		require_once 'Short/Short.php';
		$short = new Short_Short();
		$short->generateName();
		$short->setUrl($_POST['url']);
		$short->create();
		$view->short = $short;
	} else {
		$view->url = false;
	}

	Short_View::wrapView($view);
} else {

	/**
	 * In the case where the short name was not create and the method was POST, just ignore it.
	 * A programmer posting to a URL shortener is confusing the service in general.
	 */

	//Welcome screen
	$welcome = new Short_View('static/welcome.phtml');
	echo Short_View::wrapView($welcome);
}
?>