<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * UserControllerTest test case
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . '/../../../ControllerTestCase.php';

class Admin_Controller_UserControllerTest extends ControllerTestCase
{
    public function testUserNotLoggedIn()
    {
        $this->dispatch('/admin/');
        $this->assertModule('admin');
        $this->assertController('user');
        $this->assertAction('login');
        $this->assertQueryContentContains('h1', 'Administration Login',
                'Administration login message was not set');
    }

    public function testUserLoggedIn()
    {
        $this->_loginUser();

        $this->_assertCredentials();

        /**
         * test that user will be forwarded to index action
         */
        $this->dispatch('/admin/user/login');

        $this->assertAction('index');

    }

    public function testUserLoggedInFromLoginUriGoesToAdminControllerIndexAction()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    "username" => "admin",
                    "password" => "1",
                    "retpath"  => "/admin/user/login"
                ));

        $this->dispatch('/admin/user/login');

        $this->_assertCredentials();

        $this->assertController('index');
        $this->assertAction('index');
    }
    
    public function testUserLoggedInFromLogoutUriGoesToAdminControllerIndexAction()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    "username" => "admin",
                    "password" => "1",
                    "retpath"  => "/admin/user/logout"
                ));

        $this->dispatch('/admin/user/logout');

        $this->_assertCredentials();

        $this->assertController('index');
        $this->assertAction('index');
    }

    public function testUserLoggedInWithReturnPath()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    "username" => "admin",
                    "password" => "1",
                    "retpath"  => "/admin/user"
                ));
        $this->dispatch('/admin/');

        $this->_assertCredentials();

        $this->assertController('user');
        $this->assertAction('login');

    }

    public function testUserFailedToLogin()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    "username" => "qwerty",
                    "password" => "qwerty"
                ));
        $this->dispatch('/admin/');
        $this->assertQueryContentContains('p',
                'Sorry, your username or password was incorrect');
    }

    public function testUserLoggedOut()
    {
        $this->_loginUser();

        $this->dispatch('/admin/user/logout');

        $this->assertNull(Zend_Auth::getInstance()->getIdentity(),
                "user session data was not cleared after logout");
    }

    public function testUserCreation()
    {
        $this->_loginUser();
        $random = rand(0, 100000);
        $newUser = array(
            "username" => "user" . $random,
            "firstname" => "firstname" . $random,
            "lastname" => "lastname" . $random,
            "password" => "1",
            "confirmPassword" => "1",
            "email" => "test@gmail.com",
            "enabled" => "0"
        );
        $this->getRequest()->setMethod('POST')
                ->setPost($newUser);
        $this->dispatch('/admin/user/create');
        /**
         * user will be forwarded to list action
         */
        $this->assertAction('list');
    }

    public function testUserCreationForm()
    {
        $this->_loginUser();
        $this->dispatch('/admin/user/create');
        $this->assertQueryContentContains('h2', 'User Manager: Add New User');
    }

    public function testDisplayAllUsers()
    {
        $this->_loginUser();
        $this->getRequest()->setParam('limit', 0);
        $this->dispatch('/admin/user/list');
        $this->assertQueryContentContains('h2', 'User Manager: Users');

        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'cid' => array(0)
                ));
        $this->dispatch('/admin/user/list');
        $this->assertQueryContentContains('li',
                'User deletion failed with the following error: '
                . 'User with id 0 not found');
        /**
         * wrong order params
         */
        $this->dispatch('/admin/user/list/order/asdf/direction/ddd');
        $this->assertAction('list');
    }

    public function testUserCanBeDeletedWithUrlParams()
    {
        $this->_loginUser();
        $this->dispatch('/admin/user/delete/id/0');
        $this->assertQueryContentContains('li',
                'User deletion failed with the following error: '
                . 'User with id 0 not found');
    }

    public function testUserUpdateAction()
    {
        $this->_loginUser();
        $this->getRequest()->setMethod('GET');
        $this->dispatch('/admin/user/update/id/2');
        $this->assertQueryContentContains('h2', 'User Manager: Update User');
        /**
         * test forwarding to list action without id
         */
        $this->getRequest()->setMethod('GET');
        $this->dispatch('/admin/user/update/id');
        $this->assertAction('list');
    }

    public function testUserMayUpdateInfo()
    {
        $this->_loginUser();
        $testUser = array(
            "username" => "admin",
            "firstname" => "admin" . $random,
            "lastname" => "admin",
            "password" => "1",
            "confirmPassword" => "1",
            "email" => "admin@gmail.com",
            "enabled" => "1"
        );
        $this->getRequest()->setMethod('POST')
                ->setPost($testUser);
        $this->dispatch('/admin/user/update');
        $this->assertAction('list');
    }


    public function testUserNotConfirmedPassword()
    {
        $this->_loginUser();
        $testUser = array(
            "username" => "admin",
            "firstname" => "admin" . $random,
            "lastname" => "admin",
            "password" => "1",
            "email" => "admin@gmail.com",
            "enabled" => "1"
        );
        $this->getRequest()->setMethod('POST')
                ->setPost($testUser);
        $this->dispatch('/admin/user/update');
        $this->assertQueryContentContains('li',
         'User update failed with the following error: Please confirm password');
    }

    public function testUserUpdatedNotValidForm()
    {
        $this->_loginUser();
        $testUser = array(
            "username" => "admin",
            "firstname" => "admin" . $random,
            "lastname" => "admin",
            "enabled" => "1"
        );
        $this->getRequest()->setMethod('POST')
                ->setPost($testUser);
        $this->dispatch('/admin/user/update');
        $this->assertQueryContentContains('li',
                'User update failed with the following error: email isEmpty');
    }

    private function _assertCredentials()
    {
        $user = Zend_Auth::getInstance()->getIdentity();

        $this->assertEquals($user->username, 'admin');
        $this->assertEquals($user->role, 'Administrator');
    }

    private function _loginUser()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    "username" => "admin",
                    "password" => "1"
                ));
        $this->dispatch('/admin/');
    }

//    public function testUserAlreadyLoggedIn()
//    {
//        $testUser = 'admin';
//        $testPass = '1';
//
//        $mdlUser = new Admin_Model_User();
//
//        $auth = Zend_Auth::getInstance();
//
//        // store the username, first and last names of the user
//        $storage = $auth->getStorage();
//
//        // get the default db adapter
//        $db = Zend_Db_Table::getDefaultAdapter();
//        //create the auth adapter
//        $authAdapter = new Zend_Auth_Adapter_DbTable($db, 'users',
//          'username', 'password');
//        //set the username and password
//        $authAdapter->setIdentity($testUser);
//        $authAdapter->setCredential($mdlUser->generatePassword($testPass));
//        $authAdapter->getDbSelect()->where('enabled = ?', 1);
//        //authenticate
//        $result = $authAdapter->authenticate();
//
//        $storage->write($authAdapter->getResultRowObject(
//          array('id', 'username', 'firstname', 'lastname', 'role')));
//
//        $this->dispatch('/admin/');
//        $this->assertAction('index');
//    }
}
