<?php
/**
 * @package   NewClassic
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Realizes basic simple operations of create/read/update/delete
 *
 * @author miholeus
 */
class Soulex_Controller_Crud_Simple extends Soulex_Controller_Abstract
{
    /**
     * Object form
     *
     * @var Zend_Form_Abstract
     */
    protected $_form;
    /**
     * Anemic model
     *
     * @var Admin_Model_Abstract
     */
    protected $_model;
    /**
     * Object mapper
     *
     * @var Admin_Model_DataMapper_Standard
     */
    protected $_mapper;
    /**
     * Default sort parameter in ordering
     *
     * @var string
     */
    protected $_sortParam;
    /**
     * Default sort direction
     */
    protected $_sortDirection;
    /**
     * Limits order and direction $_GET params
     */
    protected function _getOrderParams()
    {
        $order = $this->_getParam('order', $this->_sortParam);
        $direction = $this->_getParam('direction');
        if(empty($direction) && !empty($this->_sortDirection)) {
            $direction = $this->_sortDirection;
        }
        if(empty($direction)) {
            $direction = 'desc';
        }

        /**
         * sets default order if model does not have proper field
         */
        $obj = new $this->_model();
        try {
            $obj->{"get" . ucfirst($order)}();
        } catch (BadMethodCallException $e) {
            $order = "id";
        }

        if(!in_array(strtolower($direction), array('asc', 'desc'))) {
            $direction = 'desc';
        }

        return array('order' => $order, 'direction' => $direction);
    }
    /**
     * You can do some work
     * in actions
     */
    protected function actionHook(){}
    /**
     * Show all objects
     */
    public function indexAction()
    {
        $this->_helper->viewRenderer->setNoRender();

        $this->view->orderParams = $this->_getOrderParams();
        $order = join(' ', $this->view->orderParams);
        $this->view->filter = array();// view property for where statements
        $limit = $this->_getLimitParam();

        if($this->_request->isPost()) {
            $post = $this->_request->getPost();
            
            $searchValue = isset($post['filter_search']) ?
                            $post['filter_search'] : null;

            $paginator = $this->_mapper->search($searchValue)
                                ->order($order)->paginate();

            try {
                if (isset($post['cid']) && is_array($post['cid'])) {
                    if(count($post['cid']) != $post['boxchecked']) {
                        throw new LengthException("Checksum is not correct");
                    }
                    try {
                        $this->_mapper->deleteBulk($post['cid']);
                        return $this->redirect('/admin/' . $this->_request->getControllerName());
                    } catch (Exception $e) {
                        throw new RuntimeException($e->getMessage(), $e->getCode());
                    }
                }
            } catch (Exception $e) {
                $this->renderSubmenu(false);
                $this->renderError("Object deletion failed with the following error: "
                        . $e->getMessage());
            }
        } else {
            $paginator = $this->_mapper->order($order)->paginate();
        }

        // show items per page
        if($limit != 0) {
            $paginator->setItemCountPerPage($limit);
        } else {
            $paginator->setItemCountPerPage(-1);
        }
        // get the page number that is passed in the request.
        //if none is set then default to page 1.
        $page = $this->_request->getParam('page', 1);

        $paginator->setCurrentPageNumber($page);
        // pass the paginator to the view to render
        $this->view->paginator = $paginator;
        Zend_Registry::set('pagination_limit', $limit);

        $this->view->render($this->_request->getControllerName() . '/index.phtml');
    }
    /**
     * Show form for object creation and process postback requests
     * to save new object
     *
     * @return void
     */
    public function createAction()
    {
        $this->actionHook();
        if($this->_request->isPost()
                && $this->_form->isValid($this->_request->getPost())) {
            $this->_form->removeElement('id');
            $this->_model->setOptions($this->_form->getValues());
            try {
                $this->_mapper->save($this->_model);

                return $this->redirect('/admin/' . $this->_request->getControllerName());
            } catch (Exception $e) {
                $this->renderSubmenu(false);
                $this->renderError("Object creation failed with the following error: "
                        . $e->getMessage());
            }
        }

        $this->view->form = $this->_form;
        $this->renderSubmenu(false);
        $this->view->render($this->_request->getControllerName() . '/create.phtml');
    }
    /**
     * Show form for editing insadvice and process postback request to
     * save object's info
     *
     * @return void
     */
    public function editAction()
    {
        $this->actionHook();
        $id = $this->_getParam('id');

        if($this->_request->isPost()
                && $this->_form->isValid($this->_request->getPost())) {

            try {
                $this->_model->setOptions($this->_form->getValues());
                $this->_mapper->save($this->_model);
                $this->disableContentRender();
                return $this->forward('index');
            } catch (Exception $e) {
                $this->renderSubmenu(false);
                $this->renderError("Object update failed with the following error: "
                        . $e->getMessage());
            }
        }

        try {
            $this->view->object = $this->_mapper->findById($id);
            $this->_form->populate($this->view->object->toArray());
        } catch (Exception $e) {
            $this->renderError($e->getMessage());
        }

        $this->view->form = $this->_form;

        $this->renderSubmenu(false);

        $this->view->render($this->_request->getControllerName() . '/edit.phtml');
    }
}
