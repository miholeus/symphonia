<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * News Form
 *
 * @author miholeus
 */
class Admin_Form_News extends Admin_Form_Template_Simple
{

    public function init()
    {
        $this->setAttrib('id', 'item-form');
        $this->setAttrib('class', 'form-validate');
        $this->setName('adminForm');

        $this->setMethod('post');
        // create new element
        $id = $this->createElement('hidden', 'id');
        // element options
        $id->setDecorators(array('ViewHelper'));
        // add the element to the form
        $this->addElement($id);
        //create the form elements
        $title = $this->createElement('text','title');
        $title->setLabel('Title: ');
        $title->setRequired('true');
        $title->addFilter('StripTags');
        $title->addErrorMessage('The title is required!');
        $this->addElement($title);
        // short description
        $shortDescription = $this->createElement('textarea','short_description');
        $shortDescription->setLabel('Short Description: ');
        $shortDescription->setAttrib('cols', 80);
        $shortDescription->setAttrib('rows', '4');
        $this->addElement($shortDescription);
        // detail description
        $detailDescription = new Soulex_Form_Element_TinyMce('detail_description');
        $this->removeDecorators($detailDescription);
        $detailDescription->setAttrib('style', 'width: 100%; height: 300px;');
        $detailDescription->setOptions(array(
            'label'      => 'Detail Description: ',
//            'required'   => true,
            'mode'       => 'exact',
            'elements' => 'detail_description',
            'editorOptions' => new Zend_Config_Ini(APPLICATION_PATH . '/configs/tinymce.ini', 'administrator')
        ));
        $this->addElement($detailDescription);
        // published
        $published = $this->createElement('radio', 'published');
        $published->setLabel('Published');
        $published->addMultiOption(1, 'Yes');
        $published->addMultiOption(0, 'No', array('checked' => true));
        $published->setAttrib('checked', 'checked');
        $this->addElement($published);
        /*
         * @TODO make own form elements
         */
        // published at
        $publishedAt = $this->createElement('text', 'published_at');
//        $publishedAt = ZendX_JQuery_Form_Element_DatePicker('published_at',
//                array('jQueryParams' => array('dateFormat' => 'yy-mm-dd'))
//        );
        $publishedAt->setLabel('Published At:');
        $this->addElement($publishedAt);
        // meta keywords
        $metakey = $this->createElement('textarea', 'metakey');
        $metakey->setLabel('Meta Keywords');
        $metakey->setAttrib('rows', 3);
        $metakey->setAttrib('cols', 30);
        $this->addElement($metakey);
        // meta description
        $metadesc = $this->createElement('textarea', 'metadesc');
        $metadesc->setLabel('Meta Description');
        $metadesc->setAttrib('rows', 3);
        $metadesc->setAttrib('cols', 30);
        $this->addElement($metadesc);
        // submit button
        $submit = $this->addElement('submit', 'submit', array('label' => 'Submit'));
    }
}
