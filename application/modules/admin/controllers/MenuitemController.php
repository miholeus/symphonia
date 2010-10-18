<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * MenuitemController processes requests to menu items
 *
 * @author miholeus
 */
class Admin_MenuitemController extends Soulex_Controller_Abstract
{
    /**
     * Show menu items including different search criteria
     * 
     * @return void
     */
    public function indexAction()
    {
        $mdlMenuItem = new Admin_Model_MenuItem();
        $this->view->orderParams = $this->_getOrderParams();
        $order = join(' ', $this->view->orderParams);
        $this->view->filter = array();// view property for where statements
        $limit = $this->_getParam('limit', 20);

        if($this->_request->isPost()) {
            $post = $this->_request->getPost();

            $paginator = $mdlMenuItem->selectState($post['filter_state'])
                                ->selectMenuId($post['filter_menuid'])
                                ->selectLevel($post['filter_level'])
                                ->search($post['filter_search'])
                                ->order($order)->paginate();

            $this->view->filter['state'] = $post['filter_state'];
            $this->view->filter['menuid'] = $post['filter_menuid'];
            $this->view->filter['level'] = $post['filter_level'];

            if(isset($post['cid'])) {
                if(is_array($post['cid'])
                        && count($post['cid']) == $post['boxchecked']) {
                    $mdlMenuItem->delete($post['cid']);
                    return $this->_redirect('/admin/menuitem');
                } else {
                    throw new Exception('FCS  is not correct! Wrong request!');
                }
            }
        } else {
            $paginator = $mdlMenuItem->order($order)->paginate();
        }


        // show items per page
        if($limit != 0) {
            $paginator->setItemCountPerPage($limit);
        } else {
            $paginator->setItemCountPerPage(-1);
        }

        $page = $this->_request->getParam('page', 1);
        $paginator->setCurrentPageNumber($page);
        // pass the paginator to the view to render
        $this->view->paginator = $paginator;
        Zend_Registry::set('pagination_limit', $limit);
        
        $maxMenuLevel = $mdlMenuItem->findMaxMenuLevel();

//        $menuLevels = array();
//        for($i = 1; $i <= $maxMenuLevel; $i++) {
//            $menuLevels[$i] = $i;
//        }
        $this->view->menuLevels = array_combine(array_values(range(1, $maxMenuLevel)),
                range(1, $maxMenuLevel));

        $mdlMenu = new Admin_Model_Menu();
        $menus = $mdlMenu->fetchAll();

        $view_menus = array();
        foreach($menus as $menu) {
            $view_menus[$menu->getId()] = $menu->getTitle();
        }
        $this->view->menus = $view_menus;

        $this->view->render('menuitem/index.phtml');
    }
    /**
     * Show menu item form and create a new item while postback
     * 
     * @return void
     */
    public function createAction()
    {
        $frmMenuItem = new Admin_Form_MenuItem();
        $mdlMenu = new Admin_Model_Menu();
        $menus = $mdlMenu->fetchAll();
        foreach($menus as $menu) {
            $frmMenuItem->addElementOption('menuId', $menu->getId(), $menu->getTitle());
        }

        $mdlMenuItem = new Admin_Model_MenuItem();
        $items = $mdlMenuItem->fetchAllGrouppedByParentId();
        $mdlMenuItem->processTreeElementForm($items, $frmMenuItem, 'parentId');

        if($this->_request->isPost() &&
                $frmMenuItem->isValid($this->_request->getPost())) {
                $frmMenuItem->removeElement('id');
                $mdlMenuItem = new Admin_Model_MenuItem($frmMenuItem->getValues());
                $mdlMenuItem->save();
                return $this->_redirect('/admin/menuitem');
        }


        $this->view->form = $frmMenuItem;
        $this->renderSubmenu(false);
        $this->view->render('menuitem/create.phtml');
    }
    /**
     * Show menu item form for editing info and update menu item
     * 
     * @return void
     */
    public function editAction()
    {
        $id = $this->_getParam('id');
        $frmMenuItem = new Admin_Form_MenuItem();
        $mdlMenu = new Admin_Model_Menu();
        $menus = $mdlMenu->fetchAll();
        foreach($menus as $menu) {
            $frmMenuItem->addElementOption('menuId', $menu->getId(), $menu->getTitle());
        }

        $mdlMenuItem = new Admin_Model_MenuItem();
        $menuItem = $mdlMenuItem->find($id);
        $items = $mdlMenuItem->fetchAllGrouppedByParentId();
        $mdlMenuItem->processTreeElementForm($items, $frmMenuItem, 'parentId',
                $menuItem->getParentId());
        $frmMenuItem->getElement('parentId')->removeMultiOption($id);

        if($this->_request->isPost() &&
                $frmMenuItem->isValid($this->_request->getPost())) {
            $mdlMenuItem = new Admin_Model_MenuItem($frmMenuItem->getValues());
            $mdlMenuItem->save();

            return $this->_redirect('/admin/menuitem');
        }

        $frmMenuItem->populate(array(
            'id' => $menuItem->getId(),
            'label' => $menuItem->getLabel(),
            'uri' => $menuItem->getUri(),
            'menuId' => $menuItem->getMenuId(),
            'position' => $menuItem->getPosition(),
            'published' => $menuItem->getPublished()
        ));

        $this->view->form = $frmMenuItem;
        $this->renderSubmenu(false);
        $this->view->render('menuitem/edit.phtml');
    }
    /**
     * Delete menu item by its id
     */
    public function deleteAction()
    {
        $id = $this->_getParam('id');
        $mdlMenuItem = new Admin_Model_MenuItem();
        $mdlMenuItem->delete($id);
        $this->_redirect('/admin/menuitem');
    }

    private function _getOrderParams()
    {
        $order = $this->_getParam('order', 'lft');
        $direction = $this->_getParam('direction', 'asc');
        /**
         * sets default order if model does not have proper field
         */
        if(!is_callable(array('Admin_Model_Menuitem',
            'get' . ucfirst($order)))) {
            $order = 'lft';
        }

        if(!in_array(strtolower($direction), array('asc', 'desc'))) {
            $direction = 'desc';
        }

        return array('order' => $order, 'direction' => $direction);
    }
}
