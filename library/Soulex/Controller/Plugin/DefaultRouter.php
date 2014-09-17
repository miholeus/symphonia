<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Enables/disables Default Router for application
 *
 * @author miholeus
 */
class Soulex_Controller_Plugin_DefaultRouter extends Zend_Controller_Plugin_Abstract
{
    /**
     *
     * @var Zend_Controller_Router_Abstract
     */
    protected $router;
    /**
     *
     * @var Zend_Controller_Front 
     */
    protected $frontController;
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {        
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter();
        $router->removeDefaultRoutes();
        
        $this->frontController = $front;
        $this->router = $router;
    }
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        if($request->getModuleName() != 'frontend') {
            /**
             * Enable default routing
             */
            $route = new Zend_Controller_Router_Route_Module(array(),
                                                              $this->frontController->getDispatcher(),
                                                              $this->frontController->getRequest());
            $this->router->addRoute('default', $route);
        }
    }
}