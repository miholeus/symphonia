<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */


/**
 * TinyMce Form Element
 *
 * @author jurian ({@link http://juriansluiman.nl})
 */
class Soulex_Form_Element_TinyMce extends Zend_Form_Element_Textarea
{
    
    /**
    * Use formTextarea view helper by default
	* @var string
	*/
	public $helper = 'formTinyMce';

}

