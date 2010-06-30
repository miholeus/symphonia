<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Menu Form
 *
 * @author miholeus
 */
class Admin_Form_Menu extends Admin_Form_Template_Simple
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
        // title
        $title = $this->createElement('text', 'title');
        $title->setLabel('Title');
        $title->setAttribs(array(
            'size' => 40,
            'class' => 'inputbox required'
        ));
        $title->setRequired(true);
        $title->addErrorMessage('Title is required!');
        $title->addFilter('StripTags');
        $this->addElement($title);
        // menu type
        $menutype = $this->createElement('text', 'menutype');
        $menutype->setLabel('Menu Type');
        $menutype->setAttribs(array(
            'size' => 40,
            'class' => 'inputbox required'
        ));
        $menutype->setRequired(true);
        $menutype->addErrorMessage('Menu Type is required!');
        $menutype->addFilter('StripTags');
        $this->addElement($menutype);
        // description
        $description = $this->createElement('text', 'description');
        $description->setLabel('Description');
        $description->setAttrib('size', 40);
        $description->addFilter('StripTags');
        $this->addElement($description);
        // submit button
        $submit = $this->addElement('submit', 'submit', array('label' => 'Submit'));
    }
}
