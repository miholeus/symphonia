<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Service;

use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\NoResultException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symphonia\CoreBundle\Entity\User as EntityUser;
use Symphonia\CoreBundle\Entity\UserStatus;

class User extends UserAwareService
{
    /**
     * @var UserPasswordEncoder
     */
    protected $passwordEncoder;

    /**
     * @param EntityUser $user
     */
    public function save(EntityUser $user)
    {
        $em = $this->getEntityManager();

        $em->persist($user);
        $em->flush();
    }


    /**
     * Подготавливает к сохранению пользователя
     * @param EntityUser $user
     */
    public function prepareUserToSave(EntityUser $user)
    {
        // Уберем плюсы из номера телефона
        $phone = $user->getPhone();
        $adoptedPhone = preg_replace('/^\+/', '', $phone);
        $user->setPhone($adoptedPhone);
    }

    /**
     * Check user password
     *
     * @param EntityUser $user
     * @param $password
     * @return bool
     */
    public function isPasswordValid(EntityUser $user, $password)
    {
        if (!$this->getPasswordEncoder()->isPasswordValid($user, $password)) {
            return false;
        }
        return true;
    }

    /**
     * @return UserPasswordEncoder
     */
    public function getPasswordEncoder()
    {
        return $this->passwordEncoder;
    }

    /**
     * @param UserPasswordEncoder $passwordEncoder
     */
    public function setPasswordEncoder(UserPasswordEncoder $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * Find user by email
     *
     * @param $email
     * @return null|EntityUser
     */
    public function findByEmail($email)
    {
        return $this->getEntityManager()
            ->getRepository(EntityUser::class)
            ->findOneBy(['email' => $email]);
    }


    /**
     * Find user by recovery code (verifyEmailUuid)
     * @param $code
     * @return null|EntityUser
     */
    public function findByRecoveryCode($code)
    {
        try {
            return $this->getEntityManager()
                ->getRepository(EntityUser::class)
                ->findOneByNotNullRecoveryCode($code);
        } catch (NoResultException $e) {
            return null;
        }
    }

    /**
     * Change user's password
     *
     * @param EntityUser $user
     * @param $password
     */
    public function changePassword(EntityUser $user, $password)
    {
        $user->setPassword($password);
        $user->setVerifyEmailUuid(null);
        $this->save($user);
    }


    /**
     * Find User by id
     *
     * @param $userId
     * @return null|EntityUser
     */
    public function findById($userId)
    {
        return $this->getRepository()->find($userId);
    }

    /**
     * Finds user by login
     *
     * @param string $login
     * @return null|EntityUser
     * @throws EntityNotFoundException
     */
    public function findByLogin(string $login)
    {
        $user =  $this->getRepository()->findOneBy(['login' => $login]);
        if (null === $user) {
            throw new EntityNotFoundException(sprintf("User was not found by login %s", $login));
        }
        return $user;
    }

    private function getRepository()
    {
        return $this->getEntityManager()->getRepository(EntityUser::class);
    }

    /**
     * Update's user last login time
     *
     * @param EntityUser $user
     * @param \DateTime|null $dateTime
     */
    public function updateLastLoginTime(EntityUser $user, \DateTime $dateTime = null)
    {
        $user->setLastLoginOn($dateTime ? $dateTime : new \DateTime());
        $this->getEntityManager()->flush($user);
    }

    /**
     * Blocks user
     *
     * @param EntityUser $user
     */
    public function block(EntityUser $user)
    {
        $status = $this->getUserStatus(UserStatus::STATUS_BLOCKED);

        $user->setStatus($status);
        $user->setIsBlocked(true);

        $this->save($user);
    }

    /**
     * @param $code
     * @return null|object|UserStatus
     */
    protected function getUserStatus($code)
    {
        $em = $this->getEntityManager();
        $status = $em->getRepository('SymphoniaCoreBundle:UserStatus')->findOneBy(['code' => $code]);
        return $status;
    }

    /**
     * Unblocks user
     *
     * @param EntityUser $user
     */
    public function unblock(EntityUser $user)
    {
        if (!$user->getIsBlocked()) {
            throw new \RuntimeException('Пользователь не заблокирован');
        }

        $status = $this->getUserStatus(UserStatus::STATUS_ACTIVE);
        $user->setStatus($status);
        $user->setIsBlocked(false);

        $this->save($user);
    }
}
