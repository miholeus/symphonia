<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Insertion's group Mapper
 * Maps objects data to table data
 *
 * @author miholeus
 */
class Soulex_Model_Insertion_GroupMapper extends Soulex_Model_DataMapper_Standard
{
    /**
     *
     * @var Soulex_Model_Insertion_DbTable_Group
     */
    protected $_dbTableClass = 'Soulex_Model_Insertion_DbTable_Group';
    /**
     *
     * @var Soulex_Model_Insertion_Group
     */
    protected $_object = 'Soulex_Model_Insertion_Group';
    /**
     *
     * @var Soulex_Model_Insertion_Insertion
     */
    protected $_collection = 'Soulex_Model_Insertion_GroupCollection';
    protected function prepareDataForSave(Soulex_Model_Abstract $object)
    {
        return $object->toArray();
    }
    /**
     * Simple search by name field using like operator
     *
     * @param string $value search value
     * @return Soulex_Model_Insertion_GroupMapper
     */
    public function search($value)
    {
        if(!empty($value)) {
            $value = str_replace('\\', '\\\\', $value);
            $value = addcslashes($value, '_%');
            $this->_select = $this->getSelect();
            $this->_select->where('name LIKE ?', '%' . $value . '%');
        }
        return $this;
    }
}