<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of MenuItem
 *
 * @author miholeus
 */
class Admin_Model_DbTable_MenuItem extends Zend_Db_Table_Abstract
{
    protected $_name = 'menu_items';
    /**
     * Insert new row
     * 
     * @param array $data
     * @return int inserted $rowId
     */
    public function _insert(array $data)
    {
        $lft = 0;//top level element
		$level = 0;//top level of parent
        $parent_id = 0;
        
        if(isset($data['parent_id'])) {
            $parent_id = $data['parent_id'];
        }
        if(isset($data['level'])) {
            $level = $data['level'];
        }
        if(isset($data['lft'])) {
            $lft = $data['lft'];
        }

        $tree_data = array(
            'lft' => $lft + 1,
            'rgt' => $lft + 2,
            'level' => $level + 1,
            'parent_id' => $parent_id
        );
        $data = array_merge($data, $tree_data);

        $this->_db->beginTransaction();
        
        try {
            $this->_db->query("UPDATE " . $this->_name . " SET rgt = rgt + 2 WHERE rgt > ?", $lft);
            $this->_db->query("UPDATE " . $this->_name . " SET lft = lft + 2 WHERE lft > ?", $lft);

            $this->insert($data);

            $rowId = $this->_db->lastInsertId();

            $this->_db->commit();
        } catch (Zend_Exception $e) {
            $this->_db->rollBack();
        }

        return $rowId;
    }
    /**
     * Update current row
     * 
     * @param array $data
     * @param  array|string $where An SQL WHERE clause, or an array of SQL WHERE clauses.
     */
    public function _update(array $data, $where, $rgtKey)
    {
        $row = $this->fetchRow($where);

        $level  = $row->level;
        $left_key    = $row->lft;
        $right_key    = $row->rgt;

        // move leaf to another node
        if($data['parent_id'] != $row->parent_id) {
            $level_up = $data['level'];// level of new parent node
            // right Key of node beside which we move current node
            // parent node is changed
            $right_key = $rgtKey - 1;
            // @todo if parent is not changed, node is placed after another sibling
            // we need to define $left_key
            if(0 === $rgtKey) {// move to root node
                $maxRgtKeyRow = $this->findMaxRightKey();
                $right_key = $maxRgtKeyRow['max_right'];
            }

            // move node to upper level
            if($level_up < $level) {
                $parentRgtKeyRow = $this->findParentRightKey($row->parent_id);
                $right_key = $parentRgtKeyRow['rgt'];
            }

            $skew_level = $level_up - $level + 1; // moving node offset
            $skew_tree  = $right_key - $left_key; // tree keys offset

//            var_dump($left_key);echo '<br />rgt: <br />';var_dump($right_key);echo '<br />lvl up:<br />';
//            var_dump($level_up);echo '<br />lvl: <br />';var_dump($level);
//

            throw new Zend_Exception('You can not move nodes to another parent.'
                    . ' This feature is not realized yet. Sorry :(');
        }

        $row->setFromArray($data);

        $row->save();
//        $this->update($data, $where);
    }
    /**
     * Delete row by its id
     *
     * @param int $id
     */
    public function _delete($id)
    {
       $this->_db->beginTransaction();

       try {
           $row = $this->find($id)->current();
           if(!$row) {
               throw new Zend_Exception('Menu Item with ID ' . $id . ' not found!');
           }
           
           $lft = $row->lft;
           $rgt = $row->rgt;
           $width = $rgt - $lft + 1;

           $this->delete('lft BETWEEN ' . $lft . ' AND ' . $rgt);

           $this->_db->query("UPDATE " . $this->_name . " SET rgt = rgt - "
                   . $width . " WHERE rgt > ?", $rgt);
           $this->_db->query("UPDATE " . $this->_name . " SET lft = lft - "
                   . $width . " WHERE lft > ?", $rgt);

           $this->_db->commit();

       } catch (Zend_Exception $e) {
           $this->_db->rollBack();
       }

    }
    /**
     *
     * @return Zend_Db_Table_Row_Abstract
     */
    public function findMaxLevel()
    {
        $select = $this->select()->from(array($this->_name), array( 'max_level' => 'MAX(level)'));
        return $this->fetchRow($select);
    }
    /**
     * Find MAX right key value of tree
     * 
     * @return Zend_Db_Table_Row_Abstract
     */
    private function findMaxRightKey()
    {
        $select = $this->select()->from(
            array($this->_name),
            array( 'max_right' => 'MAX(rgt)')
        );
        return $this->fetchRow($select);
    }
    /**
     * Find Right Key of Parent Node
     * 
     * @param int $id of parent node
     * @return Zend_Db_Table_Row_Abstract
     */
    private function findParentRightKey($id)
    {
        $select = $this->select()
                ->from(array($this->_name), array("rgt"))
                ->where("id = ?" , $id);
        return $this->fetchRow($select);
    }
}
