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
}
