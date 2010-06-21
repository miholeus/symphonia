<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */


/**
 * Description of SimpleLabel
 *
 * @author miholeus
 */
class Admin_Form_Decorator_SimpleLabel extends Zend_Form_Decorator_Abstract
{
    protected $_format = '<label for="%s">%s</label>';

    public function render($content)
    {
        $element = $this->getElement();
        $id      = htmlentities($element->getId());
        $label   = htmlentities($element->getLabel());

        $markup = sprintf($this->_format, $id, $label);

        $placement = $this->getPlacement();
        $separator = $this->getSeparator();
        switch ($placement) {
            case self::APPEND:
                return $markup . $separator . $content;
            case self::PREPEND:
            default:
                return $content . $separator . $markup;
        }
    }

}
