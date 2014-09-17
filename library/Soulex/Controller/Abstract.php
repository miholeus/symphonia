<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Soulex_Controller_Abstract provides methods
 * To set up global rendering of Views
 *
 * @property mixed|Zend_View_Interface $view
 *
 * @author miholeus
 */
class Soulex_Controller_Abstract extends Zend_Controller_Action
{

    /**
     * Disables global/content.phtml to be rendered
     *
     * @var bool
     */
    protected $contentRenderingDisabled = false;

    public function init()
    {
//        $view = Zend_Layout::getMvcInstance()->getView();
//        $view->addHelperPath("Soulex/View/Helper", "Soulex_View_Helper");


		// render submenu?
        $this->view->submenu_active = true;
        // render toolbar?
        $this->view->toolbar_active = true;
        // no errors by default
        $this->view->error_active = false;
    }

    public function postDispatch()
    {
        if(!$this->contentRenderingDisabled) {
            echo $this->view->render('global/content.phtml');
        }
    }

    /**
     * Enable/disable rendering of submenu box
     *
     * @param bool $isRendered
     */
    protected function renderSubmenu($isRendered)
    {
        $isRendered = (bool)$isRendered;
        $this->view->submenu_active = $isRendered;
    }
    /**
     * Enable/disable rendering of toolbar box
     * 
     * @param bool $isRendered
     */
    protected function renderToolbar($isRendered)
    {
        $isRendered = (bool)$isRendered;
        $this->view->toolbar_active = $isRendered;
    }
    /**
     * Enable rendering of error box
     * Set error message
     *
     * @param bool $isRendered
     */
    protected function renderError($errMsg)
    {
        $this->view->error_active = true;
        $this->view->errorMessage = $errMsg;
    }
    /**
     * Disables global/content.phtml to be rendered
     */
    protected function disableContentRender()
    {
        $this->contentRenderingDisabled = true;
    }
    /**
     * Return limit param value based on controller, then
     * save it back to session
     *
     * @return int pagination limit param
     */
    protected function _getLimitParam()
    {
        $session = new Zend_Session_Namespace('admin');
        $sessKey = md5($this->getRequest()->getControllerName() .
                $this->getRequest()->getActionName());

        if(!isset($session->pagination)) {
            $session->pagination = array();
        }

        if(!isset($session->pagination[$sessKey])) {
            // first time remember pagination limit
            $session->pagination[$sessKey] = 20;
        }

        if(null !== $this->_getParam('limit')) {
            $limit = $this->_getParam('limit');
            $session->pagination[$sessKey] = $limit;
        } else {
            $limit = $session->pagination[$sessKey];
        }

        return $limit;
    }
    /**
     * Return filter param value based on controller, then
     * save it back to session
     * 
     * @param string $name filter name
     * @return string  filter value
     */
    protected function _getFilterParam($name)
    {
        $session = new Zend_Session_Namespace('admin');
        $sessKey = md5($this->getRequest()->getControllerName() .
                $this->getRequest()->getActionName());

        if(!isset($session->filter)) {
            $session->filter = array();
        }
        if(!isset($session->filter[$sessKey])) {
            $session->filter[$sessKey] = array();
        }

        if(null !== $this->_getParam($name)) {
            $param = $this->_getParam($name);
            $session->filter[$sessKey][$name] = $param;
        } else {
            if(isset($session->filter[$sessKey][$name])) {
                $param = $session->filter[$sessKey][$name];
            } else {
                $param = null;
            }
        }

        return $param;
    }

    /**
     * Redirect to another URL
     *
     * Proxies to {@link Zend_Controller_Action_Helper_Redirector::gotoUrl()}.
     *
     * @param string $url
     * @param array $options Options to be used when redirecting
     * @return true
     */
    public function redirect($url, array $options = array())
    {
        $this->_helper->redirector->gotoUrl($url, $options);
        return true;
    }
}
