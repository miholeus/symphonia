<?php

namespace Soulex\CoreBundle\Entity;

/**
 * ContentNode
 */
class ContentNode
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
    private $value;

    /**
     * @var boolean
     */
    private $isinvokable;

    /**
     * @var string
     */
    private $params;

    /**
     * @var integer
     */
    private $pageId;


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
     * @return ContentNode
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
     * Set value
     *
     * @param string $value
     *
     * @return ContentNode
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set isinvokable
     *
     * @param boolean $isinvokable
     *
     * @return ContentNode
     */
    public function setIsinvokable($isinvokable)
    {
        $this->isinvokable = $isinvokable;

        return $this;
    }

    /**
     * Get isinvokable
     *
     * @return boolean
     */
    public function getIsinvokable()
    {
        return $this->isinvokable;
    }

    /**
     * Set params
     *
     * @param string $params
     *
     * @return ContentNode
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
     * Set pageId
     *
     * @param integer $pageId
     *
     * @return ContentNode
     */
    public function setPageId($pageId)
    {
        $this->pageId = $pageId;

        return $this;
    }

    /**
     * Get pageId
     *
     * @return integer
     */
    public function getPageId()
    {
        return $this->pageId;
    }
}
