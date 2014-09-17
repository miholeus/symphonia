<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Description of LayoutLoader
 *
 * @author miholeus
 */
class Soulex_Controller_Action_Helper_LayoutLoader extends Zend_Controller_Action_Helper_Abstract
{
    public function preDispatch()
    {
        $bootstrap = $this->getActionController()
                         ->getInvokeArg('bootstrap');
        $config = $bootstrap->getOptions();
        $module = $this->getRequest()->getModuleName();
        if (isset($config[$module]['resources']['layout']['layout'])) {
            $layoutScript =
                 $config[$module]['resources']['layout']['layout'];
            $this->getActionController()
                 ->getHelper('layout')
                 ->setLayout($layoutScript);
        }
    }
}