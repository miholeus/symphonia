<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * MenuItem Form
 *
 * @author miholeus
 */
class Admin_Form_MenuItem extends Admin_Form_Template_Simple
{
    public function init()
    {
        $this->setAttrib('id', 'item-form');
        $this->setAttrib('class', 'form-validate');
        $this->setName('adminForm');
        $this->setMethod('post');
        // id
        $id = $this->createElement('hidden', 'id');
        $this->addElement($id);
        // label
        $label = $this->createElement('text', 'label');
        $label->setLabel('Label');
        $label->setRequired(true);
        $label->addFilter('StripTags');
        $label->setAttrib('size', 80);
        $this->addElement($label);
        // uri
        $uri = $this->createElement('text', 'uri');
        $uri->setLabel('Uri');
        $uri->setRequired(true);
        $uri->addFilter('StripTags');
        $uri->setAttrib('size', 80);
        $this->addElement($uri);
        // position
        $position = $this->createElement('text', 'position');
        $position->setLabel('Position');
        $position->addFilter('Alnum');
        $this->addElement($position);
        // published
        $published = $this->createElement('radio', 'published');
        $published->setLabel('Published');
        $published->addMultiOption(1, 'Yes');
        $published->addMultiOption(0, 'No', array('checked' => true));
        $this->addElement($published);
        // parent Id
        $parentId = $this->createElement('select', 'parentId');
        $parentId->setLabel('Parent Menu Item');
        $parentId->addMultiOption(0, 'Menu_Item_Root');
        $this->addElement($parentId);
        // menuId select options
        $menuId = $this->createElement('select', 'menuId');
        $this->addElement($menuId);
        // submit button
        $submit = $this->addElement('submit', 'submit', array('label' => 'Submit'));
    }
    /**
     * Add options to select box or radios
     *
     * @param string $element name
     * @param int $key
     * @param string $value
     */
    public function addElementOption($element, $key, $value)
    {
        return $this->getElement($element)->addMultiOption($key, $value);
    }
}
