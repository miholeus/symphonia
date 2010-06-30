<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_MenuItemMapper is a mapper for Admin_Model_MenuItem
 *
 * @author miholeus
 */
class Admin_Model_MenuItemMapper extends Admin_Model_DataMapper_Abstract
{
    /**
     *
     * @var Admin_Model_DbTable_MenuItem
     */
    protected $_dbTableClass = 'Admin_Model_DbTable_MenuItem';

	/**
	 * Fetches menus
	 *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @return array Admin_Model_MenuItem
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
            $menus[] = new Admin_Model_MenuItem($current->toArray());
        }

        return $menus;
    }
    /**
     *
     * @param Admin_Model_MenuItem $menu
     * @return Admin_Model_MenuItem
     */
    public function save(Admin_Model_MenuItem $menu)
    {
        $data = array(
            'menu_id'                => $menu->getMenuId(),
            'label'                 => $menu->getLabel(),
            'uri'                   => $menu->getUri(),
            'position'              => $menu->getPosition(),
            'published'             => $menu->getPublished(),
            'parent_id'             => $menu->getParentId()
        );

        
        if (null === ($id = $menu->getId())) {
            
            if(0 != $menu->getParentId()) {
                $parentMenuClass = get_class($menu);
                $parentMenu = new $parentMenuClass;
                $parentMenu->find($menu->getParentId());

                $data['level'] = $parentMenu->getLevel();
                $data['lft'] = $parentMenu->getLft();

                $rgtKey = $parentMenu->getRgt();
            } else {
                $rgtKey = 0;
            }

            $insertedId = $this->getDbTable()->_insert($data);
            $menu->setId($insertedId);
        } else {
            $this->getDbTable()->_update($data, array('id = ?' => $id), $rgtKey);
        }
        return $menu;
    }
    /**
     * Find menu item by its id
     * 
     * @param int $id
     * @param Admin_Model_MenuItem $menu
     * @return Admin_Model_MenuItem $menu
     */
    public function findById($id, Admin_Model_MenuItem $menu)
    {
        $row = $this->getDbTable()->find($id)->current();
        if(null === $row) {
            throw new Zend_Exception('Menu with id ' . $id . ' not found!');
        }
        $menu->setOptions($row->toArray());
        return $menu;
    }
    /**
     * @return int max level
     */
    public function findMaxLevel()
    {
        $row = $this->getDbTable()->findMaxLevel();
        return $row['max_level'];
    }
    /**
     * Delete Menu Item by its id
     *
     * @param int $id
     */
    public function delete($id)
    {
        $this->getDbTable()->_delete($id);
    }
}
