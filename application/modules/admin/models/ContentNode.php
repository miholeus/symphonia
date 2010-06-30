<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_ContentNode is model that manipulates content nodes
 * objects
 *
 * @author miholeus
 */
class Admin_Model_ContentNode extends Admin_Model_Abstract
{
    protected $_id;
    protected $_name;
    protected $_value;
    protected $_isInvokable;
    protected $_params;
    protected $_page_id;

    /**
     *
     * @var Admin_Model_ContentNodeMapper
     */
    protected $_mapperClass = 'Admin_Model_ContentNodeMapper';

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    public function getValue()
    {
        return $this->_value;
    }

    public function getContentValue($param = null)
    {
        if($this->getIsInvokable()) { // dynamic node
            $content = unserialize($this->value);
            return isset($content[$param]) ? $content[$param] : null;
        } else {
            // if node is dynamic and
            // param is setted up to module, controller, action
            return $param === null ? $this->value : null;
        }
    }

    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    public function getIsInvokable()
    {
        return $this->_isInvokable;
    }

    public function setIsInvokable($isInvokable)
    {
        $this->_isInvokable = $isInvokable;
        return $this;
    }

    public function getPageId()
    {
        return $this->_page_id;
    }

    public function setPageId($pageId)
    {
        $this->_page_id = $pageId;
        return $this;
    }

    public function getParams()
    {
        return unserialize($this->_params);
    }
    /**
     *
     * @param array $params
     * @return Admin_Model_ContentNode
     */
    public function setParams($params)
    {
        $this->_page_id = serialize($params);
        return $this;
    }
    /**
     *
     * @return Admin_Model_ContentNode
     */
    public function loadNode()
    {
        try {
            $this->getMapper()->loadNodeInfo($this);
        } catch (Zend_Exception $e) {
            
        }
        return $this;
    }
    /**
     * Copy node to pages $pagesToInsert and update node info
     * on pages $pagesToUpdate
     *
     * @param array $allPages
     * @return bool succeeded/not succeeded
     */
    public function copyToPages(array $allPages)
    {
        if(null === $this->getName()) {
            throw new Zend_Exception('Node name can not be null');
        }

        $pageIds = array();
        $currentPage = $this->getPageId();

        foreach($allPages as $page) {
            $pageIds[] = $page['id'];
        }
        // exclude current page id
        $pageIds = array_diff($pageIds, array($currentPage));

        $pagesToUpdate = $this->getMapper()->findPagesWhereNodeExists(
                $this->getName());
        // exclude current page id
        $pagesToUpdate = array_diff($pagesToUpdate, array($currentPage));

        $pagesToInsert = array_diff($pageIds, $pagesToUpdate);
        
        return $this->getMapper()->copyNodeToPages($this,
                $pagesToInsert, $pagesToUpdate);
    }
    /**
     * Delete node by its id
     *
     * @param int $id
     * @return bool true if node was deleted, false otherwise
     */
    public function delete($id)
    {
        return (bool)$this->getMapper()->delete($id);
    }
}
