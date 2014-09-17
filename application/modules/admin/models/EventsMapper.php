<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_EventsMapper
 *
 * @author miholeus
 */
class Admin_Model_EventsMapper extends Admin_Model_DataMapper_Abstract
{
    /**
     *
     * @var Admin_Model_DbTable_Events
     */
    protected $_dbTableClass = 'Admin_Model_DbTable_Events';

    public function save(Admin_Model_Events $Events)
    {
        $data = array(
            'title'                 => $Events->getTitle(),
            'short_description'     => $Events->getShortDescription(),
            'detail_description'    => $Events->getDetailDescription(),
            'img_preview'           => $Events->getImgPreview(),
            'published'             => $Events->getPublished(),
            'created_at'            => $Events->getCreatedAt(),
            'updated_at'            => $Events->getUpdatedAt(),
            'published_at'          => $Events->getPublishedAt()
        );

        if (null === ($id = $Events->getId())) {
            $this->getDbTable()->insert($data);
            $insertedId = $this->getDbTable()->getDefaultAdapter()->lastInsertId();
            $Events->setId($insertedId);
        } else {
            $this->getDbTable()->update($data, array('id = ?' => $id));
        }
        return $Events;
    }
    public function find($id, Admin_Model_Events $Events)
    {
        $result = $this->getDbTable()->find($id);
        if (0 == count($result)) {
            throw new Zend_Exception("Events by id " . $id . " not found");
        }
        $row = $result->current();
        $Events->setId($row->id)
                  ->setTitle($row->title)
                  ->setShortDescription($row->short_description)
                  ->setDetailDescription($row->detail_description)
                  ->setImgPreview($row->img_preview)
                  ->setPublished($row->published)
                  ->setCreatedAt($row->created_at)
                  ->setUpdatedAt($row->updated_at)
                  ->setPublishedAt($row->published_at);
    }
    /**
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
     * @return Admin_Model_Events all set
     */
    public function fetchAll($where = null, $order = null, $count = null, $offset = null)
    {
        $resultSet = $this->getDbTable()->fetchAll($where, $order, $count, $offset);
        $entries   = array();
        foreach ($resultSet as $row) {
            $entry = new Admin_Model_Events();
            $entry->setId($row->id)
                  ->setTitle($row->title)
                  ->setShortDescription($row->short_description)
                  ->setDetailDescription($row->detail_description)
                  ->setImgPreview($row->img_preview)
                  ->setPublished($row->published)
                  ->setCreatedAt($row->created_at)
                  ->setUpdatedAt($row->updated_at)
                  ->setPublishedAt($row->published_at)
                  ->setMapper($this);
            $entries[] = $entry;
        }
        return $entries;
    }

    public function delete($id)
    {
        $where = $this->getDbTable()->getDefaultAdapter()->quoteInto('id = ?', $id);
        $this->getDbTable()->delete($where);
    }
    /**
     * Sets published in where clause
     *
     * @param int $published
     * @return void
     */
    public function published($published)
    {
        $this->_select = $this->getSelect();
        $this->_select->where('published = ?', $published);
    }
    /**
     * Simple search by title field using like operator
     *
     * @param string $value search value
     * @return void
     */
    public function search($value)
    {
        $this->_select = $this->getSelect();
        $this->_select->where('title LIKE ?', '%' . $value . '%');
    }
}
