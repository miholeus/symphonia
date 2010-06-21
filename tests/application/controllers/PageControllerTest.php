<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PageControllerTest
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . '/../ControllerTestCase.php';

class Controller_PageControllerTest extends ControllerTestCase
{
    /**
     * @todo Если пользователь зарегистрировался, то при заходе в админ панель
     * он получит сообщение об ошибке - 404 Not Found
     */
    public function testNotFoundAction()
    {
        $this->dispatch('/foo/bar');
        $this->assertController('error');
        $this->assertAction('error');
    }
    public function testOpenAction()
    {
        $this->dispatch('/');
        $this->assertController('page');
        $this->assertAction('open');
    }

    public function testCanLayoutMarkersBeRendered()
    {
        $this->markTestIncomplete('This test was not complete');
//        $this->dispatch('/buildingwork.htm');
//        $this->assertController('foo');
//        $this->assertAction('foo');
    }
}
?>
