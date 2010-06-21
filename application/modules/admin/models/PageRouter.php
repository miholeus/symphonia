<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

class Admin_Model_PageRouter {
	/**
	 * @var string routes path
	 */
	protected $_routesPath;
    /**
     * Used while setting index page
     *
     * @var string default route name
     */
    protected $_defaultRouteName = '_defaultpage';
	/**
	 * 
	 * @var Model_PageRouter
	 */
	private static $_router = null;
	/**
	 * 
	 * @var Zend_Config
	 */
	private static $_config = null;
	/**
     * Sets path to routes ini file, default path is
     * application/configs/routes.ini
     *
     * @param string $routesPath
     */
	public function __construct($routesPath = null)
	{
        if($routesPath === null) {
            $this->_routesPath = APPLICATION_PATH . '/configs/routes.ini';
        } else {
            $this->_routesPath = $routesPath;
        }
	}
	/**
	 * @return Model_PageRouter
	 */
	public static function getInstance($routesPath = null)
	{
		if(self::$_router == null) {
			self::$_router = new Admin_Model_PageRouter($routesPath);
		}
		return self::$_router;
	}
	/**
	 * Creates route
	 * 
	 * @param int $pageId
	 * @param string $uri
	 * @param string $routeType
	 * @return void
	 */
	public function createRoute($pageId, $uri, $routeType = "Zend_Controller_Router_Route_Regex")
	{
		$routeName = $this->routeNameByUri($uri);
		$routeUri = $this->routeUri($uri);
		$routeSuffix = '(?:/.*)?';
		if(substr($routeUri, -4) == 'html') {
			$routeType = "Zend_Controller_Router_Route_Static";
			$routeSuffix = '';
		}
		if(empty($routeName)) {
			throw new Zend_Exception('Create route: route can not be empty!');
		}
		// loads routes config file
		$config = $this->loadRoutesFile();
		
		$config->production->routes->$routeName = array();
		$config->production->routes->$routeName->type = $routeType;
		$config->production->routes->$routeName->route = $routeUri . $routeSuffix;
		$config->production->routes->$routeName->defaults = array();
        $config->production->routes->$routeName->defaults->module = 'frontend';
		$config->production->routes->$routeName->defaults->controller = 'page';
		$config->production->routes->$routeName->defaults->action = 'open';
		$config->production->routes->$routeName->defaults->id = $pageId;

		$writer = new Zend_Config_Writer_Ini();
		$writer->write($this->_routesPath, $config);
	}
	/**
	 * Updates route
	 * 
	 * @param int $pageId
	 * @param string $newUri
	 * @param string $oldUri
	 * @return void
	 */
	public function updateRoute($pageId, $newUri, $oldUri)
	{
		if($newUri != $oldUri) {
			$this->deleteRoute($oldUri);
			$this->createRoute($pageId, $newUri);
		} else {
			$routeName = $this->routeNameByUri($newUri);
			// loads routes config file
			$config = $this->loadRoutesFile();
			if(empty($config->production->routes->$routeName)) {
				$this->createRoute($pageId, $newUri);
			}
		}
	}
	/**
	 * Deletes route
	 * 
	 * @param string $uri
	 * @return void
	 */
	public function deleteRoute($uri)
	{
		$routeName = $this->routeNameByUri($uri);
		// loads routes config file
		$config = $this->loadRoutesFile();
		
        $config->production->routes->$routeName = array();
        
        $writer = new Zend_Config_Writer_Ini();
		$writer->write($this->_routesPath, $config);
	}
	/**
	 * Ensure that routes path exists for pages
	 * 
	 * @return void
	 */
	private function checkRoutesPath()
	{
		if(!file_exists($this->_routesPath)) {
			throw new Zend_Exception("No routes path exists!");
		}
	}
	/**
	 * @return Zend_Config_Ini file
	 */
	private function loadRoutesFile()
	{
		$this->checkRoutesPath();
		// loads config file for modification and skips all extends
		if(self::$_config == null) {
			$config = new Zend_Config_Ini($this->_routesPath,
	                              null,
	                              array('skipExtends'        => true,
	                                    'allowModifications' => true));
		} else {
			$config = self::$_config;
		}
		
		if(empty($config->production)) {
			$config->production = array();
		}
		if(empty($config->production->routes)) {
			$config->production->routes = array();
		}

		return $config;
	}
	/**
	 * Gets route name by uri
	 * 
	 * @param string $uri
	 * @return string route name
	 */
	private function routeNameByUri($uri)
	{
		$routeName = preg_replace("/[^a-z0-9]/", "", strtolower($uri));
		if(substr($routeName, -4) == 'html') {
			$routeName = substr($routeName, 0, strlen($routeName) - 4);
		}
        $routeName = $this->routeDefault($routeName, $uri);
		return $routeName;
	}
   /**
	 * Gets route uri
	 * 
	 * @param string $uri
	 * @return string uri
	 */
	private function routeUri($uri)
	{
		$uri = trim($uri, '/');
		return preg_replace("/[^a-z0-9.\/_-]/", "", strtolower($uri));
	}
   /**
     * Setting index page route name
     *
     * @param string $uri
     * @return string route name
     */
    private function routeDefault($routeName, $uri)
    {
        if(empty($routeName) && $uri == '/') {
            $routeName = $this->_defaultRouteName;
        }
        return $routeName;
    }
}