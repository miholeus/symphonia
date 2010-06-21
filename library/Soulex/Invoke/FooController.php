<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of FooController
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
