<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Class for managing nested set trees
 * Add, delete, move nodes
 *
 * @author http://web-dev.info/
 * 
 * @see http://webcodes.ru/publ/1-1-0-5450
 * 
 */
/**
 * Update:
 * Left, right, level fields are independent
 * You may choose your own names
 *
 * @author miholeus
 */
class Soulex_Db_Table_Nestedset extends Zend_Db_Table
{

    const ERROR_NODE_NOT_EXIST = 19820;

    protected $_id = 'id';
    protected $_left = 'left';
    protected $_right = 'right';
    protected $_level = 'level';

    /**
     * Constructor
     *
     * @param array config
     */
    public function __construct($config = array()) {
        parent::__construct($config);
        $db = $this->getAdapter();
        $this->_left = $db->quoteIdentifier($this->_left);
        $this->_right = $db->quoteIdentifier($this->_right);
        $this->_level = $db->quoteIdentifier($this->_level);
    }

    /**
     * Unquote identifiers
     *
     * @param string quoted identifier
     * @return string unquoted identifier
     */
    protected function _unquoteIdentifier($identifier) {
        return preg_replace('/\W/i', '', $identifier);
    }

    /**
     * Clear table and prepare it to work by insert root node (id = 1, left = 1, right = 2, level = 0)
     *
     * @param array root properties
     * @return integer table primary key value
     * @throws Zend_Db_Table_Exception
     */
    public function clear($properties = array()){
        //truncate
        $this->getAdapter()->query('TRUNCATE TABLE ' . $this->_name);
        //create root
        $root = $this->createRow(array_merge($properties, array(
            $this->_unquoteIdentifier($this->_id) => 1,
            $this->_unquoteIdentifier($this->_left) => 1,
            $this->_unquoteIdentifier($this->_right) => 2,
            $this->_unquoteIdentifier($this->_level) => 0
        )));
        return $root->save();
    }

    /**
     * Returns a Left and Right IDs and Level of an node or null if node not exists
     *
     * @param integer node id
     * @return Zend_Db_Row_Abstract
     * @throws Zend_Db_Table_Exception
     */
    public function getNodeInfo($id){
        if (!$nodeInfo = $this->fetchRow($this->_id . ' = ' . (int)$id))
            throw new Zend_Db_Table_Exception('Can\'t fetch node row (id #' . $id . ')', self::ERROR_NODE_NOT_EXIST);
        else
            return $nodeInfo;
    }

    /**
     * Add new node
     *
     * @param integer parent node id
     * @param array node properties
     * @return table primary key value
     * @throws Zend_Db_Table_Exception
     */
    public function insertNode($id, $properties = array()){
        $parent = $this->getNodeInfo($id);
        
        $leftKey = $this->_unquoteIdentifier($this->_left);
        $rightKey = $this->_unquoteIdentifier($this->_right);
        $level = $this->_unquoteIdentifier($this->_level);

        try {
            $this->_db->beginTransaction();
            
            //prepare other nodes
            $this->getAdapter()->query('UPDATE ' . $this->_name . ' SET ' .
                    $this->_left . ' = IF (' . $this->_left . ' > ' . $parent->{$rightKey} . ', ' . $this->_left . ' + 2, ' . $this->_left . '), ' .
                    $this->_right . ' = IF (' . $this->_right . ' >= ' . $parent->{$rightKey} . ', ' . $this->_right . ' + 2, ' . $this->_right . ') ' .
                    'WHERE ' . $this->_right . ' >= ' . $parent->{$rightKey});

            //new node
            $newRow = $this->createRow(array_merge($properties, array(
                        $this->_unquoteIdentifier($this->_left) => $parent->{$rightKey},
                        $this->_unquoteIdentifier($this->_right) => $parent->{$rightKey} + 1,
                        $this->_unquoteIdentifier($this->_level) => $parent->{$level} + 1)));
            $insertedId = $newRow->save();
            
            $this->_db->commit();
            
            return $insertedId;
        } catch (Zend_Db_Exception $e) {
            $this->_db->rollBack();
            throw new $e;
        }
    }

    /**
     * Add new node after some another
     *
     * @param integer node id
     * @param array node properties
     * @return table primary key value
     * @throws Zend_Db_Table_Exception
     */
    public function insertNodeAfter($afterId, $properties = array()){
        $node = $this->getNodeInfo($afterId);
        
        $leftKey = $this->_unquoteIdentifier($this->_left);
        $rightKey = $this->_unquoteIdentifier($this->_right);
        $level = $this->_unquoteIdentifier($this->_level);

        try {
            $this->_db->beginTransaction();
            
            //prepare other nodes
            $this->getAdapter()->query('UPDATE ' . $this->_name . ' SET ' .
                    $this->_left . ' = IF (' . $this->_left . ' > ' . $node->{$rightKey} . ', ' . $this->_left . ' + 2, ' . $this->_left . '), ' .
                    $this->_right . ' = IF (' . $this->_right . ' > ' . $node->{$rightKey} . ', ' . $this->_right . ' + 2, ' . $this->_right . ') ' .
                    'WHERE ' . $this->_right . ' >= ' . $node->{$rightKey});

            //new node
            $newRow = $this->createRow(array_merge($properties, array(
                        $this->_unquoteIdentifier($this->_left) => $node->{$rightKey} + 1,
                        $this->_unquoteIdentifier($this->_right) => $node->{$rightKey} + 2,
                        $this->_unquoteIdentifier($this->_level) => $node->{$level})));
            $insertedId = $newRow->save();
            
            $this->_db->commit();
            
            return $insertedId;
        } catch (Zend_Db_Exception $e) {
            $this->_db->rollBack();
            throw new $e;
        }
    }

    /**
     * Delete node
     *
     * @param integer node id
     * @param boolean delete with childs
     * @throws Zend_Db_Table_Exception
     */
    public function deleteNode($id, $withChilds = true){
        $node = $this->getNodeInfo($id);
        if ($withChilds){
            $this->_deleteNodeWithChilds($id, $node);
        } else {
            $this->_deleteNodeWithoutChilds($id, $node);
        }
    }

    /**
     * Delete one node, without childs
     *
     * @param integer node id
     * @throws Zend_Db_Table_Exception
     */
    private function _deleteNodeWithoutChilds($id, $node){
        //delete node
        
        $leftKey = $this->_unquoteIdentifier($this->_left);
        $rightKey = $this->_unquoteIdentifier($this->_right);
        
        if ($this->delete($this->_id . ' = ' . (int)$id)){
            //update other nodes
            $this->getAdapter()->query('UPDATE ' . $this->_name . ' SET ' .
                $this->_left . ' = IF (' . $this->_left . ' BETWEEN ' . $node->{$leftKey} . ' AND ' . $node->{$rightKey} . ', ' . $this->_left . ' -1, ' . $this->_left . '), '.
                $this->_right . ' = IF (' . $this->_right . ' BETWEEN ' . $node->{$leftKey} . ' AND ' . $node->{$rightKey} . ', ' . $this->_right . ' -1, ' . $this->_right . '), ' .
                $this->_level . ' = IF (' . $this->_left . ' BETWEEN ' . $node->{$leftKey} . ' AND ' . $node->{$rightKey} . ', ' . $this->_level . ' -1, ' . $this->_level . '), ' .
                $this->_left . ' = IF (' . $this->_left . ' > ' . $node->{$rightKey} . ', ' . $this->_left . ' -2, ' . $this->_left . '), ' .
                $this->_right . ' = IF (' . $this->_right . ' > ' . $node->{$rightKey} . ', ' . $this->_right . ' -2, ' . $this->_right . ') ' .
                'WHERE ' . $this->_right . '>' . $node->{$leftKey}
            );
        } else {
            throw new Zend_Db_Table_Exception('Can\'t delete node row (id #' . $id . ')');
        }
    }

    /**
     *  Delete node, with childs
     *
     * @param integer node id
     * @throws Zend_Db_Table_Exception
     */
    private function _deleteNodeWithChilds($id, $node){
        //delete nodes
        
        $leftKey = $this->_unquoteIdentifier($this->_left);
        $rightKey = $this->_unquoteIdentifier($this->_right);
        
        if ($this->delete($this->_left . ' BETWEEN ' . $node->{$leftKey} . ' AND ' . $node->{$rightKey})){
            //update other nodes
            $deltaId = ($node->{$rightKey} - $node->{$leftKey}) + 1;
            $this->getAdapter()->query('UPDATE ' . $this->_name . ' SET ' .
                $this->_left . ' = IF(' . $this->_left . ' > ' . $node->{$leftKey} . ' , ' . $this->_left . ' - ' . $deltaId . ', '.$this->_left . '), ' .
                $this->_right . ' = IF(' . $this->_right . ' > ' . $node->{$leftKey} . ' , ' . $this->_right . ' - ' . $deltaId . ', '.$this->_right . ') ' .
                'WHERE ' . $this->_right . ' > ' . $node->{$rightKey}
            );
        } else {
            throw new Zend_Db_Table_Exception('Can\'t delete node row (id #' . $id . ')');
        }
    }

    /**
     * Return node childs
     *
     * If levelEnd isn't given, only children of levelStart levels are enumerated.
     * Level values should always be greater than zero.
     * Level 1 means direct children of the node
     *
     * @param integer node id
     * @param string|array $order order field name(s)
     * @param integer childs start level relative level from which start to enumerate children
     * @param integer childs end level the last relative level at which enumerate children
     * @return array
     * @throws Exception, Zend_Db_Table_Exception
     */
    public function getChildren($id, array $order = array(), $levelStart = 1, $levelEnd = 1){
        if ($levelStart < 0) throw new Exception('levelStart value can\'t be less zero');

        $where1 = ' AND ' . $this->_name . '.' . $this->_level;
        $where2 = '_' . $this->_name . '.' . $this->_level . ' + ';

        if(!$levelEnd) $whereSql = $where1 . ' >= ' . $where2 . (int)$levelStart;
        else {
            $whereSql = ($levelEnd <= $levelStart)
                ? $where1 . '=' . $where2 . (int)$levelStart
                : ' AND ' . $this->_name . '.' . $this->_level . ' BETWEEN _' . $this->_name . '.' . $this->_level . '+' . (int)$levelStart
                . ' AND _' . $this->_name . '.' . $this->_level . ' + ' . (int)$levelEnd;
        }

        $orderSql = array();
        foreach ($order as $val)
            $orderSql[] = $this->_name . '.' . $this->getAdapter()->quoteIdentifier($val);

        return $this->getAdapter()->query('SELECT * FROM ' .
            $this->_name . ' _' . $this->_name . ', ' . $this->_name . ' ' .
            'WHERE _' . $this->_name . '.' . $this->_id . ' = ' . (int)$id . ' AND ' .
            $this->_name . '.' . $this->_left . ' BETWEEN _' . $this->_name . '.' . $this->_left . ' AND _' . $this->_name . '.' . $this->_right .
            $whereSql .
            ' ORDER BY ' . $this->_name . '.' . $this->_level . (!empty($orderSql) ? ', ' . implode(', ', $orderSql) : '')
        )->fetchAll();
    }

    /**
     * Return "leveled" rowset array of rows with some level
     *
     * @param integer level
     * @return array rows
     */
    public function getByLevel($level){
        return $this->fetchAll($this->_level . ' = ' . (int)$level)->toArray();
    }

    /**
     * Return array of nodes from node to it's top level parent
     *
     * @param integer node id
     * @param boolean include root node
     * @return array nodes data
     */
    public function getPath($id, $withRoot = false){
        return $this->getAdapter()->query('SELECT * FROM ' .
            $this->_name . ' _' . $this->_name . ', ' . $this->_name . ' ' .
            'WHERE _' . $this->_name . '.' . $this->_id . ' = ' . (int)$id . ' AND ' .
            '_' . $this->_name . '.' . $this->_left . ' BETWEEN ' . $this->_name . '.' . $this->_left . ' AND ' . $this->_name . '.' . $this->_right .
            ($withRoot ? '' : ' AND ' . $this->_name . '.' . $this->_left . ' > 1') .
            ' ORDER BY ' . $this->_name . '.' . $this->_left
        )->fetchAll();
    }

    /**
     * Return parent row
     *
     * @param integer node id
     * @param integer relative level of parent
     * @return parent row as assoc array
     * @throws Exception
     */
    public function getParent($id, $level = 1) {
        if($level < 1) throw new Exception('level can\'t be less by one');

        return $this->getAdapter()->fetchRow('SELECT * FROM ' .
            $this->_name . ' _' . $this->_name . ', ' . $this->_name . ' ' .
            'WHERE _' . $this->_name . '.' . $this->_id . ' = ' . (int)$id . ' AND ' .
            '_' . $this->_name . '.' . $this->_left . ' BETWEEN ' . $this->_name . '.' . $this->_left . ' AND ' . $this->_name . '.' . $this->_right . ' AND ' .
            $this->_name . '.' . $this->_level . ' = _' . $this->_name . '.' . $this->_level . ' - ' . $level
        );
    }
    /**
     * Find MAX right key value of tree
     * 
     * @return int
     */
    private function findMaxRightKey()
    {
        $select = $this->select()->from(
            array($this->_name),
            array( 'max_right' => 'MAX(' . $this->_right . ')')
        );
        $row = $this->fetchRow($select);
        return $row['max_right'];
    }
    /**
     * Move node with all it's childs to another node
     *
     * @param integer id of moving node
     * @param integer id of new parent node
     * @return boolean operation status
     */
    public function moveNode($id, $newParentId) {
        $node = $this->getNodeInfo($id);
        $newParent = $this->getNodeInfo($newParentId);

        $leftKey = $this->_unquoteIdentifier($this->_left);
        $rightKey = $this->_unquoteIdentifier($this->_right);
        $level = $this->_unquoteIdentifier($this->_level);
        
        //nothing to move
        if ($id == $newParentId || $node->{$leftKey} == $newParent->{$leftKey}) return true;
        //it is imposible to move a high-level node in a low-level
        if ($newParent->{$leftKey} >= $node->{$leftKey} && $newParent->{$leftKey} <= $node->{$rightKey}) return false;

        if ($newParent->{$leftKey} < $node->{$leftKey} && $newParent->{$rightKey} > $node->{$rightKey} && $newParent->{$level} < $level - 1 ) {
            $sql = 'UPDATE ' . $this->_name . ' SET ' .
                $this->_level . ' = IF(' . $this->_left . ' BETWEEN ' . $node->{$leftKey} . ' AND ' . $node->{$rightKey} . ', ' . $this->_level . sprintf('%+d', -($node->{$level} - 1) + $newParent->{$level}) . ', ' . $this->_level . '), ' .
                $this->_right . ' = IF(' . $this->_right . ' BETWEEN ' . ($node->{$rightKey} + 1) . ' AND ' . ($newParent->{$rightKey} - 1) . ', ' . $this->_right . ' - ' . ($node->{$rightKey} - $node->{$leftKey} + 1) . ', ' .
                'IF(' . $this->_left . ' BETWEEN ' . ($node->{$leftKey}) . ' AND ' . ($node->{$rightKey}) . ', ' . $this->_right . ' + ' . ((($newParent->{$rightKey} - $node->{$rightKey} - $node->{$level} + $newParent->{$level}) / 2) * 2  +  $node->{$level} - $newParent->{$level} - 1) . ', ' . $this->_right . ')),  ' .
                $this->_left . ' = IF(' . $this->_left . ' BETWEEN ' . ($node->{$rightKey} + 1) . ' AND ' . ($newParent->{$rightKey} - 1) . ', ' . $this->_left . ' - ' . ($node->{$rightKey} - $node->{$leftKey} + 1) . ', ' .
                'IF(' . $this->_left . ' BETWEEN ' . $node->{$leftKey} . ' AND ' . ($node->{$rightKey}) . ', ' . $this->_left . ' + ' . ((($newParent->{$rightKey} - $node->{$rightKey} - $node->{$level} + $newParent->{$level}) / 2) * 2  +  $node->{$level} - $newParent->{$level} - 1) . ', ' . $this->_left .  ')) ' .
                'WHERE ' . $this->_left . ' BETWEEN ' . ($newParent->{$leftKey} + 1) . ' AND ' . ($newParent->{$rightKey} - 1);
        } elseif($newParent->{$leftKey} < $node->{$leftKey}) {
            $sql  =  'UPDATE ' . $this->_name . ' SET ' .
                $this->_level . ' = IF(' . $this->_left . ' BETWEEN ' . $node->{$leftKey} . ' AND ' . $node->{$rightKey} . ', ' . $this->_level . sprintf('%+d', -($node->{$level} - 1) + $newParent->{$level}) . ', ' . $this->_level . '), ' .
                $this->_left . ' = IF(' . $this->_left . ' BETWEEN ' . $newParent->{$rightKey} . ' AND ' . ($node->{$leftKey} - 1) . ', ' . $this->_left . ' + ' . ($node->{$rightKey} - $node->{$leftKey} + 1) . ', ' .
                'IF(' . $this->_left . ' BETWEEN ' . $node->{$leftKey} . ' AND ' . $node->{$rightKey} . ', ' . $this->_left . ' - ' . ($node->{$leftKey} - $newParent->{$rightKey}) . ', ' . $this->_left . ') ' .
                '), ' .
                $this->_right . ' = IF(' . $this->_right . ' BETWEEN ' . $newParent->{$rightKey} . ' AND ' . $node->{$leftKey} . ', ' . $this->_right . ' + ' . ($node->{$rightKey} - $node->{$leftKey} + 1) . ', ' .
                   'IF(' . $this->_right . ' BETWEEN ' . $node->{$leftKey} . ' AND ' . $node->{$rightKey} . ', ' . $this->_right . ' - ' . ($node->{$leftKey} - $newParent->{$rightKey}) . ', ' . $this->_right . ') ' .
                ') ' .
                'WHERE ' . $this->_left . ' BETWEEN ' . $newParent->{$leftKey} . ' AND ' . $node->{$rightKey} .
                ' OR ' . $this->_right . ' BETWEEN ' . $newParent->{$leftKey} . ' AND ' . $node->{$rightKey};
        } else {
            $sql  =  'UPDATE ' . $this->_name . ' SET ' .
                $this->_level . ' = IF(' . $this->_left . ' BETWEEN ' . $node->{$leftKey} . ' AND ' . $node->{$rightKey} . ', ' . $this->_level . sprintf('%+d', -($node->{$level} - 1) + $newParent->{$level}) . ', ' . $this->_level . '), ' .
                $this->_left . ' = IF(' . $this->_left . ' BETWEEN ' . $node->{$rightKey} . ' AND ' . $newParent->{$rightKey} . ', ' . $this->_left . ' - ' . ($node->{$rightKey} - $node->{$leftKey} + 1) . ', ' .
                   'IF(' . $this->_left . ' BETWEEN ' . $node->{$leftKey} . ' AND ' . $node->{$rightKey} . ', ' . $this->_left . ' + ' . ($newParent->{$rightKey} - 1 - $node->{$rightKey}) . ', ' . $this->_left . ')' .
                '), ' .
                $this->_right . ' = IF(' . $this->_right . ' BETWEEN ' . ($node->{$rightKey} + 1) . ' AND ' . ($newParent->{$rightKey} - 1) . ', ' . $this->_right . '-' . ($node->{$rightKey} - $node->{$leftKey} + 1) . ', ' .
                   'IF(' . $this->_right . ' BETWEEN ' . $node->{$leftKey} . ' AND ' . $node->{$rightKey} . ', ' . $this->_right . ' + ' . ($newParent->{$rightKey} - 1 - $node->{$rightKey}) . ', ' . $this->_right . ') ' .
                ') ' .
                'WHERE ' . $this->_left . ' BETWEEN ' . $node->{$leftKey} . ' AND ' . $newParent->{$rightKey} .
                ' OR ' . $this->_right . ' BETWEEN ' . $node->{$leftKey} . ' AND ' . $newParent->{$rightKey};
        }

        return $this->getAdapter()->query($sql);
    }

    /**
     * Return siblings of node included same node
     *
     * @param node id
     * @return array
     * @throws Exception, Zend_Db_Table_Exception
     */
    public function getSiblings($id) {
        $result = array();
        if ($parent = $this->getParent($id))
            return $this->getChildren($parent[$this->_id]);
        return $result;
    }
    
    private function moveBranchWhenNodeGoesDown($params)
    {
        $this->_db->beginTransaction();
        try {
            $this->_db->query("UPDATE " . $this->_name . " SET rgt = rgt - ?"
                    . " WHERE rgt > ? AND rgt <= ?", array(
                    $params['skew_tree'],
                    $params['right_key'],
                    $params['right_key_near']
            ));
            $this->_db->query("UPDATE " . $this->_name . " SET lft = lft - ?"
                    . " WHERE lft < ? AND lft > ?", array(
                        $params['skew_tree'],
                        $params['left_key'],
                        $params['right_key_near']
            ));
            $this->_db->query("UPDATE " . $this->_name . " SET lft = lft + ?,"
                    . "rgt = rgt + ?, level = level + ? "
                    . " WHERE id IN (?) ", array(
                        $params['skew_edit'],
                        $params['skew_edit'],
                        $params['skew_level '],
                        implode(',', $params['id_edit'])
           ));
           $this->_db->commit();
        } catch (Zend_Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    private function moveBranchWhenNodeGoesUp($params)
    {
        $this->_db->beginTransaction();
        try {
            $this->_db->query("UPDATE " . $this->_name . " SET rgt = rgt + ?"
                    . " WHERE rgt < ? AND rgt > ?", array(
                    $params['skew_tree'],
                    $params['left_key'],
                    $params['right_key_near']
            ));
            $this->_db->query("UPDATE " . $this->_name . " SET lft = lft + ?"
                    . " WHERE lft < ? AND lft > ?", array(
                        $params['skew_tree'],
                        $params['left_key'],
                        $params['right_key_near']
            ));
            $this->_db->query("UPDATE " . $this->_name . " SET lft = lft + ?,"
                    . "rgt = rgt + ?, level = level + ?"
                    . " WHERE id IN (?)", array(
                        $params['skew_edit'],
                        $params['skew_edit'],
                        $params['skew_level'],
                        implode(',', $params['id_edit'])
           ));
           $this->_db->commit();
        } catch (Zend_Exception $e) {
            $this->_db->rollBack();
            throw $e;
        }
    }

    public function _insert(array $data)
    {
        $rgtKey = 0;
        
        if(0 != $data['parent_id']) {
            $parentMenu = $this->find($data['parent_id'])->current();

            $data['level'] = $parentMenu->level;// parent level

            $rgtKey = $parentMenu->rgt;
            
            $lft = $rgtKey;//top level element
        } else {
            $lft = $this->findMaxRightKey() + 1;// if node is inserted to root
        }

		$level = 1;// top level of parent
        $parent_id = 0;
        
        if(isset($data['parent_id'])) {
            $parent_id = $data['parent_id'];
        }
        if(isset($data['level'])) {
            $level = $data['level'] + 1;
        }

        $tree_data = array(
            'lft' => $lft,
            'rgt' => $lft + 1,
            'level' => $level,
            'parent_id' => $parent_id
        );
        $data = array_merge($data, $tree_data);

        $rightKey = $lft;

        $this->_db->beginTransaction();

        try {

            if($rgtKey > 0) {
                $this->_db->query("UPDATE " . $this->_name . " SET lft = lft + 2,"
                        . "rgt = rgt + 2 WHERE lft > ?", $rightKey);
            }
            $this->_db->query("UPDATE " . $this->_name . " SET rgt = rgt + 2"
                    . " WHERE rgt >= ? AND lft < ?", array($rightKey, $rightKey));

            try {
                $this->insert($data);
                $rowId = $this->_db->lastInsertId();
            } catch (Zend_Exception $e) {
                throw $e;
            }

            $this->_db->commit();
        } catch (Zend_Exception $e) {
            $this->_db->rollBack();
            throw new RuntimeException("Row insertion failed " . $e->getMessage());
        }

        return $rowId;
    }
    

    public function _update(array $data, $where)
    {
        $rgtKey = 0;
        $row = $this->fetchRow($where);
        
        // 1 select keys and level of moving node
        $level  = $row->level;
        $left_key    = $row->lft;
        $right_key    = $row->rgt;
        
        // 2 level of new parent node (1 - for root)
        $level_up = 1;
        $data['level'] = $level_up;
        
        if(0 != $data['parent_id']) {
            $parentMenu = $this->find($data['parent_id'])->current();

            $level_up = $parentMenu->level;// parent level
            
            $rgtKey = $parentMenu->rgt;
            
            $data['level'] = $level_up + 1;
        }
        

        // 3 right_key, left_key detection
        if($data['parent_id'] == $row->parent_id) {// parent node is not changed
            unset($data['level']);
            $row->setFromArray($data);
            $row->save();
            return true;
        } else {
            if(0 == $rgtKey) {
                // move node to root
                $right_key_near = $this->findMaxRightKey();
            } else {
                // simple move to another node
                if($row->level > $data['level']) {// moving node up
                    $oldParent = $this->find($row->parent_id)->current();
                    $right_key_near = $oldParent->rgt;
                } else {
                    $right_key_near = $rgtKey - 1;
                }
            }
        }

        $skew_level = $level_up - $level + 1; // moving node offset
        $skew_tree  = $right_key - $left_key + 1; // tree keys offset

        $id_edit = $this->getAllIdsOfMovingNodesInBranch($left_key, $right_key);

        $params = array(
            'skew_tree' => $skew_tree,
            'left_key' => $left_key,
            'right_key_near' => $right_key_near,
            'skew_level' => $skew_level,
            'id_edit' => $id_edit
        );

        if($right_key_near > $right_key) {// moving node up
            // editing node keys offset
            $skew_edit = $right_key_near - $left_key + 1;
            $params['skew_edit'] = $skew_edit;
        var_dump($data);
        print '<br />параметры дерева <br />';
        var_dump($right_key_near, $left_key, $right_key, $params);
        print '<br />уровень нового узла';
        var_dump($data['level']);
        die();
            $this->moveBranchWhenNodeGoesUp($params);
        } else {// moving node down
            $skew_edit = $right_key_near - $left_key + 1 - $skew_tree;
            $params['skew_edit'] = $skew_edit;
            $this->moveBranchWhenNodeGoesDown($params);
        }

        $row->setFromArray($data);

        $row->save();
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
               throw new InvalidArgumentException('menu item with id ' . $id . ' not found!');
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

       } catch (Exception $e) {
           $this->_db->rollBack();
           throw new RuntimeException("Row deletion failed " . $e->getMessage());
       }

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
    /**
     * Select ids of all nodes that exist in branch where
     * moving node is
     *
     * @param int $left_key
     * @param int $right_key
     * @return array
     */
    private function getAllIdsOfMovingNodesInBranch($left_key, $right_key)
    {
        $ids = array();
        $select = $this->select()
                ->from(array($this->_name), array("id"))
                ->where("lft >= ?" , $left_key)
                ->where("rgt <= ?", $right_key);
        $rows = $this->fetchAll($select)->toArray();
        if(is_array($rows) && count($rows) > 0) {
            foreach($rows as $row) {
                $ids[] = $row['id'];
            }
        }
        return $ids;
    }
}