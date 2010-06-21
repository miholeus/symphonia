<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of PageController
 *
 * @author miholeus
 */

class Frontend_PageController extends Zend_Controller_Action
{
	public function indexAction()
	{
        // empty action
	}
	
	public function openAction()
	{
		/*
		 * установка другого слоя
         *
         */
//		$layout = $this->_helper->layout();
//		$layout->setLayout('alternate');
        $fr = Zend_Controller_Front::getInstance();
        $invokePlugin = $fr->getPlugin('Soulex_Invoke_Plugin');

        $pageData = $invokePlugin->getPage();

        if(isset($pageData['_data'])) {
            // installs static nodes on page with their values
            foreach($pageData['_data'] as $nodeName => $nodeData) {

                if($nodeName == 'content') {
                    $this->view->$nodeName = $nodeData['value'];
                } else {
                    $this->_helper->layout()->$nodeName = $nodeData['value'];
                }
            }
        }

        $this->view->title = $pageData['title'];
        $this->view->headTitle($pageData['title'], 'SET');
        $this->view->headTitle()->setSeparator(' / ');
        $this->view->headMeta()->appendName('keywords', $pageData['meta_keywords']);
        $this->view->headMeta()->appendName('description', $pageData['meta_description']);

		/*
		 * установка другого скина
		if($this->getRequest()->getParam('skin')) {
			$view = Zend_Registry::get('view');
			$view->skin = $this->getRequest()->getParam('skin');	
		}
		*/
		
	}

    public function notfoundAction()
    {
        $this->getResponse()->setHttpResponseCode(404);
    }
	
}