<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Description of Feedback
 *
 * @author miholeus
 */
class Frontend_Form_Feedback extends Zend_Form
{
    public function init()
    {
        $this->setAttrib('id', 'contact');
        $this->setAttrib('name', 'sendform');
        $this->setMethod('post');
        // name
        $name = $this->createElement('text', 'name');
        $name->setRequired(true);
        $name->setLabel('Ф.И.О.');
        $name->addErrorMessage('Поле Ф.И.О обязательно для заполнения!');
        $name->addFilter('StripTags');
        $this->addElement($name);
        // contacts
        $contacts = $this->createElement('text', 'contacts');
        $contacts->setRequired(true);
        $contacts->setLabel('Контактные данные (телефон, e-mail)');
        $contacts->addErrorMessage('Поле Контактные данные обязательно для заполнения');
        $contacts->addFilter('StripTags');
        $this->addElement($contacts);
        // text
        $text = $this->createElement('textarea', 'text');
        $text->setRequired(true);
        $text->setLabel('Сообщение');
        $text->addErrorMessage('Поле Сообщение обязательно для заполнения');
        $text->setAttrib('rows', 5);
        $text->addFilter('StripTags');
        $this->addElement($text);
        // submit button
		$this->addElement('submit', 'submit', array('label' => 'Отправить') );
    }
}