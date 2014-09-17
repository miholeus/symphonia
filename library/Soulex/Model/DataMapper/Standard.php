<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * DataMapper abstract class for standard operations
 * Realizes some simple operations such as findById(), save(), delete() and
 * others
 *
 * @author miholeus
 */
abstract class Soulex_Model_DataMapper_Standard extends Soulex_Model_DataMapper_Abstract
{
    /**
     * Data object that mapper uses
     *
     * @var Soulex_Model_Abstract
     */
    protected $_object;
    /**
     * Collection of data objects
     *
     * @var Soulex_Model_DataMapper_Collection
     */
    protected $_collection;

    /**
     * Creates object from array
     *
     * @param array $array
     * @throws UnexpectedValueException
     * @return mixed
     */
    protected function createFromArray(array $array)
    {
        $object = new $this->_object($array);
        if(!$object instanceof Soulex_Model_Abstract) {
            throw new UnexpectedValueException("Class " . get_class($object)
                    . ' should extend Admin_Model_Abstract');
        }
        return $object;
    }
    /**
     * Saves data in database
     *
     * @param Soulex_Model_Abstract $object
     */
    public function save(Soulex_Model_Abstract $object)
    {
        $data = $this->prepareDataForSave($object);

        if (null === ($id = $object->getId())) {
            $this->getDbTable()->insert($data);
            $insertedId = $this->getDbTable()->getDefaultAdapter()->lastInsertId();
            $object->setId($insertedId);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
    }
    /**
     * Prepares data that is needed to be saved
     *
     * @param Soulex_Model_Abstract $object takes data that should be
     * prepared for future saving
     * @return array prepared data
     */
    protected abstract function prepareDataForSave(Soulex_Model_Abstract $object);

    /**
     * Fetches objects
     *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
     * @throws UnexpectedValueException
     * @return Soulex_Model_DataMapper_Collection all set
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);		
        $collection = new $this->_collection($resultSet->toArray(), $this);
        if(!$collection instanceof Soulex_Model_DataMapper_Collection) {
            throw new UnexpectedValueException("Class " . get_class($collection)
                    . ' should extend Admin_Model_DataMapper_Collection');
        }
        return $collection;
    }

    /**
     * Finds data object by its id
     *
     * @param int $id
     * @throws UnexpectedValueException
     * @return Soulex_Model_Abstract
     */
    public function findById($id)
    {     
		$result = $this->getDbTable()->find($id);		
        if (0 == count($result)) {
            throw new UnexpectedValueException($this->_object
                    . " by id " . $id . " not found");
        }		
        $object = new $this->_object();		
        $row = $result->current();		
        $object->setOptions($row->toArray());			
        return $object;
    }

    /**
     * @param $params
     * @return null|Soulex_Model_Abstract
     */
    public function findSingleBy($params)
    {
        $result = $this->getDbTable()->fetchRow($params);
        if (!empty($result)) {
            $object = new $this->_object($result);
            return $object;
        }
        return null;
    }
    /**
     * Delete object by id
     *
     * @param int $id
     * @return int number of deleted rows
     */
    public function delete($id)
    {
        $object = $this->findById($id);
        if(null !== $object) {
            $where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $id);
            return $this->getDbTable()->delete($where);
        }
    }
    /**
     * Mass deletion of items
     *
     * @param array $ids
     */
    public function deleteBulk(array $ids)
    {
        if(count($ids) > 0) {
            foreach($ids as $id) {
                $this->delete($id);
            }
        }
    }
    /**
     * Sets where state
     *
     * @param string   $cond  The WHERE condition.
     * @param mixed    $value OPTIONAL The value to quote into the condition.
     * @param string   $type  OPTIONAL The type of the given value
     * @return Zend_Db_Select This Zend_Db_Select object.
     */
    public function where($cond, $value = null, $type = null)
    {
        $this->_select = $this->getSelect();
        $this->_select->where($cond, $value, $type);
        return $this;
    }
    /**
     * Sets ordering state
     *
     * @param string $spec the column and direction to sort by
     * @return Admin_Model_DataMapper_Abstract
     */
    public function order($spec)
    {
        $this->_select = $this->getSelect();
        $this->_select->order($spec);
        return $this;
    }
    /**
     *
     * Sets a limit count and offset to the query.
     *
     * @param int $count OPTIONAL The number of rows to return.
     * @param int $offset OPTIONAL Start returning after this many rows.
     * @return Soulex_Model_DataMapper_Standard
     */
    public function limit($count = null, $offset = null)
    {
        $this->_select = $this->getSelect();
        $this->_select->limit($count, $offset);
        return $this;
    }
    /**
	 * Fetches paginator
	 *
     * @return Zend_Paginator
	 */
    public function paginate()
    {
        $adapter = new Zend_Paginator_Adapter_DbSelect($this->getSelect());
        return new Zend_Paginator($adapter);
    }
}
