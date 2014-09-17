<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Truncate multibyte string
 * Crops it by words or length
 *
 * @author miholeus
 */
class Soulex_View_Helper_Truncate extends Zend_View_Helper_Abstract
{
    public function truncate($string, $length = 80, $etc = '...', $charset='UTF-8', 
                                      $break_words = false, $middle = false) 
    { 
        if ($length == 0) 
            return ''; 

        if (strlen($string) > $length) { 
            $length -= min($length, strlen($etc)); 
            if (!$break_words && !$middle) { 
                $string = preg_replace('/\s+?(\S+)?$/u', '', mb_substr($string, 0, $length+1, $charset)); 
            } 
            if(!$middle) { 
                return mb_substr($string, 0, $length, $charset) . $etc; 
            } else { 
                return mb_substr($string, 0, $length/2, $charset) . $etc . mb_substr($string, -$length/2, $charset); 
            } 
        } else { 
            return $string; 
        } 
} 
}