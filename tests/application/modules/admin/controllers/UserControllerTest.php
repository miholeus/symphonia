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
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    "username" => "admin",
                    "password" => "1"
                ));
        $this->dispatch('/admin/');

        $this->_assertCredentials();

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
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    "username" => "admin",
                    "password" => "1"
                ));
        $this->dispatch('/admin/');

        $request = new Zend_Controller_Request_Simple('index', 'user', 'admin');

        $this->getFrontController()->dispatch($request);

        $this->assertNull(Zend_Auth::getInstance()->getIdentity(),
                "user session data was not cleared after logout");
    }

    private function _assertCredentials()
    {
        $user = Zend_Auth::getInstance()->getIdentity();

        $this->assertEquals($user->username, 'admin');
        $this->assertEquals($user->role, 'Administrator');
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
