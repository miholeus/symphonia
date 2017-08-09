<?php

namespace Symphonia\CoreBundle\Entity;

/**
 * ControllerAction
 */
class ControllerAction
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $params;

    /**
     * @var integer
     */
    private $controllerId;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ControllerAction
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set action
     *
     * @param string $action
     *
     * @return ControllerAction
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set params
     *
     * @param string $params
     *
     * @return ControllerAction
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get params
     *
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set controllerId
     *
     * @param integer $controllerId
     *
     * @return ControllerAction
     */
    public function setControllerId($controllerId)
    {
        $this->controllerId = $controllerId;

        return $this;
    }

    /**
     * Get controllerId
     *
     * @return integer
     */
    public function getControllerId()
    {
        return $this->controllerId;
    }
}
