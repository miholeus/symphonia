<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Abstract collection class that gives opportunity to organize
 * collections of objects.
 * @uses Admin_Model_DataMapperCollection::$mapper object to create objects.
 * Gives lazy load initialization of objects
 *
 * @author miholeus
 */
abstract class Soulex_Model_DataMapper_Collection implements Iterator
{
    /*
     * @var Admin_Model_DataMapper
     */
    protected $mapper;
    /**
     * Total number of items in collection
     *
     * @var int
     */
    protected $total;
    /**
     * Array of data that is used to create objects
     *
     * @var array
     */
    protected $raw = array();
    /**
     * Current pointer in collection
     *
     * @var int
     */
    private $pointer;
    /**
     * Created objects from $raw data
     *
     * @var array
     */
    private $objects = array();

    public function __construct(array $raw = null, Soulex_Model_DataMapper_Abstract $mapper)
    {
        if(!is_null($raw) && !is_null($mapper)) {
            $this->raw = $raw;
            $this->total = count($raw);
        }
        $this->mapper = $mapper;
        $this->pointer = 0;
    }

    public function add(Soulex_Model_Abstract $model)
    {
        $class = $this->targetClass();
        if( ! ($model instanceof  $class)) {
            throw new InvalidArgumentException("Only items of {$model} can be"
                . " added to collection");
        }
        $this->notifyAccess();
        $this->objects[$this->total] = $model;
        $this->total++;
    }
    /**
     * Used to deprecate loading objects of wrong type into collection
     */
    abstract public function targetClass();
    /**
     * Lazy load implementation
     */
    protected function notifyAccess()
    {
        // empty
    }

    private function getRow($num)
    {
        $this->notifyAccess();
        if( $num >= $this->total || $num < 0) {
            return null;
        }
        if(isset($this->objects[$num])) {
            return $this->objects[$num];
        }
        if(isset($this->raw[$num])) {
            // create object
            $object = $this->mapper->createObject($this->raw[$num]);
            $this->objects[$num] = $object;
            return $this->objects[$num];
        }
    }
    /**
     * Return array data of objects
     *
     * @return array
     */
    public function toArray()
    {
        return $this->raw;
    }

    public function rewind()
    {
        $this->pointer = 0;
    }

    public function current()
    {
        return $this->getRow($this->pointer);
    }

    public function key()
    {
        return $this->pointer;
    }

    public function next()
    {
        $row = $this->current();
        if($row) {
            $this->pointer++;
        }
        return $row;
    }

    public function valid()
    {
        return !is_null($this->current());
    }
}