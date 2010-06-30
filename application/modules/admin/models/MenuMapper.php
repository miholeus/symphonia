<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_MenuMapper maps Menu objects with database layer
 *
 * @author miholeus
 */
class Admin_Model_MenuMapper extends Admin_Model_DataMapper_Abstract
{
    /**
     *
     * @var Admin_Model_DbTable_Menu
     */
    protected $_dbTableClass = 'Admin_Model_DbTable_Menu';
	/**
	 * Fetches menus
	 *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @return array Admin_Model_Menu
	 */
    public function fetchAll($where, $order)
    {
        $menus = array();

        $select = $this->getDbTable()->select();

        if(null !== $where) {
            $select->where($where);
        }
        if(null !== $order) {
            $select->order($order);
        }

        $result = $this->getDbTable()->fetchAll($select);
        foreach($result as $current) {
            $menus[] = new Admin_Model_Menu($current->toArray());
        }
        
        return $menus;
    }

    public function save(Admin_Model_Menu $menu)
    {
        $data = array(
            'title'                 => $menu->getTitle(),
            'menutype'              => $menu->getMenutype(),
            'description'           => $menu->getDescription()
        );

        if (null === ($id = $menu->getId())) {
            $this->getDbTable()->insert($data);
            $insertedId = $this->getDbTable()->getDefaultAdapter()->lastInsertId();
            $menu->setId($insertedId);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
        return $menu;
    }

    public function findById($id, Admin_Model_Menu $menu)
    {
        $row = $this->getDbTable()->find($id)->current();
        if(null === $row) {
            throw new Zend_Exception('Menu with id ' . $id . ' not found!');
        }
        return new Admin_Model_Menu($row->toArray());
    }
    /**
     * Delete menu by id
     *
     * @param int $id
     */
    public function delete($id)
    {
        $where = $this->getDbTable()->getDefaultAdapter()->quoteInto('id = ?', $id);
        $this->getDbTable()->delete($where);
    }
}
