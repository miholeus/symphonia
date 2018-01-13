<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Event;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Notification manager is used to trigger events
 * If listener is subscribed to specific event, it will be invoked during notify process
 * All events are handled by event dispatcher component
 */
class NotificationManager implements NotificationInterface
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /** @var null|Event[] */
    protected $pendingEvents = null;

    /**
     * NotificationManager constructor.
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Notify all listeners about event
     *
     * @param Event $event
     * @return void
     */
    public function notify(Event $event)
    {
        $this->getEventDispatcher()->getDispatcher()->dispatch($event->getName(), $event);
    }

    /**
     * Imitation of async event dispatcher
     *
     * @param Event $event
     */
    public function notifyAsync(Event $event)
    {
        if (null === $this->pendingEvents) {
            $this->getEventDispatcher()->getDispatcher()->addListener(KernelEvents::TERMINATE, array($this, 'async'));
        }
        $this->pendingEvents[] = $event;
    }

    public function async()
    {
        $events = $this->pendingEvents;
        $this->pendingEvents = [];
        foreach ($events as $event) {
            $this->notify($event);
        }
    }
    /**
     * Main component in event management system
     *
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }
}
