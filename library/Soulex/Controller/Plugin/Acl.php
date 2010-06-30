<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Soulex_Controller_Plugin_Acl gives opportunity to secure private data
 *
 * @author miholeus
 */
class Soulex_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // set up acl
        $acl = new Zend_Acl();
        // add the roles
        $acl->addRole(new Zend_Acl_Role('guest'));
        $acl->addRole(new Zend_Acl_Role('user'), 'guest');
        $acl->addRole(new Zend_Acl_Role('administrator'), 'user');
        // add the resources
        $acl->add(new Zend_Acl_Resource('admin'));// admin area
        $acl->add(new Zend_Acl_Resource('frontend'));// all pages
        // mainly used for error page
        $acl->add(new Zend_Acl_Resource('default'));// default

        // guest can access pages
        $acl->allow('guest', array('frontend', 'default'));
        // administrators can do anything
        $acl->allow('administrator', null);

        // fetch the current user
        $auth = Zend_Auth::getInstance();
        if($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $role = strtolower($identity->role);
        } else {
            $role = 'guest';
        }

        $controller = $request->controller;
        $action = $request->action;
        $module = $request->getModuleName();

        if(!$acl->has($module)) {
            $request->setModuleName('default');
            $request->setControllerName('error');
            $request->setActionName('error');
        } else {

            if($module == 'admin') {
                $layout = Zend_Layout::getMvcInstance();
                $layout->setLayout($module);
            }

            if (!$acl->isAllowed($role, $module, $controller . '.' . $action)) {
                 if ($role == 'guest') {
                     $request->setModuleName('admin');
                     $request->setControllerName('user');
                     $request->setActionName('login');
                 } else {
                    $request->setModuleName('frontend');
                    $request->setControllerName('error');
                    $request->setActionName('noauth');
               }
            }
        }

    }
}
?>
