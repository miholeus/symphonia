<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Soulex_Components_Events_EventsService is the main class for manipulation
 * events objects.
 * It grants common interface for Events
 *
 * @author miholeus
 */
class Soulex_Components_Events_EventsService
{
    /**
     *
     * @var Soulex_Components_Events_EventsModel
     */
    protected $_events;

    public function __construct($options = null)
    {
        $this->_events = new Soulex_Components_Events_EventsModel($options);
    }
    /**
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
     * @return array which contains Soulex_Components_Events_EventsModel objects
     */
    public function fetchAll($where = null, $order = null, $limit = null, $offset = null)
    {
        return $this->_events->fetchAll($where, $order, $limit, $offset);
    }
    /**
     *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @return Zend_Paginator_Adapter_DbSelect
     */
    public function fetchPaginator($where = null, $order = null)
    {
        return $this->_events->fetchPaginator($where, $order);
    }
    /**
     * Finds object in data source by its id
     *
     * @param int $id
     * @return Soulex_Components_Events_EventsModel|null
     */
    public function findById($id)
    {
        return $this->_events->find($id);
    }
    /**
     * Saves events in data source
     *
     * @return Soulex_Components_Events_EventsModel
     */
    public function save()
    {
        return $this->_events->save();
    }
    /**
     * Bulk deletion of events
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
     * Deletes events in data source by id
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        $this->_events->delete($id);
    }
    /**
     * @see Soulex_Model_Events::setOptions()
     *
     */
    public function setOptions($options)
    {
        $this->_events->setOptions($options);
    }
    /**
     * Selects published status for events
     *
     * @param bool $published
     * @return Soulex_Components_Events_EventsService
     */
    public function selectEnabled($published)
    {
        $this->_events->selectPublished($published);
        return $this;
    }

    public function search($searchValue)
    {
        $this->_events->search($searchValue);
        return $this;
    }
    /**
     *
     * @param string $spec the column and direction to sort by
     * @return Soulex_Components_Events_EventsService
     */
    public function order($spec)
    {
        $this->_events->order($spec);
        return $this;
    }
    /**
     *
     * @return Zend_Paginator
     */
    public function paginate()
    {
        return $this->_events->paginate();
    }
}
