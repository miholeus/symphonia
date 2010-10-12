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

        $userForm->setLoginForm($this->getRequest()->getPathInfo());
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
                  array('id', 'username', 'firstname', 'lastname', 'role')));

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
            if ($userForm->isValid($this->_request->getPost())) {

                $userForm->removeElement('id');
                $userForm->removeElement('retpath');

                try {

                    $userModel = new Admin_Model_User($userForm->getValues());
                    $userModel->save();

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
        $userModel = new Admin_Model_User();
        $where = null;// default where statement
        $this->view->orderParams = $this->_getOrderParams();
        $order = join(' ', $this->view->orderParams);
        $this->view->filter = array();// view property for where statements
        $limit = $this->_getParam('limit', 20);

        if($this->_request->isPost()) {
            $post = $this->_request->getPost();

            $paginator = $userModel->selectEnabled($post['filter_enabled'])
                                ->selectRole($post['filter_role'])
                                ->order($order)->paginate();

            $this->view->filter['enabled'] = $post['filter_enabled'];
            $this->view->filter['role'] = $post['filter_role'];

            if(is_array($post['cid'])) {
                try {
                    $userModel->deleteBulk($post['cid']);
                } catch (Exception $e) {
                    $this->renderSubmenu(false);
                    $this->renderError("User deletion failed with the following error: "
                            . $e->getMessage());
                }
            }
        } else {
            $paginator = $userModel->order($order)->paginate();
        }
        
        // show items per page
        if($limit != 0) {
            $paginator->setItemCountPerPage($limit);
        } else {
            $paginator->setItemCountPerPage(-1);
        }

        $this->view->paginator = $paginator;
        Zend_Registry::set('pagination_limit', $limit);

        $this->view->render('user/list.phtml');
    }

    public function updateAction ()
    {
        $userForm = new Admin_Form_User();
        $userForm->setAction('/admin/user/update');
        
        if ($this->_request->isPost()) {
            // remove element to disable its validation in the form
            $userForm->removeElement('username');

            $passValue = $this->_request->getParam('password');
            $passConfirmValue = $this->_request->getParam('confirmPassword');

            if ($userForm->isValid($this->_request->getPost())) {

                try {

                    if(!empty ($passValue)
                            && empty($passConfirmValue)) {
                        throw new Zend_Exception("Please confirm password");
                    }

                    // remove confirm password field as it doesn't exist in DB
                    $userForm->removeElement('confirmPassword');
                    $userForm->removeElement('retpath');

                    $userModel = new Admin_Model_User($userForm->getValues());
                    $userModel->save();

                    $this->disableContentRender();

                    return $this->_forward('list');

                } catch (Exception $e) {
                  $this->renderError("User update failed with the following error: "
                          . $e->getMessage());
                }

            } else {

                $errMsg = '';
                foreach($userForm->getErrors() as $element => $err) {
                    if(!empty($err[0])) {
                        $errMsg .= $element . ' ' . $err[0] . ' ';
                    }
                }
                $this->renderError("User update failed with the following error: "
                        . $errMsg);
                
            }
        } else {
            $id = $this->_request->getParam('id');
            if(null === $id) {
                $this->renderSubmenu(false);
                $this->renderToolbar(false);
                return $this->_forward('list');
            }
            $userModel = new Admin_Model_User();
            $currentUser = $userModel->find($id);
            $userForm->populate($currentUser->toArray());
            // disable username field
            $userForm->getElement('username')->setAttrib('disabled', 'disabled');
      }

      $this->view->form = $userForm;

      $this->renderSubmenu(false);

      $this->view->render('user/update.phtml');

    }

    public function deleteAction()
    {
        $id = $this->_request->getParam('id');
        $userModel = new Admin_Model_User();
        try {
            $userModel->delete($id);
            $this->disableContentRender();
            return $this->_forward('list');
        } catch (Exception $e) {
            $this->renderSubmenu(false);
            $this->renderError("User deletion failed with the following error: "
                    . $e->getMessage());
            $this->view->render('user/delete.phtml');
        }
    }

    private function _getOrderParams()
    {
        $order = $this->_getParam('order', 'firstname');
        $direction = $this->_getParam('direction', 'desc');
        /**
         * sets default order if model does not have proper field
         */
        if(!is_callable(array('Admin_Model_User',
            'get' . ucfirst($order)))) {
            $order = 'firstname';
        }

        if(!in_array(strtolower($direction), array('asc', 'desc'))) {
            $direction = 'desc';
        }

        return array('order' => $order, 'direction' => $direction);
    }
}