<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symphonia\CoreBundle\Entity\User;
use Symphonia\CoreBundle\Event\NotificationInterface;
use Symphonia\CoreBundle\Service\Traits\EventsAwareTrait;

/**
 * Service that takes currently logged user
 */
class UserAwareService
{
    use EventsAwareTrait;
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var User
     */
    protected $user;
    /**
     * @var NotificationInterface
     */
    protected $notificationManager;
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    public function __construct(EntityManager $em, TokenStorage $tokenStorage, NotificationInterface $notificationManager)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->notificationManager = $notificationManager;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        if (null === $this->user) {
            if (null === $this->tokenStorage->getToken()) {
                return null;
            }
            $this->user = $this->tokenStorage->getToken()->getUser();
        }
        return $this->user;
    }
}
