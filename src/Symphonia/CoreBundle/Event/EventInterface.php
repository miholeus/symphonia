<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Event;
/**
 * Interface for events
 * Each event should have name
 */
interface EventInterface
{
    /**
     * @return string
     */
    public function getName();
}