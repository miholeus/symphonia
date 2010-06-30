<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Soulex_Invoke_ActionMapper is a mapper class for Soulex_Invoke_Action
 *
 * @author miholeus
 */

class Soulex_Invoke_ActionMapper extends Zend_Db_Table_Abstract
{
    protected $_name = 'controller_actions';

    public function findAction($id)
    {
        $row = $this->getAdapter()->query(
                "SELECT a.action, a.params, c.controller"
                . " FROM " . $this->_name . " AS a"
                . " INNER JOIN controllers AS c"
                . " ON c.id = a.controller_id"
                . " WHERE a.id = ?", $id)->fetchObject();
        if($row) {
            return $row;
        } else {
            throw new Zend_Exception('Action with ID ' . $id . ' was not found!');
        }
    }
}
?>
