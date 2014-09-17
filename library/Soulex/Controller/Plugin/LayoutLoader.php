<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Soulex_Controller_Plugin_LayoutLoader automatically loads layouts
 * for different modules
 *
 * @author miholeus
 */
class Soulex_Controller_Plugin_LayoutLoader extends Zend_Controller_Plugin_Abstract
{
    /**
     * Layout object
     *
     * @var Zend_Layout
     */
    protected $_layout;
    /**
     * Register View, Doctype, View Helper
     *
     * @param Zend_Controller_Request_Abstract $request
     * @return Zend_View
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->_layout = Zend_Layout::getMvcInstance();

        $view = $this->_layout->getView();
//        $view->addHelperPath('Soulex/View/Helper', 'Soulex_View_Helper');
        $view->doctype('XHTML1_TRANSITIONAL');
        if($request->getModuleName() == 'admin') {
            $view->headTitle('Administrative panel', 'SET');
        }
        $view->skin = $request->getModuleName();
		Zend_Registry::set('view', $view);

		// Add it to the ViewRenderer
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
	        'ViewRenderer'
	    );
	    $viewRenderer->setView($view);
	    return $view;
    }
    /**
     * Set layout according to module's name
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        /**
         * Check if layout was changed already in some module
         * then do not set standard layout
         */
        $layout_changed = false;
        try {
            $layout_changed = Zend_Registry::get('layout_changed');
        } catch (Zend_Exception $e) {
            // no layout was changed and nobody disabled layout
            if($this->_layout->isEnabled()) {
                $this->_layout->setLayout($request->getModuleName());
            }
        }
    }
}
