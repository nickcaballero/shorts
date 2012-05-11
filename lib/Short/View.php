<?php
/**
 * 
 * A simple view class
 * 
 * @author nick
 *
 */
class Short_View
{
	/**
	 * Path prefix for all views
	 * 
	 * @var string
	 */
	private static $pathPrefix = '../views/';
	
	/**
	 * Full path to view file
	 * 
	 * @var string
	 */
	public $viewPath;
	
	/**
	 * Title for view
	 * 
	 * @var string
	 */
	public $title = '';

	/**
	 * Constructor takes name of view and validates it
	 * 
	 * @param string $name
	 */
	public function __construct($name) {
		$this->setName($name);
	}

	/**
	 * Render the view
	 */
	public function render() {
		ob_start();
		include $this->viewPath;
		return ob_get_clean();
	}

	/**
	 * Get view path
	 *
	 * @param string $name Name of view. Path relative to path prefix
	 */
	public static function getViewPath($name) {
		return self::$pathPrefix.$name;
	}

	/**
	 * Set the view name
	 *
	 * @param string $name Name of view. Path relative to path prefix
	 * @throws Exception
	 */
	public function setName($name) {
		//Validate view name
		if(preg_match('#\.\.[\\\/]#', $name)) {
			throw new Exception('Invalid name');
		}

		//Check if file is readable
		$viewPath = $this->getViewPath($name);
		if(!is_readable($viewPath)) {
			throw new Exception('View not readable');
		}

		$this->viewPath = $viewPath;
	}

	/**
	 * Wrap a view around a layout
	 *
	 * @param Short_View $view
	 * @param string $layout Name of layout
	 */
	public static function wrapView($view, $layout='layout.phtml') {
		$html = new Short_View($layout);
		$html->body = $view->render();
		$html->title = $view->title;
		echo $html->render();
	}
}