<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Insertion Mapper
 * Maps objects data to table data
 *
 * @author miholeus
 */
class Soulex_Model_Insertion_Mapper extends Soulex_Model_DataMapper_Standard
{
    /**
     *
     * @var Soulex_Model_Insertion_DbTable_Insertion
     */
    protected $_dbTableClass = 'Soulex_Model_Insertion_DbTable_Insertion';
    /**
     *
     * @var Soulex_Model_Insertion_Insertion
     */
    protected $_object = 'Soulex_Model_Insertion_Insertion';
    /**
     *
     * @var Soulex_Model_Insertion_Insertion
     */
    protected $_collection = 'Soulex_Model_Insertion_Collection';
    protected function prepareDataForSave(Soulex_Model_Abstract $object)
    {
        return $object->toArray();
    }
    /**
     * Simple search by name field using like operator
     *
     * @param string $value search value
     * @return Soulex_Model_Insertion_Mapper
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
    /**
     * Get insertions by selected fields
     *
     * @param array $fields
     * @param array $fetched_fields
     * @return Soulex_Model_Insertion_Collection / null if nothing was found
     */
    public function getItemsBy(array $fields, $fetched_fields = array('source'))
    {
        if(count($fields) > 0) {
            $select = $this->getDbTable()->getAdapter()->select()
                    ->from($this->getDbTable()->getName(), $fetched_fields);
            foreach($fields as $fieldName => $fieldValue) {
                $select->where($fieldName . ' = ?', $fieldValue);
            }
            $select->order('group_position DESC');
            $result = $this->getDbTable()->getAdapter()->fetchAll($select);
            if(count($result) > 0) {
                return new Soulex_Model_Insertion_Collection($result, $this);
            }
        }
        return null;
    }
}