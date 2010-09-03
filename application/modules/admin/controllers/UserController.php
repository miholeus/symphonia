<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_UserController processes requests to users
 *
 * @author miholeus
 */
class Admin_UserController extends Soulex_Controller_Abstract
{

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()) {
            $this->view->identity = $auth->getIdentity();
        }
        $this->renderSubmenu(false);
        $this->view->render('user/index.phtml');
    }

    public function loginAction ()
    {
        $this->disableContentRender();

        $auth = Zend_Auth::getInstance();
        // we have already logined
        if($auth->hasIdentity()) {
            return $this->_forward('index');
        }

        $userForm = new Admin_Form_User();
        $userForm->showLoginAndPasswordFields();
        $userForm->setAction('/admin/user/login');
        $userForm->getElement('retpath')->setValue($this->getRequest()->getPathInfo());
        if ($this->_request->isPost() && $userForm->isValid($this->_request->getPost())) {
            
            $return_path = $this->_request->getPost('retpath');
            // we will not redirect to login, logout actions
            if(preg_match("~\/user\/(login|logout).*~", $return_path)) {
                $return_path = '';
            }

            $mdlUser = new Admin_Model_User();
            $data = $userForm->getValues();
            //set up the auth adapter
            // get the default db adapter
            $db = Zend_Db_Table::getDefaultAdapter();
            //create the auth adapter
            $authAdapter = new Zend_Auth_Adapter_DbTable($db, 'users',
              'username', 'password');
            //set the username and password
            $authAdapter->setIdentity($data['username']);
            $authAdapter->setCredential($mdlUser->generatePassword($data['password']));
            $authAdapter->getDbSelect()->where('enabled = ?', 1);
            //authenticate
            $result = $authAdapter->authenticate();
            if ($result->isValid()) {
                // store the username, first and last names of the user
                $storage = $auth->getStorage();
                $storage->write($authAdapter->getResultRowObject(
                  array('id', 'username', 'first_name', 'last_name', 'role')));

                $row = $authAdapter->getResultRowObject('id');
                $mdlUser->updateLastVisitDate($row->id);
                
                return !empty($return_path) ?
                         $this->_redirect($return_path) :
                         $this->_forward('index', 'index');
            } else {
                $this->view->loginMessage = "Sorry, your username or
                  password was incorrect";
            }
        }
        $this->view->form = $userForm;
    }

    public function logoutAction ()
    {
        $this->disableContentRender();
        
        $authAdapter = Zend_Auth::getInstance();
        $authAdapter->clearIdentity();
    }

    public function createAction ()
    {
        $userForm = new Admin_Form_User();
        if ($this->_request->isPost()) {
            if ($userForm->isValid($_POST)) {
                $userModel = new Admin_Model_User();

                $userForm->removeElement('id');
                $userForm->removeElement('retpath');

                try {

                    $userModel->createUser($userForm->getValues());
                    $this->disableContentRender();
                    return $this->_forward('list');

                } catch (Exception $e) {
                    $this->renderError(
                            "User creation failed with the following error: "
                            . $e->getMessage());
                }

            }
        }
        $userForm->setAction('/admin/user/create');
        $this->view->form = $userForm;

        $this->renderSubmenu(false);

        $this->view->render('user/create.phtml');
    }
    
    public function listAction ()
    {
        $currentUsers = Admin_Model_User::getUsers();
        if ($currentUsers->count() > 0) {
            $this->view->users = $currentUsers;
        } else {
            $this->view->users = null;
        }
        $this->view->render('user/list.phtml');
    }

    public function updateAction ()
    {
        $userForm = new Admin_Form_User();
        $userForm->setAction('/admin/user/update');
        $userForm->removeElement('password');
        $userModel = new Admin_Model_User();
        if ($this->_request->isPost()) {
            // remove element to disable its validation in the form
            $userForm->removeElement('username');
            
            if ($userForm->isValid($this->_request->getPost())) {

              $userForm->removeElement('retpath');

              try {
                  $userModel->updateUser($userForm->getValue('id'),
                      $userForm->getValues()
                  );

                  $this->disableContentRender();

                  return $this->_forward('list');

              } catch (Exception $e) {
                  $this->renderError("User update failed with the following error: "
                          . $e->getMessage());
              }

            }
        } else {
            $id = $this->_request->getParam('id');
            if(null === $id) {
                $this->renderSubmenu(false);
                $this->renderToolbar(false);
                return $this->_forward('list');
            }
            $currentUser = $userModel->find($id)->current();
            $userForm->populate($currentUser->toArray());
            // disable username field
            $userForm->getElement('username')->setAttrib('disabled', 'disabled');
      }

      $this->view->form = $userForm;

      $this->renderSubmenu(false);

      $this->view->render('user/update.phtml');

    }

    public function passwordAction()
    {
        $passwordForm = new Admin_Form_User();
        $passwordForm->setAction('/admin/user/password');
        $passwordForm->removeElement('first_name');
        $passwordForm->removeElement('last_name');
        $passwordForm->removeElement('username');
        $passwordForm->removeElement('role');
        $userModel = new Admin_Model_User();
        if ($this->_request->isPost()) {
            if ($passwordForm->isValid($_POST)) {
                 $userModel->updatePassword(
                     $passwordForm->getValue('id'),
                     $passwordForm->getValue('password')
                 );

                 $this->disableContentRender();

                 return $this->_forward('list');
            }
        } else {
            $id = $this->_request->getParam('id');
            $currentUser = $userModel->find($id)->current();
            $passwordForm->populate($currentUser->toArray());
      }
      $this->view->form = $passwordForm;

      $this->renderSubmenu(false);

      $this->view->render('user/password.phtml');
    }

    public function deleteAction()
    {
        $id = $this->_request->getParam('id');
        $userModel = new Admin_Model_User();
        try {
            $userModel->deleteUser($id);
            $this->disableContentRender();
            return $this->_forward('list');
        } catch (Exception $e) {
            $this->renderSubmenu(false);
            $this->renderError("User deletion failed with the following error: "
                    . $e->getMessage());
            $this->view->render('user/delete.phtml');
        }

    }

}