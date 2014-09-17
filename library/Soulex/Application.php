<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

require_once 'Zend/Application.php';
/**
 * Application class
 * @see Zend_Application
 *
 * It can cache config file
 *
 * @author miholeus
 */
class Soulex_Application extends Zend_Application
{
    /**
     *
     * @var Zend_Cache_Core|null
     */
    protected $_configCache;

    public function __construct($environment, $options = null, Zend_Cache_Core $configCache = null)
    {
        $this->_configCache = $configCache;
        parent::__construct($environment, $options);
        register_shutdown_function(array($this, 'shutdown'));
    }

    protected function _cacheId($file)
    {
       return md5($file . '_' . $this->getEnvironment());
    }

    //Override
    protected function _loadConfig($file)
    {
        $suffix = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (
            $this->_configCache === null
            || $suffix == 'php'
            || $suffix == 'inc'
        ) { //No need for caching those
            return parent::_loadConfig($file);
        }

        $configMTime = filemtime($file);

        $cacheId = $this->_cacheId($file);
        $cacheLastMTime = $this->_configCache->test($cacheId);

        if (
            $cacheLastMTime !== false
            && $configMTime < $cacheLastMTime
        ) { //Valid cache?
            return $this->_configCache->load($cacheId, true);
        } else {
            $config = parent::_loadConfig($file);
            $this->_configCache->save($config, $cacheId, array(), null);

            return $config;
        }
    }
    public function shutdown()
    {
        $error = error_get_last();
        if(null !== $error) {
//            $fr = Zend_Controller_Front::getInstance();
//            $fr->getRequest()->setControllerName('Error')
//                    ->setActionName('fatall');
        }
    }
}