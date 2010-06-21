<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of ContentNodeMapper
 *
 * @author miholeus
 */
class Admin_Model_ContentNodeMapper extends Admin_Model_DataMapper_Abstract
{
    /**
     *
     * @var Admin_Model_DbTable_ContentNode
     */
    protected $_dbTableClass = 'Admin_Model_DbTable_ContentNode';

    public function loadNodeInfo(Admin_Model_ContentNode $node)
    {
        $result = $this->getDbTable()->select()
                ->where("name = ?", $node->getName())
                ->where("page_id = ?", $node->getPageId());

        $row = $this->getDbTable()->fetchRow($result);

        if(!$row) {
            throw new Zend_Exception("Node with name " . $node->getName()
                    . " on page " . $node->getPageId() . " was not setted up!");
        }

        $node->setId($row->id)
             ->setName($row->name)
             ->setValue($row->value)
             ->setIsInvokable($row->isInvokable)
             ->setParams($row->params)
             ->setPageId($row->page_id);
    }
}
