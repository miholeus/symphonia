<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Description of MenuController
 *
 * @author miholeus
 */
class Frontend_MenuController extends Zend_Controller_Action
{
    public function menuleftAction()
    {
        $mdlMenuItem = new Admin_Model_MenuItem();
        $menuItems = $mdlMenuItem->fetchAll('menu_id = 6 AND published = 1', 'position');
        $this->view->menus = $menuItems;

        $this->view->selectedUri = $this->_request->getPathInfo();
        
        $responseSegment = $this->_getParam('_responseSegment');
        $this->_helper->viewRenderer->setResponseSegment($responseSegment);
    }

    public function menutopAction()
    {
        $mdlMenuItem = new Admin_Model_MenuItem();
        $menuItems = $mdlMenuItem->fetchAll('menu_id = 5 AND published = 1', 'position');
        $this->view->menus = $menuItems;
        $responseSegment = $this->_getParam('_responseSegment');
        $this->_helper->viewRenderer->setResponseSegment($responseSegment);
    }
}
