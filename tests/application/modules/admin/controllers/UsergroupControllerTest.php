<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * UsergroupControllerTest tests User groups CRUD
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . '/../../../ControllerTestCase.php';

class Admin_Controller_UsergroupControllerTest extends ControllerTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $userFixture = new Admin_Fixture_User();
        $userFixture->authenticate();
    }
    
    public function testUserCanSeeUsergroupCreationForm()
    {
        $this->dispatch('/admin/usergroup/create');
        /**
         * user groups are not implemented at the moment
         */
        $this->assertQueryContentContains('div.m', 'Not yet implemented :(');
    }
}
