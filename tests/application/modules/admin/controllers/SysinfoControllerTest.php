<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * SysinfoControllerTest asserts that system information is displayed
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . '/../../../ControllerTestCase.php';

class Admin_Controller_SysinfoControllerTest extends ControllerTestCase
{
    protected function  setUp()
    {
        parent::setUp();
        $userFixture = new Admin_Fixture_User();
        $userFixture->authenticate();
    }
    public function testSystemInformationIsDisplayed()
    {
        $this->dispatch('/admin/sysinfo');
        $this->assertQueryContentContains('h2', 'System Information');
        $this->assertQueryContentContains('ul#submenu li a', 'System Info');
        $this->assertQueryContentContains('ul#submenu li a', 'PHP Settings');
        $this->assertQueryContentContains('ul#submenu li a', ' Configuration File');
        $this->assertQueryContentContains('ul#submenu li a', 'PHP Information');
    }
}
