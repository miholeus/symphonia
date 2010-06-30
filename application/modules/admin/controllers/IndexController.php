<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * IndexController is base controller in admin panel
 *
 * @author miholeus
 */
class Admin_IndexController extends Soulex_Controller_Abstract
{
    public function indexAction()
    {
        $this->renderSubmenu(false);
        $this->renderToolbar(false);
        $this->view->render('index/index.phtml');
    }
}
?>
