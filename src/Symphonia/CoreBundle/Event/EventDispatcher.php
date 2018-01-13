<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Event;
/**
 * Main component for event dispatching.
 * Composition pattern is used for neat dependency injection
 */
class EventDispatcher
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcher
     */
    private $dispatcher;

    public function __construct(\Symfony\Component\EventDispatcher\EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcher
     */
    public function getDispatcher()
    {
        return $this->dispatcher;
    }
}