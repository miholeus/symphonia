<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * User fixture
 * Sets up user authentication
 *
 * @author miholeus
 */
require_once dirname(__FILE__) . '/../../../ControllerTestCase.php';

class Admin_Fixture_User extends ControllerTestCase
{
    public function authenticate()
    {
        $this->getRequest()->setMethod('POST')
                ->setPost(array(
                    "username" => "admin",
                    "password" => "1"
                ));
        $this->dispatch('/admin/');
        $this->getRequest()->setMethod('GET');
    }
}
