<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Service;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symphonia\CoreBundle\Entity\User as UserEntity;

/**
 * Recovers user's password by email
 */
class EmailPasswordRecoveryService
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;
    /**
     * @var TwigEngine
     */
    private $twigEngine;
    private $senderAddress;

    public function __construct(\Swift_Mailer $mailer, TwigEngine $twigEngine, $senderAddress)
    {
        $this->mailer = $mailer;
        $this->twigEngine = $twigEngine;
        $this->senderAddress = $senderAddress;
    }

    /**
     * Sends recovery code to email
     *
     * @param UserEntity $user
     * @return string
     * @throws \Twig_Error
     */
    public function sendPasswordRecoveryCode(UserEntity $user)
    {
        $recoveryCode = Uuid::uuid4()->toString();
        $messageBody = $this->twigEngine->render(
            'SymphoniaCoreBundle:email:recover.html.twig',
            [
                'user' => $user,
                'code' => $recoveryCode
            ]
        );
        /** @var \Swift_Message $message */
        $message = \Swift_Message::newInstance()
            ->setSubject('Password recovery')
            ->setFrom($this->senderAddress)
            ->setTo($user->getEmail())
            ->setBody($messageBody, 'text/html');
        $this->mailer->send($message);
        return $recoveryCode;
    }
}
