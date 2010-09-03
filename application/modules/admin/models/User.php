<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */
/**
 * Admin_Model_User is Data Access Object Layer.
 * It takes info from database and saves it back.
 *
 * @author miholeus
 */
class Admin_Model_User extends Zend_Db_Table_Abstract
{
    protected $_name = 'users';
    const ERR_USER_EXISTS = 1;

    public function createUser($data)
    {
        // checks user existance, throws exception if found one
        $this->checkUserExistanceByUsername($data['username']);
        // create a new row
        $rowUser = $this->createRow();
        if($rowUser) {
            // update the row values
            foreach ($data as $key => $value) {
                if($key == 'password') {
                    $value = $this->generatePassword($value);
                }
                $rowUser->$key = $value;
            }
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

    public function updateUser($id, $data)
    {
        // checks user existance, throws exception if found one
        $this->checkUserExistanceByUsername($data['username']);
        // fetch the user's row
        $rowUser = $this->find($id)->current();
        if($rowUser) {
            // update the row values
            foreach($data as $key => $value) {
                $rowUser->$key = $value;
            }
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
    /**
     * Finds user by username, returns null if nothing was found
     *
     * @param string $name
     * @return Zend_Db_Table_Row_Abstract|null
     */
    private function findByUsername($name)
    {
        return $this->fetchRow(
                $this->getDefaultAdapter()->quoteInto('username = ?',
                        $name));
    }
    /**
     * Invoked in checkUserExistanceByUsername()
     *
     * @throws Zend_Db_Exception
     */
    private function triggerErrorUserExists()
    {
        throw new Zend_Db_Exception(
                'username ' . $data['username'] . ' already exists',
                self::ERR_USER_EXISTS
        );
    }
    /**
     * Checks if username already exists and throws exception if found one
     *
     * @param string $name
     * @throws Zend_Db_Exception
     * @return Zend_Db_Table_Row_Abstract|null
     */
    private function checkUserExistanceByUsername($name)
    {
        $row = $this->findByUsername($name);
        if(null !== $row) {// username already exists
            $this->triggerErrorUserExists();
        }
        return $row;
    }

}
?>
