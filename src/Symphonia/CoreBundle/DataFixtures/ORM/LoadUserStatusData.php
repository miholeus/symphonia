<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenomania\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Symphonia\CoreBundle\Entity\UserStatus;

class LoadUserStatusData extends AbstractFixture
    implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 1;
    }

    public function load(ObjectManager $manager)
    {
        $data = [
            UserStatus::STATUS_ACTIVE => 'Active',
            UserStatus::STATUS_BLOCKED => 'Blocked',
            UserStatus::STATUS_DELETED => 'Deleted',
            UserStatus::STATUS_REGISTERED => 'Registered'
        ];
        $statusActive = null;

        foreach ($data as $code => $name) {
            $status = new UserStatus();
            $status->setName($name);
            $status->setCode($code);
            if ($code == UserStatus::STATUS_ACTIVE) {
                $statusActive = $status;
            }

            $manager->persist($status);
        }

        $manager->flush();

        $this->addReference('status-active', $statusActive);
    }
}