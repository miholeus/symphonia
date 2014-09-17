<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * PageControllerTest tests Admin_Page_Controller actions
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . '/../../../ControllerTestCase.php';

class Admin_Controller_PageControllerTest extends ControllerTestCase
{
    protected function  setUp()
    {
        parent::setUp();
        $userFixture = new Admin_Fixture_User();
        $userFixture->authenticate();
    }

    public function testUserCanListPages()
    {
        $this->dispatch('/admin/page');
        $this->assertController('page');
        $this->assertAction('index');
        $this->assertQueryContentContains('h2', 'Page Manager: Page');
    }

    public function testUserCanListAllPages()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'limit' => 0
                ));
        $this->dispatch('/admin/page/');
        $this->assertController('page');
        $this->assertAction('index');
    }

    public function testUserCanSeeEditPageForm()
    {
        $this->dispatch('/admin/page/edit/id/2');
        $this->assertController('page');
        $this->assertAction('edit');
        $this->assertQueryContentContains('h2', 'Page Manager: Edit Page');
        $this->assertQueryContentContains('span', 'Content Nodes');
    }

    public function testUserCanUpdatePage()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'id' => 2, //page id
                    'title' => 'title updated from test',
                    'uri' => '/testing.html'
                ));
        $this->dispatch('/admin/page/edit');
        $this->assertRedirectTo('/admin/page');
    }

    public function testUserCannotUpdatePageIfTheFormIsNotValid()
    {
        /*
         * mandatory fields uri and title are empty
         */
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'id' => 2, //page id
                    'title' => '',
                    'uri' => ''
                ));
        $this->dispatch('/admin/page/edit');
        $this->assertQueryContentContains('li',
                'Value is required and can\'t be empty');
    }

    public function testUserCanSeeCreatePageForm()
    {
        $this->dispatch('/admin/page/create');
        $this->assertQueryContentContains('h2',
                'Page Manager: Add New Page');
        $this->assertController('page');
        $this->assertAction('create');
    }

    public function testUserCanCreateNewPage()
    {
        $random = rand(0, 100000);
        $testPage = array(
            'uri' => 'testUri' . $random . '.html',
            'title' => 'testTitle' . $random,
            'content' => 'Here is a simple test content',
            'meta_keywords' => 'keywords' . $random,
            'meta_description' => 'description' . $random,
            'nodes[content][type]' => 1,
            'published' => 0
        );
        $this->getRequest()->setMethod('POST')
                ->setPost($testPage);
        $this->dispatch('/admin/page/create');
        $this->assertRedirectTo('/admin/page');
    }
}
