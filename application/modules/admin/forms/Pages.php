<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Pages Form
 *
 * @author miholeus
 */

class Admin_Form_Pages extends Admin_Form_Template_Simple
{
	public function init()
	{
        $this->setAttrib('id', 'item-form');
        $this->setAttrib('class', 'form-validate');
        $this->setName('adminForm');

        $this->setMethod('post');

		// title text field
		$title = $this->createElement('text', 'title');
		$title->setLabel('page title');
		$title->setAttrib('size', 80);
		$title->setRequired(true);
		$this->addElement($title);
		// uri text field
		$uri = $this->createElement('text', 'uri');
		$uri->setLabel('page uri');
		$uri->setAttrib('size', 80);
		$uri->setRequired(true);
		$this->addElement($uri);
		// meta keywords text field
		$meta_keywords = $this->createElement('textarea', 'meta_keywords');
		$meta_keywords->setLabel('meta keywords');
		$meta_keywords->setAttrib('cols', 30);
        $meta_keywords->setAttrib('rows', 3);
		$this->addElement($meta_keywords);
		// meta description textarea field
		$meta_description = $this->createElement('textarea', 'meta_description');
		$meta_description->setLabel('meta description');
		$meta_description->setAttrib('cols', 30);
		$meta_description->setAttrib('rows', 3);
		$this->addElement($meta_description);
        // published
        $published = $this->createElement('radio', 'published');
        $published->setLabel('Published');
        $published->addMultiOption(1, 'Yes');
        $published->addMultiOption(0, 'No', array('checked' => true));
        $published->setAttrib('checked', 'checked');
        $this->addElement($published);
        // static/dynamic selector
        $nodeType = $this->createElement('radio', 'nodescontenttype');
        $nodeType->setLabel('Node Type');
        $nodeType->addMultiOption(0, 'Static');
        $nodeType->addMultiOption(1, 'Dynamic');
        $this->addElement($nodeType);
		// content textarea field
        $content = new Soulex_Form_Element_TinyMce('content');
        $this->removeDecorators($content);
        $content->setAttrib('style', 'width: 100%; height: 300px;');
        $content->setOptions(array(
            'label'      => 'Content: ',
            'mode'       => 'exact',
            'elements' => 'content',
            'editorOptions' => new Zend_Config_Ini(APPLICATION_PATH . '/configs/tinymce.ini', 'administrator')
        ));
		$this->addElement($content);
		// id hidden text field
		$id = $this->createElement('hidden', 'id');
		$this->addElement($id);

        // content node module, controller, action fields
        //@todo set error handling for node fields
        $this->addElement('text', 'nodescontentmodule', array(
            'label' => 'Module'
        ));
        $this->addElement('text', 'nodescontentcontroller', array(
            'label' => 'Controller'
        ));
        $this->addElement('text', 'nodescontentaction', array(
            'label' => 'Action'
        ));

 		// submit button
		$this->addElement('submit', 'submit', array('label' => 'Submit', 'order' => 999) );
	}

    public function addTextArea($name, $value)
    {
        $f = $this->createElement('textarea', $name);
        $f->setLabel($name);
        $f->setValue($value);
        $f->setAttrib('cols', 80);
        $f->setAttrib('rows', 5);
        $this->addElement($f);
    }
    /**
     * Adds Text area and button to delete it
     *
     * @param string $name
     * @param string $value
     */
    public function addTextAreaControl($name, $value)
    {
        $this->addTextArea($name, $value);

        $f = $this->createElement('button', $name . '-button');
        $f->setLabel('delete ' . $name);
        $f->setAttrib('onclick', 'deleteNode("' . $name . '", this)');

        $this->addElement($f);

        // @todo add controls to array to retrieve them in future
    }

    public function addNewNodeButton($name = null)
    {
        $f = $this->createElement('button', 'addnode');
        $f->setLabel('add Node');
        $f->setAttrib('onclick', 'addNode(this)');
        $this->addElement($f);
    }
    /**
     * Set module, controller, action values of node
     *
     * @param string $name of node
     * @param array $data containing module, controller, action
     */
    public function setDynamicNodeData($name, $data)
    {
        $this->getElement('nodes' . $name . 'module')->setValue($data['module']);
        $this->getElement('nodes' . $name . 'controller')->setValue($data['controller']);
        $this->getElement('nodes' . $name . 'action')->setValue($data['action']);
    }
}