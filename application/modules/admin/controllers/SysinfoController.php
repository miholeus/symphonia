<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of SysinfoController
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
