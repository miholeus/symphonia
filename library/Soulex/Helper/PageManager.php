<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Page Manager deals with page's data and loads content nodes on page
 *
 * @author miholeus
 */
class Soulex_Helper_PageManager
{
    /**
     * Page's identifier
     *
     * @var int
     */
    protected $_pageId;
    /**
     *
     * @var Zend_Controller_Action_HelperBroker
     */
    protected $_helper;
    /**
     * Nodes that will not be loaded on page
     * 
     * @var array 
     */
    protected $_disabledNodes = array();
    public function __construct($pageId, Zend_Controller_Action_HelperBroker $helper)
    {
        $this->_pageId = $pageId;
        $this->_helper = $helper;
    }
    /**
     * Exclude Nodes from loading on page
     * 
     * @param array $nodes 
     */
    public function excludeNodes($nodes)
    {
        if(is_array($nodes)) {
            foreach($nodes as $node) {
                $this->_disabledNodes[] = $node;
            }
        } else {
            $this->_disabledNodes[] = $nodes;
        }
    }
    /**
     * Load nodes on page
     */
    public function loadNodes()
    {
        $nodesMapper = new Model_ContentNode();
        $nodes = $nodesMapper->getPageNodes($this->_pageId, $this->getDisabledNodes());
        if(count($nodes) > 0) {
            // installs static nodes on page with their values
            foreach($nodes as $nodeData) {
                if($nodeData['isInvokable'] == 1) {
                    $value = unserialize($nodeData['value']);
                    $this->_helper->actionStack($value['action'],
                            $value['controller'],
                            $value['module'], array(
                            '_responseSegment' => $nodeData['name']
                    ));
                } else {
                    $value = $nodeData['value'];
                    $this->_helper->layout()->$nodeData['name'] = $value;
                }
            }
        }
    }
    /**
     *
     * @return array disabled nodes 
     */
    protected function getDisabledNodes()
    {
        return $this->_disabledNodes;
    }
}