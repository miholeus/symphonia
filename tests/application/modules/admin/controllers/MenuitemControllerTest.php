<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Description of MenuitemControllerTest
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . '/../../../ControllerTestCase.php';

class Admin_Controller_MenuitemControllerTest extends ControllerTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $userFixture = new Admin_Fixture_User();
        $userFixture->authenticate();
    }

    public function testMenuitemsCanBeDisplayed()
    {
        $this->dispatch('/admin/menuitem');
        $this->assertController('menuitem');
        $this->assertAction('index');
    }

    public function testDeleteMenuItem()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'cid' => array(0),
                    'boxchecked' => '1'
                ));
        $this->dispatch('/admin/menuitem');
        $this->assertRedirectTo('/admin/menuitem');
    }

    public function testCreateFormCanBeDisplayed()
    {
        $this->dispatch('/admin/menuitem/create');
        $this->assertController('menuitem');
        $this->assertAction('create');
        $this->assertQueryContentContains('h2', 'Menu Manager: Add New Menu Item');
    }

    public function testMenuitemCanBeCreated()
    {
        $testMenuitem = array(
            'label' => 'test_label',
            'uri' => '/testing.html',
            'position' => '100',
            'parentId' => '0', // menu root
            'published' => '0',
            'menuId' => '5' // location: menu top
        );
        $this->getRequest()->setMethod('POST')
                ->setPost($testMenuitem);
        $this->dispatch('/admin/menuitem/create');
        $this->assertRedirectTo('/admin/menuitem');
    }

    public function testEditFormCanBeDisplayed()
    {
        $this->dispatch('/admin/menuitem/edit/id/94');
        $this->assertController('menuitem');
        $this->assertAction('edit');
        $this->assertQueryContentContains('h2', 'Menu Manager: Edit Menu Item');
    }

    public function testMenuitemCanBeEdited()
    {
        $testMenuitem = array(
            'label' => 'test_label',
            'uri' => '/testing.html',
            'position' => '100',
            'parentId' => '0', // menu root
            'published' => '0',
            'menuId' => '5', // location: menu top
            'id' => '94'
        );
        $this->getRequest()->setMethod('POST')
                ->setPost($testMenuitem);
        $this->dispatch('/admin/menuitem/edit');
        $this->assertRedirectTo('/admin/menuitem');
    }

    public function testMenuitemCanBeDeletedWithUrlParams()
    {
        $this->dispatch('/admin/menuitem/delete/id/0');
        $this->assertRedirectTo('/admin/menuitem');
    }
}
