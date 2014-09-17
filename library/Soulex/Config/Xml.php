<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Xml parser based on Zend_Config_Xml
 * Class parses correctly xmls with attributes
 * <items>
 *   <item attrib="value">another value</item>
 * </items>
 *
 * @author miholeus
 */
class Soulex_Config_Xml extends Zend_Config_Xml
{
    protected $pointer;
    /**
     * Returns a string or an associative and possibly multidimensional array from
     * a SimpleXMLElement.
     * 
     * @param  SimpleXMLElement $xmlObject Convert a SimpleXMLElement into an array
     * @return array|string
     */
    protected function _toArray(SimpleXMLElement $xmlObject)
    {
        $config = array();
        $this->pointer = 0;

        foreach ($xmlObject->children() as $key => $value) {

            if ( sizeof($value->children()) ) {
                if( $value->attributes() ) {
                    $config[$key] = $this->_arrayMergeRecursive($config[$key],
                            $this->_toArray( $value ));
                } else {
                    if( gettype(current($xmlObject->children())) == 'array') {
                        $config[$key][$this->pointer] = $this->_toArray($value);
                    } else {
                        $config[$key] = $this->_toArray($value );
                    }
                }
            } else {
                if( !empty( $config[$key] ) ) {
                    settype($config[$key], 'array');
                    $config[$key][$this->pointer] = (string)$value;
                } else {
                    $config[$key] = (string)$value;
                }
            }

            if( $value->attributes() ) {
                $string = $config[$key][$this->pointer];
                $attributes = array();
                foreach( $value->attributes() as $aKey => $aItem) {
                  $attributes[$aKey] = current($aItem);
                }
                $config[$key][$this->pointer] = array(
                    '_data' => $string,
                    '_attributes' => $attributes
                );
            }

            $this->pointer++;
        }

        return $config;
    }
}
