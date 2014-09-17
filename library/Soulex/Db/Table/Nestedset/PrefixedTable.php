<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Nested sets with prefix table support
 *
 * @author miholeus
 */
class Soulex_Db_Table_Nestedset_PrefixedTable extends Soulex_Db_Table_Nestedset
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