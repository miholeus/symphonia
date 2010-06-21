<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */
/**
 * Description of User
 *
 * @author miholeus
 */
class Admin_Model_User extends Zend_Db_Table_Abstract
{
    protected $_name = 'users';

    public function createUser($username, $password, $firstName, $lastName, $role)
    {
        // create a new row
        $rowUser = $this->createRow();
        if($rowUser) {
            // update the row values
            $rowUser->username = $username;
            $rowUser->password = $this->generatePassword($password);
            $rowUser->first_name = $firstName;
            $rowUser->last_name = $lastName;
            $rowUser->role = $role;
            $rowUser->registerDate = date("Y-m-d H:i:s");
            $rowUser->save();
            //return the new user
            return $rowUser;
        } else {
            throw new Zend_Exception("Could not create user!");
        }
    }

    public function generatePassword($password)
    {
        return md5($password);
    }

    public static function getUsers()
    {
        $userModel = new self();
        $select = $userModel->select();
        $select->order(array('last_name', 'first_name'));
        return $userModel->fetchAll($select);
    }

    public function updateUser($id, $username, $firstName, $lastName, $role)
    {
        // fetch the user's row
        $rowUser = $this->find($id)->current();
        if($rowUser) {
            // update the row values
            $rowUser->username = $username;
            $rowUser->first_name = $firstName;
            $rowUser->last_name = $lastName;
            $rowUser->role = $role;
            $rowUser->save();
            //return the updated user
            return $rowUser;
        }else{
            throw new Zend_Exception("User update failed.  User not found!");
        }
    }

    public function updatePassword($id, $password)
    {
        // fetch the user's row
        $rowUser = $this->find($id)->current();
        if($rowUser) {
            //update the password
            $rowUser->password = $this->generatePassword($password);
            $rowUser->save();
        }else{
            throw new Zend_Exception("Password update failed. User not found!");
        }
    }

    public function deleteUser($id)
    {
        // fetch the user's row
        $rowUser = $this->find($id)->current();
        if($rowUser) {
            $rowUser->delete();
        }else{
            throw new Zend_Exception("Could not delete user. User not found!");
        }
    }

    public function updateLastVisitDate($id)
    {
        $where = $this->getDefaultAdapter()->quoteInto('id = ?', $id);
        $this->update(array('lastvisitDate' => date("Y-m-d H:i:s")), $where);
    }

}
?>
