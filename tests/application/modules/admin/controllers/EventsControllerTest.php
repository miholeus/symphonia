<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * EventsControllerTest tests Events controller actions are working fine
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . '/../../../ControllerTestCase.php';

class Admin_Controller_EventsControllerTest extends ControllerTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $userFixture = new Admin_Fixture_User();
        $userFixture->authenticate();
    }

    public function testListAllEvents()
    {
        $this->dispatch('/admin/events');
        $this->assertController('events');
        $this->assertAction('index');
    }

    public function testEventsCannotBeDeletedIfBoxcheckedIsNull()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'cid' => array(0)
                ));
        $this->dispatch('/admin/events');
        $this->assertQueryContentContains('li',
                'Events deletion failed with the following error: Checksum is not correct');
    }

    public function testEventsCanBeDeleted()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'cid' => array(0),
                    'boxchecked' => 1
                ));
        $this->dispatch('/admin/events');
        $this->assertRedirectTo('/admin/events');
    }

    public function testUserCanSeeEditEventsForm()
    {
        $this->dispatch('/admin/events/edit/id/2');
        $this->assertController('events');
        $this->assertAction('edit');
    }

    public function testUserCanSeeCreateEventsForm()
    {
        $this->dispatch('/admin/events/create');
        $this->assertController('events');
        $this->assertAction('create');
        $this->assertQueryContentContains('h2', 'Events Manager: Add Events');
    }

    public function testUserCanCreateEvents()
    {
        $testEvents = array(
            'id' => '',
            'title' => 'testEvents',
            'short_description' => 'testDescription',
            'detail_description' => 'testDetailedDescription',
            'img_preview' => '',
            'published' => 0,
            'published_at' => date("Y-m-d H:i:s")
        );
        $this->getRequest()->setMethod('POST')
                ->setPost($testEvents);
        $this->dispatch('/admin/events/create');
        /*
         * test forwarding to index action
         */
        $this->assertAction('index');
    }

    public function testUserCanUpdateEvents()
    {
        $testEvents = array(
            'id' => 2,
            'title' => 'testEvents',
            'short_description' => 'testDescription',
            'detail_description' => 'testDetailedDescription',
            'img_preview' => '',
            'published' => 0,
            'published_at' => date("Y-m-d H:i:s")
        );
        $this->getRequest()->setMethod('POST')
                ->setPost($testEvents);
        $this->dispatch('/admin/events/edit');
        /*
         * test forwarding to index action
         */
        $this->assertAction('index');
    }
}
