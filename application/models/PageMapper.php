<?php
/**
 * Description of Model_Page
 *
 * @author miholeus
 */

class Model_PageMapper extends Zend_Db_Table_Abstract
{
	protected $_name = 'pages';
	
	protected $_dependentTables = 'Model_ContentNode';
	/*
	protected $_referenceMap = array(
		'Page' => array(
			'columns' => 'parent_id',
			'refTableClass' => 'Model_page',
			'refColumns' => array('id'),
			'onDelete' => self::CASCADE,
			'onUpdate' => self::RESTRICT
		)
	);
	*/
	/**
	 * Creates new page
	 * 
	 * @uses Model_ContentNode
	 * 
	 * @param string $title
	 * @param string $uri
	 * @param string $meta_keywords
	 * @param string $meta_description
     * @param int $published
	 * @param string $content
	 * @return int id of created page
	 */
	public function createPage($title, $uri, $meta_keywords, $meta_description,
            $published, $content)
	{
		$lft = 0;//top level page
		$level = 0;//top level of parent
		
		$this->_db->beginTransaction();
		$this->_db->query("UPDATE " . $this->_name . " SET rgt = rgt + 2 WHERE rgt > ?", $lft);
		$this->_db->query("UPDATE " . $this->_name . " SET lft = lft + 2 WHERE lft > ?", $lft);
		
		$row = $this->createRow();
		$row->title = $title;
		$row->uri = $uri;
		$row->meta_keywords = $meta_keywords;
		$row->meta_description = $meta_description;
        $row->published = $published;
		$row->lft = $lft + 1;
		$row->rgt = $lft + 2;
		$row->level = $level + 1;
		$row->save();
		
		$pageId = $this->_db->lastInsertId();
		
		$this->_db->commit();

		$mdlContentNode = new Model_ContentNode();
		$mdlContentNode->createNode('content', $content, $pageId);
		
		return $pageId;
	}
	/**
	 * Updates selected page
	 * 
	 * @param int $pageId
	 * @param array $data
	 * @return void
	 */
	public function updatePage($pageId, $data)
	{
		$row = $this->find($pageId)->current();
		if($row) {
			$row->title = $data['title'];
			$row->uri = $data['uri'];
			$row->meta_keywords = $data['meta_keywords'];
			$row->meta_description = $data['meta_description'];
            $row->published = $data['published'];
			$row->save();
			
			unset($data['title'], $data['uri'], $data['meta_keywords'],
				$data['meta_description'], $data['published']);
            unset($data['id'], $data['submit']);// remove form id and submit button

            $mdlContentNode = new Model_ContentNode();
            // saving nodes data
            if(isset($data['nodes']) && is_array($data['nodes'])
                    && count($data['nodes']) > 0) {
                foreach($data['nodes'] as $nodeName => $nodeData) {
                    if($nodeData['type'] == 1) {// dynamic node
                        $_nodeData = array('module' => $nodeData['module'],
                            'controller' => $nodeData['controller'],
                            'action' => $nodeData['action']);
                        $mdlContentNode->setNode($pageId,
                                $nodeName,
                                serialize($_nodeData),
                                $nodeData['type']
                        );
                    } else { // static node
                        $mdlContentNode->setNode($pageId,
                                $nodeName,
                                $data[$nodeName],
                                $nodeData['type']
                        );
                    }
                }
            }


//            $pageNodes = $mdlContentNode->getPageNodes($pageId);
//
//            foreach($pageNodes->toArray() as $node) {
//                if(!array_key_exists($node['name'], $data)) {
//                    // remove unnessesary node
//                    $mdlContentNode->delete($this->getWhere($node['id']));
//                } else { // update node value
//                    $mdlContentNode->setNode($pageId, $node['name'], $data[$node['name']]);
//                }
//                unset($data[$node['name']]);
//            }
//
//            if(count($data) > 0) {
//                // adding new nodes
//                foreach($data as $key => $value) {
//                    $mdlContentNode->setNode($pageId, $key, $value);
//                }
//            }

		} else {
			throw new Zend_Exception('Update failed: no page found!');
		}
	}
	/**
	 * Deletes page
	 * 
	 * @param int $id of page that will be deleted
	 * @return true if success
	 */
	public function deletePage($id)
	{
		$this->_db->beginTransaction();
		$row = $this->find($id)->current();
		if($row) {
			$myLeft = $row->lft;
			$myRight = $row->rgt;
			$myWidth = $myRight - $myLeft + 1;
						
			$this->_db->query("DELETE FROM " . $this->_name . " WHERE lft BETWEEN ? AND ?",
					array($myLeft, $myRight)
			);
			$this->_db->query("UPDATE " . $this->_name . " SET rgt = rgt - ? WHERE rgt > ?",
					array($myWidth, $myRight)
			);
			$this->_db->query("UPDATE " . $this->_name . " SET lft = lft - ? WHERE lft > ?",
					array($myWidth, $myRight)
			);
			
			$this->_db->commit();
			
			return true;
		} else {
			$this->_db->rollBack();
			throw new Zend_Exception('Delete failed: no page found!');
		}		
	}
	
	public function findPage($id, $where)
	{
		$row = $this->find($id)->current();

		if($row) {
			$pageFields = $row->toArray();
			$mdlContentNode = new Model_ContentNode();
			$contentNodes = $row->findDependentRowset($mdlContentNode);
			$pageContent = array();
			if(count($contentNodes) > 0) {
				foreach($contentNodes as $node) { //looking for content fields
					$pageContent[$node['name']] = array(
                        'id'            => $node['id'],
                        'value'         => $node['value'],
                        'isInvokable'   => $node['isInvokable'],
                        'params'        => $node['params']
                    );
				}
			}
			return array_merge($pageFields, array('_data' => $pageContent));
		} else {
			return null;
		}
	}

    private function getWhere($id)
    {
        return $this->getAdapter()->quoteInto("id = ?", $id);
    }
    /**
     * Fetch all pages
     * 
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function fetchAllPages($where, $order)
    {
        $select = $this->select();
        if(null !== $where) {
            $select->where($where);
        }
        if(null !== $order) {
            $select->order($order);
        }

        return $this->fetchAll($select);
    }
}