<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Simple Form without any standard decorators
 *
 * @author miholeus
 */
class Admin_Form_Template_Simple extends Zend_Form
{
   /**
     * Set ViewScript Decorator
     *
     * @param string $viewScript template name
     */
    public function setViewScript($viewScript)
    {
        $this->setDecorators(array(
            array('viewScript', array(
               'viewScript' => $viewScript . '.phtml'
            )))
        );
    }

    /**
     * Add Element to form without default decorators
     *
     * @see Zend_Form::addElement()
     */
    public function addElement($element, $name = null, $options = null)
    {
        parent::addElement($element, $name, $options);

        if (isset($this->_elements[$name])) {
            $this->removeDecorators($this->_elements[$name]);
        }
    }

    /**
     * Create form element without default decorators
     *
     * @see Zend_Form::createElement()
     */
    public function createElement($type, $name, $options = null)
    {
        $element = parent::createElement($type, $name, $options);
        $this->removeDecorators($element);
        return $element;
    }
    /**
     * Remove default decorators for $element
     *
     * @param Zend_Form_Element $element
     */
    protected function removeDecorators($element)
    {
        $element->removeDecorator('Label');
        $element->removeDecorator('HtmlTag');
        $element->removeDecorator('DtDdWrapper');
        $element->removeDecorator('Description');
    }

}
