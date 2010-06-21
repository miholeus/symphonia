<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Bootstrap
 *
 * @author miholeus
 */
class Frontend_Bootstrap extends Zend_Application_Module_Bootstrap
{

	protected function _initRouter()
	{
		$frontController = Zend_Controller_Front::getInstance();

		$router = $frontController->getRouter();

		try {
            $router->addConfig(new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'production'), 'routes');
            $router->addConfig(new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.custom.ini', 'production'), 'routes');
        } catch (Zend_Controller_Router_Exception $e) {
            // no route configuration in routes
            // @TODO check error!!
            // Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->gotoUrl('/admin');
        }
	}

	protected function _initNamespace()
	{
        // register Zend_Controller_Plugin Abstract
        $frontController = Zend_Controller_Front::getInstance();
        $frontController->registerPlugin(new Soulex_Invoke_Plugin());
	}
}
?>
