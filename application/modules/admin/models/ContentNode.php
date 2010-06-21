<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of ContentNode
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
}
