<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of ContentnodeController
 *
 * @author miholeus
 */
class Admin_ContentnodeController extends Zend_Controller_Action
{
    /**
     * Loads Info of Current Node on Page
     */
    public function loadnodeAction()
    {
        $this->_helper->layout()->disableLayout();
//        $this->_helper->viewRenderer->setNoRender();
        $pageId = $this->_getParam('page');
        $node = $this->_getParam('node');

        $mdlContentNode = new Admin_Model_ContentNode(array(
            'pageId'    => $pageId,
            'name'      => $node
        ));
        $mdlContentNode->loadNode();
        
        $this->view->node = $mdlContentNode;
    }

    public function copynodeAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $pageId = $this->_getParam('page');
        $node = $this->_getParam('node');

        $mdlContentNode = new Admin_Model_ContentNode(array(
            'pageId'    => $pageId,
            'name'      => $node
        ));
        $isSucceeded = $mdlContentNode->copyToAllPages();
        return Zend_Json_Encoder::encode(array('success' => $isSucceeded));
    }
}
