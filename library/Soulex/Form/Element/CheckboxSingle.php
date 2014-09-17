<?php
/**
 * @package   NewClassic
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Render checkbox without input type hidden element
 *
 * @author miholeus
 */
class Soulex_Form_Element_CheckboxSingle extends Zend_Form_Element_Checkbox
{
    public $helper = 'formCheckboxSingle';
}