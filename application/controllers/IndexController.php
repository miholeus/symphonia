<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function testAction()
    {
        $decorator = new Admin_Form_Decorator_SimpleInput();
        $element = new Zend_Form_Element('foo', array(
           'label' => 'Foo',
           'belongsTo' => 'bar',
            'value' => 'Test',
//            'decorators'    => array($decorator)
            'prefixPath' => array('decorator' => array(
                'Admin_Form_Decorator' => APPLICATION_PATH . "/modules/admin/forms/Decorator"
            )),
            'decorators' => array('SimpleInput', 'SimpleLabel')
        ));
        $this->view->element = $element;

    }
}

