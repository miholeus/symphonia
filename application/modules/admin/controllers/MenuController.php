<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * MenuController processes requests to menus in admin panel
 * Menus are like groups to menu items. Each menu contains
 * many menu items
 *
 * @author miholeus
 */
class Admin_MenuController extends Soulex_Controller_Abstract
{
    /**
     * Show all menus
     */
    public function indexAction()
    {
        $mdlMenu = new Admin_Model_Menu();
        $this->view->orderParams = $this->_getOrderParams();
        $order = join(' ', $this->view->orderParams);
        $limit = $this->_getParam('limit', 20);

        $paginator = $mdlMenu->order($order)->paginate();

        // show items per page
        if($limit != 0) {
            $paginator->setItemCountPerPage($limit);
        } else {
            $paginator->setItemCountPerPage(-1);
        }

        $this->view->paginator = $paginator;
        Zend_Registry::set('pagination_limit', $limit);

        $this->view->render('menu/index.phtml');
    }
    /**
     * Show form for menu creation and process postback requests
     * to save new menu
     *
     * @return void
     */
    public function createAction()
    {
        $frmMenu = new Admin_Form_Menu();
        if($this->_request->isPost()
                && $frmMenu->isValid($this->_request->getPost())) {

            $mdlMenu = new Admin_Model_Menu();
            $mdlMenu->setTitle($frmMenu->getValue('title'))
                    ->setMenutype($frmMenu->getValue('menutype'))
                    ->setDescription($frmMenu->getValue('description'));
            $mdlMenu->save();

            return $this->_redirect('/admin/menu');
        }

        $this->view->form = $frmMenu;
        $this->renderSubmenu(false);
        $this->view->render('menu/create.phtml');
    }
    /**
     * Show form for editing menu and process postback request to
     * save info about menu
     *
     * @return void
     */
    public function editAction()
    {
        $id = $this->_getParam('id');

        $frmMenu = new Admin_Form_Menu();

        if($this->_request->isPost()
                && $frmMenu->isValid($this->_request->getPost())) {
            $mdlMenu = new Admin_Model_Menu($this->_request->getPost());
            $mdlMenu->save();

            $this->disableContentRender();

            return $this->_forward('index');
        }

        $mdlMenu = new Admin_Model_Menu();
        $this->view->menu = $mdlMenu->find($id);

        $frmMenu->populate(array(
            'id' => $this->view->menu->getId(),
            'title' => $this->view->menu->getTitle(),
            'menutype' => $this->view->menu->getMenutype(),
            'description' => $this->view->menu->getDescription()
        ));
        $this->view->form = $frmMenu;

        $this->renderSubmenu(false);

        $this->view->render('menu/edit.phtml');
    }
    /**
     * Delete menu by its id
     */
    public function deleteAction()
    {
        $mdlPage = new Admin_Model_Menu();
		$id = $this->getRequest()->getParam('id');

		$mdlPage->delete($id);
		$this->_redirect('/admin/menu');
    }

    private function _getOrderParams()
    {
        $order = $this->_getParam('order', 'title');
        $direction = $this->_getParam('direction', 'desc');
        /**
         * sets default order if model does not have proper field
         */
        if(!is_callable(array('Admin_Model_Menu',
            'get' . ucfirst($order)))) {
            $order = 'title';
        }

        if(!in_array(strtolower($direction), array('asc', 'desc'))) {
            $direction = 'desc';
        }

        return array('order' => $order, 'direction' => $direction);
    }
}
