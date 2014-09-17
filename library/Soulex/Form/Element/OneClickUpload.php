<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * OneClickUpload allows to upload files using AJAX technology
 *
 * @author miholeus
 */
class Soulex_Form_Element_OneClickUpload extends Zend_Form_Element_Text
{

    /**
    * Use formText view helper by default
     *
	* @var string
	*/
	public $helper = 'formOneClickUpload';
    /**
     * Add Helper Path to view
     */
    public function init()
    {
        $view = $this->getView();
        $view->addHelperPath('Soulex/View/Helper/JQuery', 'Soulex_View_Helper_JQuery');
    }
}