<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Maps Admin_Model_Sysinfo to Database objects
 *
 * @author miholeus
 */
class Admin_Model_SysinfoMapper
{
    private static $_instance = null;
    /**
     *
     * @var Zend_Db 
     */
    protected $_db;
    /**
     *
     *
     * @var array
     */
    protected $_dbCollationInfo;

    protected function getDbCollationVariable($variable)
    {
        if(!empty($this->_dbCollationInfo[$variable])) {
            return $this->_dbCollationInfo[$variable];
        }
        return false;
    }
    /**
     * Gets collation_$variable from database
     *
     * @param string $variable
     * @return string
     */
    protected function getDbCollationInfo($variable)
    {
        // if we have already collation info
        if($this->getDbCollationVariable($variable)) {
            return $this->getDbCollationVariable($variable);
        }
        // else loads data from database
        $var_value = '';

        $result = $this->_db->query('SHOW variables LIKE ?', 'collation_%');
        $rows = $result->fetchAll();
        foreach($rows as $row) {
            $this->_dbCollationInfo[$row['Variable_name']] = $row['Value'];
            if($variable == $row['Variable_name']) {
                $var_value = $row['Value'];
            }
        }

        return $var_value;
    }
    
    public static function getInstance()
    {
        if(self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function __construct()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
        $this->_db = Zend_Db::factory($config->resources->db->adapter, $config->resources->db->params);

        $this->_dbCollationInfo = array(
            'collation_connection' => '',
            'collation_database' => '',
            'collation_server' => ''
        );
    }
    public function dbVersion()
    {
        return $this->_db->getServerVersion();
    }

    public function databaseCollation()
    {
        return $this->getDbCollationInfo('collation_database');
    }

    public function connectionCollation()
    {
        return $this->getDbCollationInfo('collation_connection');
    }

    public function serverCollation()
    {
        return $this->getDbCollationInfo('collation_server');
    }

}
