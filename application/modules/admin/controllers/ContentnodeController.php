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
    /**
     * Copy node info to all pages
     * 
     * @return string json message whether copying succeeded or not
     */
    public function copynodeAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $post = $this->_request->getPost();
        if(empty($post['page']) || empty($post['node'])) {
            $isSucceeded = false;
        } else {
            $pageId = $post['page'];
            $node = $post['node'];

            $mdlContentNode = new Admin_Model_ContentNode(array(
                'pageId'    => $pageId,
                'name'      => $node
            ));

            $mdlPage = new Model_Page();
            $allPages = $mdlPage->fetchAll();
            $isSucceeded = $mdlContentNode->copyToPages($allPages);
        }
        echo Zend_Json_Encoder::encode(array('success' => $isSucceeded));
    }

    public function deletenodeAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        $post = $this->_request->getPost();
        if(empty($post['nodeId'])) {
            $isSucceeded = false;
        } else {
            $id = $post['nodeId'];

            $mdlContentNode = new Admin_Model_ContentNode();
            // @todo return values for delete action
            $isSucceeded = $mdlContentNode->delete($id);
        }
        echo Zend_Json_Encoder::encode(array('success' => $isSucceeded));
    }
}
