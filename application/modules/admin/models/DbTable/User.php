<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Admin_Model_DbTable_User is a gateway to users database
 *
 * @author miholeus
 */
class Admin_Model_DbTable_User extends Zend_Db_Table_Abstract
{
    protected $_name = 'users';
}
