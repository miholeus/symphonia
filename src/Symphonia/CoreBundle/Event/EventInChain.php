<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Event;
/**
 * Events that are used in chain should be prefixed
 */
abstract class EventInChain extends Event implements EventInChainInterface
{
    abstract public function getPrefix();

    public function getName()
    {
        return sprintf("%s.%s", $this->getPrefix(), $this->name);
    }

    /**
     * @return string
     */
    public function getClearName()
    {
        return $this->name;
    }

    /**
     * Event description
     *
     * @return string
     */
    abstract public function getDescription();
}
