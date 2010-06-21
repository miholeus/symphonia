<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * FileLogger logs into files
 *
 * @author miholeus
 */
class Soulex_Logger_FileLogger
{
    /**
     *
     * @var Zend_Log
     */
    protected $_logger;
    /**
     *
     * @var Soulex_Logger_FileLogger
     */
    public static $_instance = null;

    public static function getInstance()
    {
        if(self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    protected function  __construct()
    {
        $this->_logger = Zend_Registry::get('log');
    }
    /**
     *
     * @return Zend_Log
     */
    public function getLog()
    {
        return $this->_logger;
    }
    /**
     * Logs info message
     * 
     * @param string $message
     */
    public static function info($message)
    {
        self::getInstance()->getLog()->info($message);
    }
}