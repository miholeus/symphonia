<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Event;
/**
 * Interface EventInChainInterface
 * Mainly used for prefixed events.
 * For example, if any event is a part of other events, it should be prefixed with main event
 * for better understanding of domain processes
 */
interface EventInChainInterface extends EventInterface
{
    /**
     * Get prefix for event
     *
     * @return string
     */
    public function getPrefix();
}
