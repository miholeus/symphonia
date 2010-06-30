<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_Model_ContentNodeMapper is used to retrieve data in database
 * and send it to model or transfer model's data back to database
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

    public function copyNodeToPages(Admin_Model_ContentNode $node,
            array $pagesToInsert, array $pagesToUpdate)
    {
         $result = $this->getDbTable()->select()
                ->where("name = ?", $node->getName())
                ->where("page_id = ?", $node->getPageId());

        $row = $this->getDbTable()->fetchRow($result);

        $nodeValues = array(
            'name'          => $row->name,
            'value'         => $row->value,
            'params'        => $row->params,
            'isInvokable'   => $row->isInvokable
        );

        try {

            $this->getDbTable()->getAdapter()->beginTransaction();

            if (count($pagesToInsert) > 0) {
                foreach ($pagesToInsert as $pageId) {
                    $_data = array_merge($nodeValues, array('page_id' => $pageId));
                    $this->getDbTable()->insert($_data);
                }
            }

            // update existing pages
            if (count($pagesToUpdate) > 0) {
                $this->getDbTable()->update(
                        array_diff($nodeValues, array('name' => $row->name)),
                        $this->getDbTable()->getAdapter()->quoteInto('name = ?', $row->name)
                        . ' AND page_id IN (' . join(',', $pagesToUpdate) . ')'
                );
            }

            $this->getDbTable()->getAdapter()->commit();

            return true;

        } catch (Zend_Exception $exc) {
            // echo $exc->getTraceAsString();
            $this->getDbTable()->getAdapter()->rollBack();
        }

        return false;
    }
    /**
     * Find pages where node $nodeName exists and return their ids
     *
     * @param string $nodeName
     * @return array $pageIds
     */
    public function findPagesWhereNodeExists($nodeName)
    {
        $pageIds = array();
        $select = $this->getDbTable()->select()->where('name = ?', $nodeName);
        $result = $this->getDbTable()->fetchAll($select)->toArray();

        if(is_array($result) && count($result) > 0) {
            foreach($result as $current) {
                $pageIds[] = $current['page_id'];
            }
        }
        return $pageIds;
    }
    /**
     * Delete node by its id
     *
     * @param int $id
     * @return int 1/0 number of deleted rows
     */
    public function delete($id)
    {
        $where = $this->getDbTable()->getAdapter()->quoteInto('id = ?', $id);
        return $this->getDbTable()->delete($where);
    }
}
