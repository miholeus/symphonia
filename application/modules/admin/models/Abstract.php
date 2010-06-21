<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Abstract object
 * It has flexible interfaces
 * to set/get mapper objects and class variables
 *
 * @author miholeus
 */
class Admin_Model_Abstract
{
    protected $_mapper = null;
    /**
     * Needs to be overrided
     */
    protected $_mapperClass;

    public function __construct(array $options = null)
    {
        if(is_array($options)) {
            $this->setOptions($options);
        }
    }

    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if(('mapper' == $name) || !method_exists($this, $method)) {
            throw new Zend_Exception('Setting property error: Invalid property ' . $name . '!');
        }
        $this->$method($value);
    }

    public function __get($name)
    {
        $method = 'get' . $name;
        if(('mapper' == $name) || !method_exists($this, $method)) {
            throw new Zend_Exception('Getting property error: Invalid property ' . $name . '!');
        }
        return $this->$method();
    }

    public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }

    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(new $this->_mapperClass);
        }
        return $this->_mapper;
    }
    /**
     * Using setXXX() methods to set values
     * Option keys with underscore are replaced with string
     * in which first occurence of underscore is deleted and
     * next letter is upper cased
     *
     * Examples:
     * <title> = setTitle()
     * <menu_id> = setMenuId()
     *
     * @param array $options of object variables
     *
     * @return Admin_Model_Abstract
     */
    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach($options as $key => $value) {
            if(false !== ($pos = strpos($key, '_'))) {
                $underscore_left = substr($key, 0, $pos);
                $underscore_right = ucfirst(substr($key, $pos + 1));
                $key = $underscore_left . $underscore_right;
            }
            $method = 'set' . ucfirst($key);
            if(in_array($method, $methods)) {
                $this->$method($value);
            }
        }
        return $this;
    }
}
