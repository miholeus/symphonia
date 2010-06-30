<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_Menu Types
 *
 * @author miholeus
 */
class Admin_Model_Menu extends Admin_Model_Abstract
{
    protected $_id;
    protected $_title;
    protected $_menutype;
    protected $_description;
    /**
     *
     * @var Admin_Model_MenuMapper
     */
    protected $_mapperClass = 'Admin_Model_MenuMapper';

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function getTitle()
    {
        return $this->_title;
    }

    public function setTitle($title)
    {
        $this->_title = $title;
        return $this;
    }

    public function getMenutype()
    {
        return $this->_menutype;
    }

    public function setMenutype($menutype)
    {
        $this->_menutype = $menutype;
        return $this;
    }

    public function getDescription()
    {
        return $this->_description;
    }

    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }
    /**
     *
     * @return Admin_Model_Menu
     */
    public function save()
    {
        return $this->getMapper()->save($this);
    }

	/**
	 * Fetches menus
	 *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @return array Admin_Model_Menu
	 */
    public function fetchAll($where = null, $order = null)
    {
        return $this->getMapper()->fetchAll($where, $order);
    }
    /**
     *
     * @param int $id
     * @return Admin_Model_Menu
     */
    public function find($id)
    {
        return $this->getMapper()->findById($id, $this);
    }

    public function delete($id)
    {
        $this->getMapper()->delete($id);
    }
}
