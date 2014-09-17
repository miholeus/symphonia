<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Gives database prefix support for tables
 *
 * @author miholeus
 */
class Soulex_Model_DbTable_Abstract_PrefixedTable extends Zend_Db_Table_Abstract
{
    public function __construct($config = array())
    {
        parent::__construct($config);
        $config = $this->getAdapter()->getConfig();
        if(array_key_exists('prefix', $config))
        {
            $this->_name = $config['prefix'] . $this->_name;
        }
    }
    /**
     *
     * @return string table name
     */
    public function getName()
    {
        return $this->_name;
    }
}