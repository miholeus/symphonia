<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Repository;

use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symphonia\CoreBundle\Entity\User;

class UserRepository extends \Doctrine\ORM\EntityRepository implements UserLoaderInterface
{
    /**
     * Loads user by username
     * It is used by firewall security component
     *
     * @param string $username
     * @return User
     */
    public function loadUserByUsername($username)
    {
        /** @var User $user */
        $user = $this->createQueryBuilder('u')
            ->where('u.login = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getOneOrNullResult();

        return $user;
    }
}