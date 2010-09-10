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
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $layout = Zend_Layout::getMvcInstance();
        $layout->setLayout($request->getModuleName());

        $view = $layout->getView();
        $view->addHelperPath('Soulex/View/Helper', 'Soulex_View_Helper');
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
}
