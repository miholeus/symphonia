<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_UsergroupController processes requests to user groups
 *
 * @author miholeus
 */
class Admin_UsergroupController extends Soulex_Controller_Abstract
{
    /**
     * Create user group request
     */
    public function createAction()
    {
        $this->renderSubmenu(false);
        $this->renderToolbar(false);
        $this->view->render('usergroup/create.phtml');
    }
}
