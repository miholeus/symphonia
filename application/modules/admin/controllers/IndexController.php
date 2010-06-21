<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of IndexController
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
