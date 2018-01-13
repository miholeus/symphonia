<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Event;
/**
 * Interface NotificationInterface
 * Main interface for notification system
 */
interface NotificationInterface
{
    /**
     * Notify about triggered event
     *
     * @param $event
     * @return mixed
     */
    public function notify(Event $event);
}