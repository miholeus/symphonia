<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * FooController basic test controller
 *
 * @author miholeus
 */
class Soulex_Invoke_FooController extends Zend_Controller_Action
{
    public function fooAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        return 'this is foo action of foo controller invoked by parser!';
    }
}
?>
