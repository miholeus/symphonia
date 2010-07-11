<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_Events
 *
 * @author miholeus
 */
class Admin_Model_Events extends Admin_Model_Abstract
{
    protected $_id;
    protected $_title;
    protected $_short_description;
    protected $_detail_description;
    protected $_img_preview;
    protected $_published;
    protected $_created_at;
    protected $_updated_at;
    protected $_published_at;
    /**
     *
     * @var Admin_Model_EventsMapper
     */
    protected $_mapperClass = 'Admin_Model_EventsMapper';

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

    public function setTitle($value)
    {
        $this->_title = $value;
        return $this;
    }

    public function getShortDescription()
    {
        return $this->_short_description;
    }

    public function setShortDescription($description)
    {
        $this->_short_description = $description;
        return $this;
    }

    public function getDetailDescription()
    {
        return $this->_detail_description;
    }

    public function setDetailDescription($description)
    {
        $this->_detail_description = $description;
        return $this;
    }

    public function setImgPreview($img_src)
    {
        $this->_img_preview = $img_src;
        return $this;
    }

    public function getImgPreview()
    {
        return $this->_img_preview;
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

    public function getCreatedAt()
    {
        return $this->_created_at;
    }

    public function setCreatedAt($created_time)
    {
        $this->_created_at = $created_time;
        return $this;
    }

    public function getUpdatedAt()
    {
        return $this->_updated_at;
    }

    public function setUpdatedAt($updated_time)
    {
        $this->_updated_at = $updated_time;
        return $this;
    }

    public function getPublishedAt()
    {
        return $this->_published_at;
    }

    public function setPublishedAt($published_time)
    {
        $this->_published_at = $published_time;
        return $this;
    }

    public function save()
    {
        return $this->getMapper()->save($this);
    }
    /**
     * Finds data row by id and returns new object
     * If object was not found then we set initial null values
     * to object
     *
     * @param int $id
     * @return Admin_Model_Events|null
     */
    public function find($id)
    {
        try {
            $this->getMapper()->find($id, $this);
        } catch (Zend_Exception $e) {
            return null;
//            // delete object data
//            foreach(get_class_vars(__CLASS__) as $varname => $varvalue) {
//                if($varname == "_mapper") {
//                    continue;
//                }
//                $this->$varname = null;
//            }
        }
        return $this;
    }
    /**
     *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int
     * @return Admin_Model_Events
     */
    public function fetchAll($where = null, $order = null, $limit = null, $offset = null)
    {
        return $this->getMapper()->fetchAll($where, $order, $limit, $offset);
    }
    /**
     *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @return Zend_Paginator_Adapter_DbSelect
     */
    public function fetchPaginator($where, $order)
    {
        return $this->getMapper()->fetchPaginator($where, $order);
    }
    /**
     * Delete menu by it's id
     *
     * @param int $id
     */
    public function delete($id)
    {
        $this->getMapper()->delete($id);
    }


}
