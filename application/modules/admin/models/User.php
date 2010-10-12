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
 * All properties should be declared as protected due to toArray method
 * that uses reflection properties
 *
 * @author miholeus
 */
class Admin_Model_User extends Admin_Model_Abstract
{
    const ERR_USER_EXISTS = 1;
    
    protected $_id;
    protected $_username;
    protected $_email;
    protected $_password;
    protected $_firstname;
    protected $_lastname;
    protected $_enabled;
    protected $_registerDate;
    protected $_lastvisitDate;
    protected $_role;

    /**
     *
     * @var Admin_Model_UserMapper
     */
    protected $_mapperClass = 'Admin_Model_UserMapper';

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function getUsername()
    {
        return $this->_username;
    }

    public function setUsername($name)
    {
        $this->_username = $name;
        return $this;
    }

    public function getEmail()
    {
        return $this->_email;
    }

    public function setEmail($email)
    {
        $this->_email = $email;
    }

    public function getPassword()
    {
        return $this->_password;
    }

    public function setPassword($password)
    {
        $this->_password = $password;
        return $this;
    }

    public function getFirstname()
    {
        return $this->_firstname;
    }

    public function setFirstname($name)
    {
        $this->_firstname = $name;
        return $this;
    }

    public function getLastname()
    {
        return $this->_lastname;
    }

    public function setLastname($name)
    {
        $this->_lastname = $name;
        return $this;
    }

    public function getEnabled()
    {
        return $this->_enabled;
    }
    /**
     *
     * @param bool $isEnabled
     * @return Admin_Model_User
     */
    public function setEnabled($isEnabled)
    {
        $this->_enabled = (bool)$isEnabled;
        return $this;
    }

    public function getRegisterDate()
    {
        return $this->_registerDate;
    }
    /**
     *
     * @param string $date [YYYY-MM-DD hh:i:s]
     * @return Admin_Model_User
     */
    public function setRegisterDate($date)
    {
        $this->_registerDate = $date;
        return $this;
    }

    public function getLastvisitDate()
    {
        return $this->_lastvisitDate;
    }
    /**
     *
     * @param string $date [YYYY-MM-DD hh:i:s]
     * @return Admin_Model_User
     */
    public function setLastvisitDate($date)
    {
        $this->_lastvisitDate = $date;
        return $this;
    }

    public function getRole()
    {
        return $this->_role;
    }
    /**
     *
     * @param string $role
     * @return Admin_Model_User
     */
    public function setRole($role)
    {
        $this->_role = $role;
        return $this;
    }
    /**
     * Returns array of all protected properties that can be
     * accessed through get*() methods
     *
     * @return array
     */
    public function toArray()
    {
        $arr = array();
        $reflect = new Zend_Reflection_Class($this);
        $getters = $reflect->getMethods(Zend_Reflection_Method::IS_PUBLIC);

        $properties = $reflect->getProperties(
                Zend_Reflection_Property::IS_PROTECTED);
        $propertiesNames = array();
        foreach($properties as $property) {
            if(substr($property->getName(), 0, 1) == "_") {
                if(substr($property->getName(), 1, 6) != "mapper") {
                    $propertiesNames[] = substr($property->getName(), 1);
                }
            }
        }

        foreach($getters as $method) {
            if(substr($method->getName(), 0, 3) == "get") {
                if(PHP_VERSION_ID > 50302) {
                    $propName = lcfirst(substr($method->getName(), 3));
                } else {
                    $propName = substr($method->getName(), 3);
                    $propName{0} = strtolower($propName{0});
                }
                $methodName = $method->getName();

                // check if there is protected property that
                // corresponds to method name
                if(in_array($propName, $propertiesNames)) {
                    $arr[$propName] = $this->$methodName();
                }
            }
        }

        return $arr;
    }

    public function save()
    {
        return $this->getMapper()->save($this);
    }

    public function generatePassword($password)
    {
        return md5($password);
    }
    /**
     * Fetches users including where statements and order
     *
     * @return Admin_Model_User|null
     */
    public function fetch()
    {
        return $this->getMapper()->fetch();
    }
    /**
     *
     * @return Zend_Paginator 
     */
    public function paginate()
    {
        $adapter = $this->getMapper()->fetchPaginator();
        return new Zend_Paginator($adapter);
    }
    /**
     *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @param int                               $count  OPTIONAL An SQL LIMIT count.
     * @param int
     * @return Admin_Model_User
     */
    public function fetchAll($where = null, $order = null, $limit = null, $offset = null)
    {
        return $this->getMapper()->fetchAll($where, $order, $limit, $offset);
    }
    /**
     *
     * @param string|array|Zend_Db_Table_Select $where  OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
     * @param string|array                      $order  OPTIONAL An SQL ORDER clause.
     * @return Zend_Paginator_Adapter_DbSelect
     */
    public function fetchPaginator($where, $order)
    {
        return $this->getMapper()->fetchPaginator($where, $order);
    }
    /**
     * Delete user by it's id
     *
     * @param int $id
     */
    public function delete($id)
    {
        $auth = Zend_Auth::getInstance();
        $myId = $auth->getIdentity()->id;
        if($myId == $id) {
            throw new Zend_Exception("You cannot delete yourself");
        }
        $this->getMapper()->delete($id);
    }
    /**
     * Mass user deletion
     *
     * @uses delete
     * @param array $ids
     */
    public function deleteBulk($ids)
    {
        if(is_array($ids) && count($ids) > 0) {
            foreach($ids as $id) {
                $this->delete($id);
            }
        }
    }

    /**
     * Finds data row by id and returns new object
     * If object was not found then we set initial null values
     * to object
     *
     * @param int $id
     * @throws Zend_Exception if user was not found
     * @return Admin_Model_User
     */
    public function find($id)
    {
        $this->getMapper()->find($id, $this);
        return $this;
    }

    public function updateLastVisitDate($id)
    {
        $this->getMapper()->setLastVisit($id, date("Y-m-d H:i:s"));
    }
    /**
     * Selects role
     *
     * @param string $role
     * @return Admin_Model_User
     */
    public function selectRole($role)
    {
        if($role != '') {
            $this->getMapper()->role($role);
        }
        return $this;
    }
    /**
     * Selects enabled status for users
     *
     * @param bool $enabled
     * @return Admin_Model_User
     */
    public function selectEnabled($enabled)
    {
        /**
         * isset added to prevent the clause when user is updated
         * and $enabled value comes as null
         */
        if($enabled != '*' && isset($enabled)) {
            $enabled = (int)$enabled;
            $this->getMapper()->enabled($enabled);
        }
        return $this;
    }

    public function search($searchValue)
    {
        if(!empty($searchValue)) {
            $searchValue = str_replace('\\', '\\\\', $searchValue);
            $searchValue = addcslashes($searchValue, '_%');
            $this->getMapper()->search($searchValue);
        }
        return $this;
    }
    /**
     *
     * @param string $spec the column and direction to sort by
     * @return Admin_Model_User
     */
    public function order($spec)
    {
        $this->getMapper()->order($spec);
        return $this;
    }
    /**
     * Checks if username already exists and throws exception if found one
     *
     * @param string $name
     * @throws Zend_Exception
     * @return Admin_Model_User|null
     */
    public function checkUserExistanceByUsername($name)
    {
        $user = $this->getMapper()->findByUsername($name);
        if(null !== $user) {// username already exists
            $this->triggerErrorUserExists($name);
        }
        return $user;
    }

    /**
     * Invoked in checkUserExistanceByUsername()
     *
     * @throws Zend_Exception
     */
    private function triggerErrorUserExists($name)
    {
        throw new Zend_Exception(
                'username ' . $name . ' already exists',
                self::ERR_USER_EXISTS
        );
    }

}
?>
