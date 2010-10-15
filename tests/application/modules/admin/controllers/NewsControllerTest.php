<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * NewsControllerTest tests news controller actions are working fine
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . '/../../../ControllerTestCase.php';

class Admin_Controller_NewsControllerTest extends ControllerTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $userFixture = new Admin_Fixture_User();
        $userFixture->authenticate();
    }

    public function testListAllNews()
    {
        $this->dispatch('/admin/news');
        $this->assertController('news');
        $this->assertAction('index');
    }

    public function testNewsCannotBeDeletedIfBoxcheckedIsNull()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'cid' => array(0)
                ));
        $this->dispatch('/admin/news');
        $this->assertQueryContentContains('h2', 'Application error');
        $this->assertResponseCode(500);
    }

    public function testNewsCanBeDeleted()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'cid' => array(0),
                    'boxchecked' => 1
                ));
        $this->dispatch('/admin/news');
        $this->assertRedirectTo('/admin/news');
    }

    public function testUserCanSeeEditNewsForm()
    {
        $this->dispatch('/admin/news/edit/id/2');
        $this->assertController('news');
        $this->assertAction('edit');
    }

    public function testUserCanSeeCreateNewsForm()
    {
        $this->dispatch('/admin/news/create');
        $this->assertController('news');
        $this->assertAction('create');
        $this->assertQueryContentContains('h2', 'News Manager: Add News');
    }

    public function testUserCanCreateNews()
    {
        $testNews = array(
            'id' => '',
            'title' => 'testNews',
            'short_description' => 'testDescription',
            'detail_description' => 'testDetailedDescription',
            'published' => 0,
            'published_at' => date("Y-m-d H:i:s")
        );
        $this->getRequest()->setMethod('POST')
                ->setPost($testNews);
        $this->dispatch('/admin/news/create');
        /*
         * test forwarding to index action
         */
        $this->assertAction('index');
    }

    public function testUserCanUpdateNews()
    {
        $testNews = array(
            'id' => 2,
            'title' => 'testNews',
            'short_description' => 'testDescription',
            'detail_description' => 'testDetailedDescription',
            'published' => 0,
            'published_at' => date("Y-m-d H:i:s")
        );
        $this->getRequest()->setMethod('POST')
                ->setPost($testNews);
        $this->dispatch('/admin/news/edit');
        /*
         * test forwarding to index action
         */
        $this->assertAction('index');
    }
}
