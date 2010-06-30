<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * BuildtreeController generates pages tree in xml format
 * Obsolete class (will be removed)
 *
 * @author miholeus
 */
class Admin_BuildtreeController extends Zend_Controller_Action
{
	public function init()
	{
		$this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
	}

    public function indexAction()
    {
		$response = $this->getResponse();
		$response->setHeader('Content-type', 'text/xml', true);
		$pageBuilder = new Soulex_Admin_Tree_Page();
		echo $pageBuilder->generateTree();
    }
}
?>
