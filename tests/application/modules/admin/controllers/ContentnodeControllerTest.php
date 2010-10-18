<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Description of ContentnodeControllerTest
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . '/../../../ControllerTestCase.php';

class Admin_Controller_ContentnodeControllerTest extends ControllerTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $userFixture = new Admin_Fixture_User();
        $userFixture->authenticate();
    }

    public function testNodeCanBeLoaded()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'pageId' => 2,
                    'node' => 'content'
                ));
        $this->dispatch('/admin/contentnode/loadnode');
        $this->assertController('contentnode');
        $this->assertAction('loadnode');
    }

    public function testNodeCanBeCopiedToAllPages()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'page' => 2,
                    'node' => 'sidebar'
                ));
        $this->dispatch('/admin/contentnode/copynode');
        $this->assertController('contentnode');
        $this->assertAction('copynode');
    }

    public function testNodeCanBeDeleted()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    'nodeId' => '0'
                ));
        $this->dispatch('/admin/contentnode/deletenode');
        $this->assertController('contentnode');
        $this->assertAction('deletenode');
        $this->assertEquals(
                Zend_Json_Encoder::encode(array('success' => false)),
                $this->getResponse()->getBody());
    }

    public function testNodeCannotBeDeletedWithoutNodeId()
    {
        $this->dispatch('/admin/contentnode/deletenode');
        $this->assertEquals(
                Zend_Json_Encoder::encode(array('success' => false)),
                $this->getResponse()->getBody());
    }
}
