<?php
/**
 * Description of Model_ContentNode
 *
 * @author miholeus
 */

class Model_ContentNode extends Zend_Db_Table_Abstract
{
    /**
     * The default table name
     */
    protected $_name = 'content_nodes';
	
	protected $_referenceMap = array(
		'Page' => array(
			'columns' => array('page_id'),
			'refTableClass' => 'Model_PageMapper',
			'refColumns' => array('id'),
			'onDelete' => self::CASCADE,
			'onUpdate' => self::RESTRICT
		)
	);
	
	public function createNode($name, $value, $page_id)
	{
		$row = $this->createRow();
		$row->name = $name;
		$row->value = $value;
		$row->page_id = $page_id;
		$row->save();
	}
	
	public function setNode($pageId, $node, $value, $type = 0)
	{
	    // fetch the row if it exists
	    $select = $this->select();
	    $select->where("page_id = ?", $pageId);
	    $select->where("name = ?", $node);
	    $row = $this->fetchRow($select);
	    //if it does not then create it
	    if(!$row) {
	        $row = $this->createRow();
	        $row->page_id = $pageId;
	        $row->name = $node;
	    }
	    //set the content
	    $row->value = $value;
        $row->isInvokable = $type;

	    $row->save();
	}

    public function getPageNodes($pageId)
    {
        // select page nodes
        $select = $this->select();
        $select->where("page_id = ?", $pageId);
        return $this->fetchAll($select);
    }
}