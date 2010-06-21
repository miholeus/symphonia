<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * MenuItem
 *
 * @author miholeus
 */
class Admin_Model_MenuItem extends Admin_Model_Abstract
{
    protected $_id;
    protected $_menu_id;
    protected $_label;
    protected $_uri;
    protected $_position;
    protected $_published;
    protected $_lft;
    protected $_rgt;
    protected $_parent_id;
    protected $_level;
    /**
     *
     * @var Admin_Model_MenuItemMapper
     */
    protected $_mapperClass = 'Admin_Model_MenuItemMapper';

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function getLabel()
    {
        return $this->_label;
    }

    public function setLabel($label)
    {
        $this->_label = $label;
        return $this;
    }

    public function getMenuId()
    {
        return $this->_menu_id;
    }

    public function setMenuId($menu_id)
    {
        $this->_menu_id = $menu_id;
        return $this;
    }

    public function getUri()
    {
        return $this->_uri;
    }

    public function setUri($uri)
    {
        $this->_uri = $uri;
        return $this;
    }

    public function getPosition()
    {
        return $this->_position;
    }

    public function setPosition($position)
    {
        $this->_position = $position;
        return $this;
    }

    public function getPublished()
    {
        return $this->_published;
    }

    public function setPublished($published)
    {
        $this->_published = $published;
        return $this;
    }

    public function getParentId()
    {
        return $this->_parent_id;
    }

    public function setParentId($parentId)
    {
        $this->_parent_id = $parentId;
        return $this;
    }

    public function getLft()
    {
        return $this->_lft;
    }

    public function setLft($lft)
    {
        $this->_lft = $lft;
        return $this;
    }

    public function getRgt()
    {
        return $this->_rgt;
    }

    public function setRgt($rgt)
    {
        $this->_rgt = $rgt;
        return $this;
    }

    public function getLevel()
    {
        return $this->_level;
    }

    public function setLevel($level)
    {
        $this->_level = $level;
        return $this;
    }
    /**
     * Fetch Menu Items
     * 
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @return array Admin_Model_MenuItem
     */
    public function fetchAll($where = null, $order = null)
    {
        return $this->getMapper()->fetchAll($where, $order);
    }
    /**
     * Fetch Menu Items groupped by parent ids
     *
     * @return array Admin_Model_MenuItem
     */
    public function fetchAllGrouppedByParentId($where = null, $order = null)
    {
        $menuItems = $this->fetchAll($where, $order);
        $items = array();
        foreach($menuItems as $item) {
            $parentId = $item->getParentId();
            $items[$parentId][] = $item;
        }
        return $items;
    }
    /**
     * Recursively add items to form's selectbox
     * 
     * @param array $items groupped by parent id
     * @param Admin_Form_Template_Simple $form
     * @param string $name of $form's selectbox
     * @param int $parentIdSelected OPTIONAL    Check $parentIdSelected option
     * @param int $parentId OPTIONAL    Start iterations on $parentId
     * @param int $lvl  OPTIONAL    Level of depth of iteratable item
     * @return void
     */
    public function processTreeElementForm($items, Admin_Form_Template_Simple $form,
            $name, $parentIdSelected = 0, $parentId = 0, $lvl = 1)
    {
        if(!isset($items[$parentId])) {
            return null;
        }

        foreach($items[$parentId] as $item) {
            $label = str_repeat('-', $lvl);
            $label .= $item->getLabel();
            $option = $form->addElementOption($name, $item->getId(), $label);
            if($item->getId() === $parentIdSelected) {
                $option->setValue($item->getId());
            }
            $this->processTreeElementForm($items, $form, $name, $parentIdSelected, $item->getId(), $lvl + 1);
        }
    }
	/**
	 * Fetches paginator
	 *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @return Zend_Paginator_Adapter_DbSelect
	 */
    public function fetchPaginator($where = null, $order = null)
    {
        $select = $this->getMapper()->getDbTable()->select();
        if(null !== $where) {
            $select->where($where);
        }
        if(null !== $order) {
            $select->order($order);
        }

        $adapter = new Zend_Paginator_Adapter_DbSelect($select);
        return $adapter;

    }
    /**
     * Find Menu Item by its id
     * 
     * @param int $id
     * @return Admin_Model_MenuItem
     */
    public function find($id)
    {
        return $this->getMapper()->findById($id, $this);
    }
    /**
     *
     * @return Admin_Model_MenuItem
     */
    public function save()
    {
        return $this->getMapper()->save($this);
    }
    /**
     * Maximum number of menu level
     *
     * @return int max menu level
     */
    public function findMaxMenuLevel()
    {
        return $this->getMapper()->findMaxLevel();
    }
    /**
     * Delete menu item(s) by id(s)
     *
     * @todo Rebuild Menu Items Nested set values
     * @param int|array $id
     */
    public function delete($id)
    {
        if(is_array($id)) {
            if(count($id) > 0) {
                foreach($id as $curId) {
                    $this->getMapper()->delete($curId);
                }
            }
        } else {
            $this->getMapper()->delete($id);
        }
    }
}
