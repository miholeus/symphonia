<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Data Mapper Abstract uses setDbTable() and
 * getDbTable() methods to connect to table data gateway
 *
 * @author miholeus
 */
class Admin_Model_DataMapper_Abstract
{
    protected $_dbTable;
    protected $_dbTableClass;
    /**
     * Set table data gateway
     *
     * @param string|Zend_Db_Table_Abstract $dbTable
     * @return Zend_Db_Table_Abstract
     */
    public function setDbTable($dbTable)
    {
        if (is_string($dbTable)) {
            $dbTable = new $dbTable();
        }
        if (!$dbTable instanceof Zend_Db_Table_Abstract) {
            throw new Zend_Exception('Invalid table data gateway provided');
        }
        $this->_dbTable = $dbTable;
        return $this;
    }
    /**
     * Get table data gateway
     * 
     * @return Zend_Db_Table_Abstract
     */
    public function getDbTable()
    {
        if (null === $this->_dbTable) {
            $this->setDbTable($this->_dbTableClass);
        }
        return $this->_dbTable;
    }
}
