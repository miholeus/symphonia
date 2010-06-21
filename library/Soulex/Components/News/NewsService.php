<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of NewsService
 *
 * @author miholeus
 */
class Soulex_Components_News_NewsService
{
    /**
     *
     * @var Soulex_Components_News_NewsModel 
     */
    protected $_news;

    public function __construct($options = null)
    {
        $this->_news = new Soulex_Components_News_NewsModel($options);
    }
    /**
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int                               $offset OPTIONAL An SQL LIMIT offset.
     * @return array which contains Soulex_Components_News_NewsModel objects
     */
    public function fetchAll($where = null, $order = null, $limit = null, $offset = null)
    {
        return $this->_news->fetchAll($where, $order, $limit, $offset);
    }
    /**
     *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @return Zend_Paginator_Adapter_DbSelect
     */
    public function fetchPaginator($where = null, $order = null)
    {
        return $this->_news->fetchPaginator($where, $order);
    }

    public function fetch($limit, $order = null)
    {
        return $this->_news->fetchAll('published = 1', $order, $limit);
    }
    /**
     * Finds object in data source by its id
     *
     * @param int $id
     * @return Soulex_Components_News_NewsModel|null
     */
    public function findById($id)
    {
        return $this->_news->find($id);
    }
    /**
     * Saves news in data source
     *
     * @return Soulex_Components_News_NewsModel
     */
    public function save()
    {
        return $this->_news->save();
    }
    /**
     * Bulk deletion of news
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
     * Deletes news in data source by id
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        $this->_news->delete($id);
    }
    /**
     * @see Soulex_Model_News::setOptions()
     * 
     */
    public function setOptions($options)
    {
        $this->_news->setOptions($options);
    }
}
