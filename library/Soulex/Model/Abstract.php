<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Abstract object
 * It has flexible interfaces
 * to set/get class variables
 * ArrayAccess interfaces gives possibility to access protected properties
 * as array
 *
 * @author miholeus
 */
abstract class Soulex_Model_Abstract implements ArrayAccess
{

    public function offsetExists($offset)
    {
        return is_callable(array($this, "get" . ucfirst($offset)));
    }

    public function offsetGet($offset)
    {
        return $this->{"get" . ucfirst($offset)}();
    }

    public function offsetSet($offset, $value)
    {
        $this->{"set" . ucfirst($offset)}($value);
    }

    public function offsetUnset($offset)
    {
        $this->{"set" . ucfirst($offset)}(null);
    }

    public function __construct(array $options = null)
    {
        if(is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function __call($name, $args)
    {
        $accessor = substr($name, 0, 3);
        $property = substr($name, 3);
        switch($accessor) {
            case 'get':
                if('mapper' == strtolower($property) || !$property = $this->validateAttribute($property)) {
                    throw new BadMethodCallException('Getting property error: Invalid property ' . $name . '!');
                }
                return $this->$property;
            break;
            case 'set':
                if('mapper' == strtolower($property) || !$property = $this->validateAttribute($property)) {
                    throw new BadMethodCallException('Setting property error: Invalid property ' . $name . '!');
                }
                $this->$property = $args[0];
                return $this;
            break;
            default:
                throw new BadMethodCallException("Calling to unknown method or property "
                        . $name);
        }
    }

    protected function validateAttribute($name)
    {
        $name = $this->_lcfirst($name);

//        $name = $this->transformName($name);

        /**
         * Is not good code for PHP5
         */
        /*
        if (in_array($name,
            array_keys(get_class_vars(get_class($this))))) {
            return $name;
        }
         *
         */
        $reflection = new ReflectionClass($this);
        if(in_array($name, array_keys($reflection->getdefaultProperties()))) {
            return $name;
        }
        return false;
    }
    /**
     * Returns array of all protected properties that can be
     * accessed through get*() methods
     *
     * @return array
     */
    public function toArray()
    {
        $arr = array();
        $reflect = new Zend_Reflection_Class($this);

        $properties = $reflect->getProperties(
                Zend_Reflection_Property::IS_PROTECTED);
        $propertiesNames = array();
        foreach($properties as $property) {
            if(substr($property->getName(), 0, 1) != "_" &&
                    substr($property->getName(), 1, 6) != "mapper") {
                $propertiesNames[] = $property->getName();
            }
        }

        foreach($propertiesNames as $prop) {
            $methodName = "get" . ucfirst($prop);
            $arr[$prop] = $this->$methodName();
        }

        return $arr;
    }
    /**
     * Using setXXX() methods to set values
     * Option keys with underscore are replaced with string
     * in which first occurence of underscore is deleted and
     * next letter is upper cased
     *
     * Examples:
     * <title> = setTitle()
     * <menu_id> = setMenu_id()
     *
     * @param array $options of object variables
     *
     * @return Admin_Model_Abstract
     */
    public function setOptions(array $options)
    {
        $propNames = array_keys(get_class_vars(get_class($this)));
        foreach($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if(in_array($key, $propNames) || is_callable(array($this, $method))) {
                $this->$method($value);
            }
        }
        return $this;
    }

    private function _lcfirst($value)
    {
        if(PHP_VERSION_ID > 50302) {
            $value = lcfirst($value);
        } else {
            $value{0} = strtolower($value{0});
        }
        return $value;
    }
    /**
     * Replace capitalized characters to lower case with underscore
     * before them
     * Transform string like someContent to some_content
     * or someContentMayBeHere to some_content_may_be_here
     *
     * @param string $name
     * @return string
     */
    private function transformName($name)
    {
        $lowered = strtolower($name);
        $char_buff_name = preg_split('//', $name, -1, PREG_SPLIT_NO_EMPTY);
        $char_buff_lowered = preg_split('//', $lowered, -1, PREG_SPLIT_NO_EMPTY);
        $diff = array_diff($char_buff_name, $char_buff_lowered);

        if(count($diff) > 0) {// found difference
            foreach($diff as $chr) {
                $pos = strpos($name, $chr);
                $begin = substr($name, 0, $pos);
                $end = substr($name, $pos);
                $end{0} = strtolower($end{0});
                $name = $begin . '_' . $end;
            }
        }
        return $name;
    }
}
