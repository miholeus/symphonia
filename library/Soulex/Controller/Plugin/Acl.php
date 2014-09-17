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
    /**
     *
     * @var Zend_Acl
     */
    protected $acl;
    /**
     * Add system roles
     */
    protected function addRoles()
    {
        $this->acl->addRole(new Zend_Acl_Role('guest'));
        $this->acl->addRole(new Zend_Acl_Role('user'), 'guest');
//        $this->acl->addRole(new Zend_Acl_Role('copyrighter'), 'user');
        $this->acl->addRole(new Zend_Acl_Role('administrator'), 'user');
    }
    /**
     * Add system resources
     */
    protected function addResources()
    {
        $this->acl->add(new Zend_Acl_Resource('admin'));// admin area
        $this->acl->add(new Zend_Acl_Resource('frontend'));// all pages
        // mainly used for error page
        $this->acl->add(new Zend_Acl_Resource('default'));// default
    }

    /**
     * Dispath before any request
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        // set up acl
        $this->acl = new Zend_Acl();
        // add the roles
        $this->addRoles();

        // add the resources
        $this->addResources();

        // guest can access pages
        $this->acl->allow('guest', array('frontend', 'default'));
        // administrators can do anything
        $this->acl->allow('administrator', null);

        // fetch the current user
        if($request->getModuleName() == 'frontend') {
            $auth = NewClassic_Model_Auth::getInstance();
        } else {
            $auth = Zend_Auth::getInstance();
        }
        if($auth->hasIdentity()) {
            $identity = $auth->getIdentity();
            $role = strtolower($identity->role);
        } else {
            $role = 'guest';
        }

        // admin section permissions
        if($request->getModuleName() == 'admin') {
            $this->setAdminAreaPermissions($role);
        }
        // frontend section permissions
        if($request->getModuleName() == 'frontend') {
            $this->setProfileAreaPermissions($role);
            $this->setUserManagerPermissions($role);
        }

        // store acl in registry
        Zend_Registry::set('acl', $this->acl);


        $controller = $request->controller;
        $action = $request->action;
        $module = $request->getModuleName();

        if(!$this->acl->has($module)) {
            $request->setModuleName($module);
            $request->setControllerName('error');
            $request->setActionName('error');
        } else {

            if (!$this->acl->isAllowed($role, $module, $controller . '.' . $action)) {

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

    protected function setAdminAreaPermissions($role)
    {
        $this->acl->addRole(new Zend_Acl_Role('copyrighter'), 'user');
        $this->acl->addResource('admin.style');
        $this->acl->addResource('admin.trademark');
        $this->acl->addResource('admin.article');
        $this->acl->addResource('admin.famousauthor');
        $this->acl->addResource('admin.famousobject');

        // copyrighters can access admin area
        $this->acl->allow('copyrighter', 'admin');

        // user rights to different privileges
        $this->acl->allow($role, 'admin.style', 'publish',
                new NewClassic_Acl_Assertion_AdminPanel());
        $this->acl->allow($role, 'admin.trademark', 'publish',
                new NewClassic_Acl_Assertion_AdminPanel());
        $this->acl->allow($role, 'admin.article', 'publish',
                new NewClassic_Acl_Assertion_AdminPanel());
        $this->acl->allow($role, 'admin.famousauthor', 'publish',
                new NewClassic_Acl_Assertion_AdminPanel());
        $this->acl->allow($role, 'admin.famousobject', 'publish',
                new NewClassic_Acl_Assertion_AdminPanel());

    }

    protected function setProfileAreaPermissions($role)
    {
        $this->acl->addRole(new Zend_Acl_Role('specialist'), 'user');
        $this->acl->addRole(new Zend_Acl_Role('company'), 'specialist');
        $this->acl->addRole(new Zend_Acl_Role('moderator'), 'company');

        $this->acl->addRole(new Zend_Acl_Role('copyrighter'), 'user');
        $this->acl->addRole(new Zend_Acl_Role('editor'), 'copyrighter');
        $this->acl->addRole(new Zend_Acl_Role('chief'), 'editor');

        $resourcesList = new NewClassic_Acl_Resource_Frontend_Profile_Resource_List();
        foreach($resourcesList->getResources() as $resourceName => $privileges) {
            $this->acl->addResource($resourceName);

            foreach($privileges as $name => $data) {
                if(in_array($role, $data['roles'])) {
                    if(isset($data['assertion'])) {
                        $this->acl->allow($role, $resourceName, $name,
                                new $data['assertion']);
                    } else {
                        $this->acl->allow($role, $resourceName, $name);
                    }
                }
            }
        }
    }
    
    protected function setUserManagerPermissions($role)
    {
        $resourcesList = new NewClassic_Acl_Resource_Frontend_UserManager_Resource_List();
        foreach($resourcesList->getResources() as $resourceName => $privileges) {
            $this->acl->addResource($resourceName);

            foreach($privileges as $name => $roles) {
                if(in_array($role, $roles)) {
                    $this->acl->allow($role, $resourceName, $name,
                            new NewClassic_Acl_Assertion_UserManager());
                }
            }
        }
    }
}