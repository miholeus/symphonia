<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Entity\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symphonia\CoreBundle\Entity\Exception\ValidatorException;
use Symphonia\CoreBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symphonia\CoreBundle\Entity\Traits\ValidatorTrait;
use Symphonia\CoreBundle\Service\Exception\UploadFileException;

class UserListener
{
    use ValidatorTrait;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param User $user
     * @return array|null
     */
    protected function getValidationGroups(User $user)
    {
        $groups = null;
        if ($user->getAvatar() && $user->getAvatar() instanceof File) {
            $groups = ['Default', 'upload'];
        }
        return $groups;
    }

    /**
     * User post load event
     *
     * @param User $user
     * @param LifecycleEventArgs $event
     */
    public function postLoad(User $user, LifecycleEventArgs $event)
    {
        $user->addRole("ROLE_" . $user->getRole()->getName());
    }
    /**
     * @param User $user
     * @param LifecycleEventArgs $event
     * @throws ValidatorException
     */
    public function preUpdate(User $user, LifecycleEventArgs $event)
    {
        $userService = $this->getUserService();
        $userService->prepareUserToSave($user);

        $groups = $this->getValidationGroups($user);
        $this->validate($user, null, $groups);

        $uow = $event->getEntityManager()->getUnitOfWork();
        $entityChangeSet = $uow->getEntityChangeSet($user);

        if (trim($user->getPassword()) == '') {
            if (isset($entityChangeSet['password'])) {
                // recover old value
                $user->setPassword($entityChangeSet['password'][0]);
                $user->setPasswdChangedOn(new \DateTime());
            }
        } else {
            if (!empty($entityChangeSet['password'])) {
                $encoder = $this->getContainer()->get('security.password_encoder');
                $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
                $user->setPasswdChangedOn(new \DateTime());
            }
        }
        $this->convertFilePathToRelative($user);
        $user->setUpdatedOn(new \DateTime());
    }

    public function prePersist(User $user, LifecycleEventArgs $event)
    {
        $groups = $this->getValidationGroups($user);
        $this->validate($user, null, $groups);

        $encoder = $this->getContainer()->get('security.password_encoder');
        $user->setPassword($encoder->encodePassword($user, $user->getPassword()));
        $this->convertFilePathToRelative($user);
    }

    /**
     * @return \Symphonia\CoreBundle\Service\User
     */
    protected function getUserService()
    {
        return $this->getContainer()->get('user.service');
    }

    /**
     * Формируем пути к файлам фотографий пользователя
     *
     * @param User $user
     * @throws UploadFileException
     */
    protected function convertFilePathToRelative(User $user)
    {
        $rootDirectory = $this->getContainer()->getParameter('profile_upload_photo_dir');
        if ($user->getAvatar() instanceof File && $avatar = $user->getAvatar()) {
            $pos = strpos($avatar->getRealPath(), $rootDirectory);
            if (false !== $pos) {
                $path = substr($avatar->getRealPath(), $pos-1);
                $user->setAvatar($path);
            }
        }
        if ($user->getAvatarSmall() instanceof File && $avatarSmall = $user->getAvatarSmall()) {
            $pos = strpos($avatarSmall->getRealPath(), $rootDirectory);
            if (false !== $pos) {
                $path = substr($avatarSmall->getRealPath(), $pos-1);
                $user->setAvatarSmall($path);
            }
        }
    }
}
