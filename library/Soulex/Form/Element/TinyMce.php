<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */


/**
 * TinyMce Form Element
 *
 * @author jurian (http://juriansluiman.nl)
 */
class Soulex_Form_Element_TinyMce extends Zend_Form_Element_Textarea
{
    
    /**
    * Use formTextarea view helper by default
	* @var string
	*/
	public $helper = 'formTinyMce';

}

