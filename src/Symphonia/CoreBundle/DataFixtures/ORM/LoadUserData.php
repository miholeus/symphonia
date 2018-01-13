<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symphonia\CoreBundle\Entity\User;

class LoadUserData extends AbstractFixture
    implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setFirstname('Alexander');
        $user->setLastname('Pierce');
        $user->setLogin('demo');
        $user->setEmail('noreply@selloutsport.com');
        $user->setPhone('79999999999');
        $user->setRole($this->getReference('role-super_admin'));
        $user->setStatus($this->getReference('status-active'));
//        $encoder = $this->container->get('security.password_encoder');
//        $password = $encoder->encodePassword($user, 'demo');
        $password = 'demo';

        $user->setPassword($password);
        $user->setBirthDate(new \DateTime("now"));
        $user->setMailNotification(true);
        $user->setMustChangePasswd(false);
        $user->setIsActive(true);
        $user->setIsSuperuser(true);

        $demoUser = $this->createDemoUser();

        $manager->persist($user);
        $manager->persist($demoUser);

        $manager->flush();

        $this->addReference('admin-user', $user);
    }

    protected function createDemoUser()
    {
        $user = new User();
        $user->setFirstname('Rose');
        $user->setLastname('Johnson');
        $user->setLogin('rose');
        $user->setEmail('rose@selloutsport.com');
        $user->setPhone('79999999998');
        $user->setRole($this->getReference('role-user'));
        $user->setStatus($this->getReference('status-active'));

//        $encoder = $this->container->get('security.password_encoder');
//        $password = $encoder->encodePassword($user, 'demo');
        $password = 'demo';

        $user->setPassword($password);
        $user->setBirthDate(new \DateTime("now"));
        $user->setMailNotification(true);
        $user->setMustChangePasswd(false);
        $user->setIsActive(true);
        $user->setIsSuperuser(false);

        return $user;
    }

    public function getOrder()
    {
        return 3;
    }
}