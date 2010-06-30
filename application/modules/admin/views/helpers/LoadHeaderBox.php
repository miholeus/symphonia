<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Loads Header Block in Admin Panel
 *
 * @author miholeus
 */
class Soulex_View_Helper_LoadHeaderBox extends Zend_View_Helper_Abstract
{
    public function loadHeaderBox()
    {
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()) {
            $layout = Zend_Layout::getMvcInstance();
            $view = $layout->getView();
            return $view->render('global/header.phtml');
        }
    }
}
