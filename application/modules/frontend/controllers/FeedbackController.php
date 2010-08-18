<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Sends email from contact form
 *
 * @author miholeus
 */
class Frontend_FeedbackController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $form = new Frontend_Form_Feedback();
        if($this->_request->isPost()
            && $form->isValid($this->_request->getPost())) {
            $bootstrap = $this->getInvokeArg('bootstrap');
            $options = $bootstrap->getOption('feedback');
            if(isset($options['email']) && is_array($options['email'])) {
                if(count($options['email']) > 0) {
                    $message = 'Ф.И.О.: ' . $form->getValue('name') . "\n"
                                . "Контактные данные: "
                                . $form->getValue('contacts') . "\n"
                                . "Сообщение: " . $form->getValue('text');
                    foreach($options['email'] as $email) {
                        $mail = new Zend_Mail('utf-8');
                        $mail->setBodyText($message)
                             ->addTo($email)
                             ->setSubject($options['subject'])
                             ->setFrom($options['from'],
                                       $options['fromname'])
                             ->send();
                    }
                    $this->view->messageSent = true;
                }
            }
        }
        $this->view->form = $form;
    }
}