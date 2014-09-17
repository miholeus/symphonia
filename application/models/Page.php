<?php
/**
 * Page objects manipulation
 * Model_Page has its own mapper for retrieving data in database
 * and router for route managemant operations.
 * CRUD operations are delegated to mapper class.
 *
 * @author miholeus
 *
 */
class Model_Page
{
	/**
	 * @var Model_PageMapper
	 */
	protected $_mapper = null;
	/**
	 * @var Model_PageRouter
	 */
	protected $_pageRouter = null;
	
	public function __construct()
	{
		$this->_mapper = new Model_PageMapper();
		$this->_pageRouter = Admin_Model_PageRouter::getInstance();
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
        $select = $this->_mapper->select();
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
     * Fetch all page objects
     *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @return array $pages
     */
    public function fetchAll($where = null, $order = null)
    {
        $pages = $this->_mapper->fetchAllPages($where, $order)->toArray();

        return $pages;
    }
	
	public function find($id, $where = null)
	{
		return $this->_mapper->findPage($id, $where);
	}
	
	
	public function update($id, $data)
	{

		//@todo double find action
		$page = $this->_mapper->find($id)->current();

		if($page) {
			$uri = $page->uri;
		} else {
			throw new Zend_Exception('Delete page: no page found!');
		}
		
		$this->_mapper->updatePage($id, $data);
		
		$this->_pageRouter->updateRoute($id, $data['uri'], $uri);
	}
	
	public function create($title, $uri, $meta_keywords, $meta_description, $published, $content)
	{
		$pageId = $this->_mapper->createPage($title, $uri, $meta_keywords, $meta_description, $published, $content);
		
		$this->_pageRouter->createRoute($pageId, $uri);
		
		return $pageId;
	}
    /**
     * Bulk page deletion
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
	
	public function delete($id)
	{	
		//@todo double find action
		$page = $this->_mapper->find($id)->current();
		if($page) {
			$uri = $page->uri;
		} else {
			throw new Zend_Exception('Delete page: no page found!');
		}
		
		$this->_pageRouter->deleteRoute($uri);
		
		return $this->_mapper->deletePage($id);
	}

    public function findPagesToInstallNode($nodeName)
    {
        $select = $this->_mapper->select()->where('name = ?', $nodeName);

        $result = $this->_mapper->fetchAll($select);
    }
    /**
     * Selects publish status for pages
     *
     * @param bool $published
     * @return Model_Page
     */
    public function selectPublished($published)
    {
        /**
         * isset added to prevent the clause when page is updated
         * and $enabled value comes as null
         */
        if($published != '*' && isset($published)) {
            $published = (int)$published;
            $this->_mapper->published($published);
        }
        return $this;
    }

    public function search($searchValue)
    {
        if(!empty($searchValue)) {
            $searchValue = str_replace('\\', '\\\\', $searchValue);
            $searchValue = addcslashes($searchValue, '_%');
            $this->_mapper->search($searchValue);
        }
        return $this;
    }
    /**
     *
     * @param string $spec the column and direction to sort by
     * @return Model_Page
     */
    public function order($spec)
    {
        $this->_mapper->order($spec);
        return $this;
    }
    /**
     *
     * @return Zend_Paginator
     */
    public function paginate()
    {
        $adapter = $this->_mapper->fetchPaginator();
        return new Zend_Paginator($adapter);
    }

}