<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * User Form
 *
 * @author miholeus
 */
class Admin_Form_User extends Admin_Form_Template_Simple
{
    public function init()
    {
        $this->setMethod('post');
        $this->setName('adminForm');
        // create new element
        $id = $this->createElement('hidden', 'id');
        // element options
        $id->setDecorators(array('ViewHelper'));
        // add the element to the form
        $this->addElement($id);

        //create the form elements
        $username = $this->createElement('text','username');
        $username->setLabel('Username: ');
        $username->setRequired('true');
        $username->addFilter('StripTags');
        $username->addErrorMessage('The username is required!');
        $this->addElement($username);

        // password
        $password = $this->createElement('password', 'password');
        $password->setLabel('Password: ');
        $this->addElement($password);

        // confirm password
        $confPassword = $this->createElement('password', 'confirmPassword');
        $confPassword->setLabel('Confirm Password');
        $confPassword->addPrefixPath('Soulex_Validate', 'Soulex/Validate', 'validate');
        $confPassword->addValidator('PasswordConfirmation', true, array('password'));
//        $confPassword->addValidator(
//                new Zend_Validate_Identical(array('token' => 'password', 'strict' => false)));
        $this->addElement($confPassword);


        // first name
        $firstName = $this->createElement('text','firstname');
        $firstName->setLabel('First Name: ');
        $firstName->addFilter('StripTags');
        $this->addElement($firstName);

        // last name
        $lastName = $this->createElement('text','lastname');
        $lastName->setLabel('Last Name: ');
        $lastName->addFilter('StripTags');
        $this->addElement($lastName);

        // email
        $email = $this->createElement('text', 'email');
        $email->setLabel('Email');
        $email->setRequired(true);
        $email->setAttrib('class', 'required');
        $this->addElement($email);

        // enabled
        $enabled = $this->createElement('radio', 'enabled');
        $enabled->addMultiOptions(array(
            '1' => 'No',
            '0' => 'Yes'
        ));
        $enabled->setLabel('Enabled');
        $this->addElement($enabled);

        // role
        $role = $this->createElement('select', 'role');
        $role->setLabel("Select a role:");
        $role->setAttrib('class', 'checklist usergroups');
        $role->addMultiOption('User', 'user');
        $role->addMultiOption('Administrator', 'administrator');
        $this->addElement($role);

        // register Date
        $registerDate = $this->createElement('text', 'registerDate');
        $registerDate->setLabel('Register Date');
        $registerDate->setAttribs(array(
            'readonly' => 'readonly',
            'class'    => 'readonly'
        ));
        $this->addElement($registerDate);

        // lastvisit Date
        $lastvisitDate = $this->createElement('text', 'lastvisitDate');
        $lastvisitDate->setLabel('Last visit Date');
        $lastvisitDate->setAttribs(array(
            'readonly' => 'readonly',
            'class'    => 'readonly'
        ));
        $this->addElement($lastvisitDate);

        // return path
        $retpath = $this->createElement('hidden', 'retpath');
        $this->addElement($retpath);
        $submit = $this->addElement('submit', 'submit', array('label' => 'Submit'));
    }
    /**
     * Default values for user login form
     *
     * @param string $path used to set up return path value
     */
    public function setLoginForm($path)
    {
        $this->setAttribs(array(
            'id' => 'form-login',
            'name' => 'login'
        ));
        $this->setAction('/admin/user/login');
        $this->getElement('retpath')->setValue($path);
        
        // remove unneccessary elements
        foreach($this->getElements() as $element) {
            if(!in_array($element->getName(), array(
                'username', 'password', 'retpath'
            ))) {
                $this->removeElement($element->getName());
            }
        }

        // login username
        $login_username = $this->getElement('username');
        $login_username->setAttribs(array(
            'class' => 'inputbox',
            'size' => 15,
            'id' => 'login-username'
        ));
        // login password
        $login_password = $this->getElement('password');
        $login_password->setAttribs(array(
            'class' => 'inputbox',
            'size' => 15,
            'id' => 'login-password'
        ));
    }

}