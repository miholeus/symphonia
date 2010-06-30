<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */


/**
 * Description of SimpleInput
 *
 * @author miholeus
 */
class Admin_Form_Decorator_SimpleInput extends Zend_Form_Decorator_Abstract
{
    protected $_format = '<input id="%s" name="%s" type="text" value="%s"/>';


    public function render($content)
    {
        $element = $this->getElement();
        $name    = htmlentities($element->getFullyQualifiedName());
        $id      = htmlentities($element->getId());
        $value   = htmlentities($element->getValue());

        $markup  = sprintf($this->_format, $id, $name, $value);

        $placement = $this->getPlacement();
        $separator = $this->getSeparator();

        switch($separator) {
            case self::PREPEND:
                return $markup . $separator . $content;
            case self::APPEND:
            default:
                return $content . $separator . $markup;
        }

        return $markup;
    }

}
