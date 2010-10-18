<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * MenuControllerTest test user can view/create/update/delete menus
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . '/../../../ControllerTestCase.php';

class Admin_Controller_MenuControllerTest extends ControllerTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $userFixture = new Admin_Fixture_User();
        $userFixture->authenticate();
    }

    public function testMenusCanBeDisplayed()
    {
        $this->dispatch('/admin/menu');
        $this->assertController('menu');
        $this->assertAction('index');
    }

    public function testMenusCreationFormIsDisplayed()
    {
        $this->dispatch('/admin/menu/create');
        $this->assertController('menu');
        $this->assertAction('create');
        $this->assertQueryContentContains('h2', 'Menu Manager: Add New Menu');
    }

    public function testMenusCanBeCreated()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'title' => 'test_title',
                    'menutype' => 'menutype' . rand(0, 1000000),
                    'description' => 'test_description'
                ));
        $this->dispatch('/admin/menu/create');
        $this->assertRedirectTo('/admin/menu');
    }

    public function testMenusEditFormIsDisplayed()
    {
        $this->dispatch('/admin/menu/edit/id/5');
        $this->assertController('menu');
        $this->assertAction('edit');
        $this->assertQueryContentContains('h2', 'Menu Manager: Edit Menu');
    }

    public function testMenusCanBeEdited()
    {
        $testData = array(
            'title' => 'Menu Top',
            'menutype' => 'menutop',
            'description' => 'Menu Top Location',
            'id' => 5
        );
        $this->getRequest()->setMethod('POST')
                ->setPost($testData);
        $this->dispatch('/admin/menu/edit');
        /**
         * test User is forwarded to index action
         */
        $this->assertAction('index');
    }

    public function testMenusCanBeDeletedWithUrlParams()
    {
        $this->dispatch('/admin/menu/delete/id/0');
        $this->assertRedirectTo('/admin/menu');
    }
}
