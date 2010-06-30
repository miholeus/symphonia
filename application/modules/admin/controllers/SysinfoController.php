<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_SysinfoController processes requests to system information
 *
 * @author miholeus
 */
class Admin_SysinfoController extends Soulex_Controller_Abstract
{
    public function indexAction()
    {
        $mdlSysInfo = new Admin_Model_Sysinfo();

        $this->view->sysInfo = $mdlSysInfo;
        $this->view->render('sysinfo/index.phtml');
    }
}
