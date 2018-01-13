<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Service\Traits;

use Symphonia\CoreBundle\Event\Event;
use Symphonia\CoreBundle\Event\NotificationInterface;

/**
 * Awareness of events
 */
trait EventsAwareTrait
{
    /**
     * @var NotificationInterface
     */
    protected $notificationManager;

    /**
     * Pending events
     *
     * @var Event[]
     */
    protected $pendingEvents = [];

    /**
     * @return NotificationInterface
     */
    public function getNotificationManager()
    {
        return $this->notificationManager;
    }

    /**
     * Notify events
     */
    protected function updateEvents()
    {
        $events = $this->pendingEvents;
        foreach ($events as $event) {
            $this->getNotificationManager()->notify($event);
        }
        $this->pendingEvents = [];
    }

    public function attachEvent(Event $event)
    {
        $this->pendingEvents[] = $event;
    }
}
