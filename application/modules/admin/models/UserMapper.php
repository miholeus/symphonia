<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Admin_Model_UserMapper
 *
 * @author miholeus
 */
class Admin_Model_UserMapper extends Admin_Model_DataMapper_Abstract
{
    protected $_dbTableClass = 'Admin_Model_DbTable_User';

    public function save(Admin_Model_User $user)
    {
        $data = $user->toArray();
        unset($data['lastvisitDate']);

        if(!empty($data['password'])) {
            $data['password'] = $user->generatePassword($data['password']);
        }

        if (null === ($id = $user->getId())) {
            // checks user existance, throws exception if found one
            $user->checkUserExistanceByUsername($data['username']);

            $data['registerDate'] = date("Y-m-d H:i:s");

            try {
                $this->getDbTable()->insert($data);
                $insertedId = $this->getDbTable()->getDefaultAdapter()->lastInsertId();
                $user->setId($insertedId);
            } catch (Zend_Exception $exc) {
                throw new Zend_Exception($exc->getMessage());
            }

        } else {
            unset($data['registerDate']);

            try {
                $rowUser = $this->getDbTable()->find($id)->current();
                if($rowUser) {
                    /**
                     * username and password can not be null
                     */
                    if(null === $data['username']) {
                        unset($data['username']);
                    }
                    if(empty($data['password'])) {
                        unset($data['password']);
                    }
                    $this->getDbTable()->update($data, array('id = ?' => $id));
                } else {
                    throw new Zend_Exception("User with id " . $id . " not found");
                }
            } catch (Zend_Exception $exc) {
                throw new Zend_Exception($exc->getMessage());
            }
        }
        return $user;
    }
    /**
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
     * @return Admin_Model_User array all set
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row) {
            $entries[] = new Admin_Model_User($row->toArray());
        }
        return $entries;
    }
	/**
	 * Fetches user items
	 *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @return Zend_Paginator_Adapter_DbSelect
	 */
    public function fetchPaginator($where, $order)
    {
        $select = $this->getDbTable()->select();
        if(null !== $where) {
            $select->where($where);
        }
        if(null !== $order) {
            $select->order($order);
        }

        $adapter = new Zend_Paginator_Adapter_DbSelect($select);
        return $adapter;
    }

    public function delete($id)
    {
        $row = $this->getDbTable()->find($id)->current();
        if(null === $row) {
            throw new Zend_Exception("User with id " . $id . " not found");
        }
        $where = $this->getDbTable()->getDefaultAdapter()->quoteInto('id = ?', $id);
        $this->getDbTable()->delete($where);
    }

    public function find($id, Admin_Model_User $user)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            throw new Zend_Exception("User by id " . $id . " not found");
        }
        $row = $result->current();
        $user->setOptions($row->toArray());
    }

    public function setLastVisit($userId, $date)
    {
        $where = $this->getDbTable()->getDefaultAdapter()
                 ->quoteInto('id = ?', $userId);
        $this->getDbTable()->update(array('lastvisitDate' => $date), $where);
    }

    /**
     * Finds user by username, returns null if nothing was found
     *
     * @param string $name
     * @return Admin_Model_User|null
     */
    private function findByUsername($name)
    {
        $row = $this->getDbTable()->fetchRow(
               $this->getDbTable()->getDefaultAdapter()
                    ->quoteInto('username = ?', $name));
        if(null !== $row) {
            return new Admin_Model_User($row->toArray());
        }
        return null;
    }
}
