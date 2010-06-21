<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */


/**
 * Purifies all data passed to view
 *
 * @author miholeus
 */
class HTMLPurifier_View extends Zend_View {
    protected $_vars = array();

    public function __set($key, $val)
    {

        if(is_string($val)) {
            $purified = $this->escape($val);
        } elseif(is_array($val)) {
            $purified = array_map(array($this, 'traverseSingle'), $val);
        } else { // other types: integers, bools, objects
            $purified = $this->traverseSingle($val);
        }

        $this->_vars[$key] = array(
            'raw' => $val,
            'purified' => $purified
        );

        return $this;
    }

    public function getRaw($key)
    {
        if(isset($this->_vars[$key])) {
            return $this->_vars[$key]['raw'];
        }
        return null;
    }

    public function __get($key)
    {
        if(isset($this->_vars[$key])) {
            return $this->_vars[$key]['purified'];
        }
        return null;
    }

    private function traverseSingle($element)
    {
        if(is_object($element)) {
            $reflect = new ReflectionObject($element);
            foreach ($reflect->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
                $prop->
              $element->{$prop->getName()} = $this->escape($element->{$prop->getName()});
            }
            return $element;
        } else {
            return $this->escape($element);
        }
    }
}
?>
