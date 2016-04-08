<?php
/**
 * System Bootstraper
 * Bootstraps Soulex Core classes and enables autoloading.
 * System Logger is attached as plugin resource.
 */
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initNamespace()
    {
        Zend_Loader_Autoloader::getInstance()->registerNamespace('Soulex_');
    }

	protected function _initAutoload()
	{
		// Add autoloader empty namespace
		Zend_Loader_Autoloader::getInstance();
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath' => dirname(__FILE__)
        ));

		// Return it so that it can be stored by the bootstrap
		return $autoloader;
	}

    protected function _initLog()
    {
        if($this->hasPluginResource('log')) {
            $plugin = $this->getPluginResource('log');
            $log = $plugin->getLog();
            Zend_Registry::set('log', $log);
        }
    }

}