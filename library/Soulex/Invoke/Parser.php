<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Soulex_Invoke_Parser parses static nodes
 *
 * @author miholeus
 */
class Soulex_Invoke_Parser
{
   /**
     * @var Soulex_Invoke_Parser
     */
    private static $_parser = null;
    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request = null;
    /**
     * @var Zend_Controller_Response_Abstract
     */
    protected $_response = null;
    /**
     * Singleton instance
     *
     * @return Soulex_Invoke_Parser
     */
    public static function getInstance()
    {
        if(self::$_parser === null) {
            self::$_parser = new self();
        }
        return self::$_parser;
    }

    /**
     * Parses string to find occurences like <!! ... !!> and replace them
     * with controllers call
     * @uses analyze method
     *
     * @param string $content
     * @return string
     */
    public function parse($content)
    {
        if(self::$_parser === null) {
            throw new Zend_Exception('Parser was not set');
        }

        return preg_replace_callback("~<!!(.*?)!!>~", array(self::$_parser, 'analyze'), $content);
        
    }
    
    private function analyze($matches)
    {
        // we'll search for it
        $comand = 'invoke';
        $available_invokes = array('action'); // for now we have only action invokes
        // $matches[1] -- contains markers to replace
        if(is_array($matches) && isset($matches[1])) {
            $text_to_analyze = $matches[1];
            preg_match("~\b" . $comand . "\b\s(.*)~", $text_to_analyze, $method);

            if(is_array($method) && isset($method[1])) {
                preg_match("~\b(" . implode("|", $available_invokes) . ")\((.*)\)~", $method[1], $invocation);

                if(is_array($invocation) && isset($invocation[1])) {
                    // invoke method with params if they exist
                    if(isset($invocation[2])) {
                        $invoke_params = explode(',', $invocation[2]);
                    } else {
                        $invoke_params = null;
                    }

                    if(is_callable(array($this, 'invoke_' . $invocation[1]))) {
                        return call_user_func_array(array($this, 'invoke_' . $invocation[1]), array($invoke_params)) ;
                    }
                }
            }

        }
    }

    private function invoke_action($invoke_params)
    {
        // removes whitespaces and & in params
        if(count($invoke_params) > 0) {
            foreach($invoke_params as $paramKey => $paramValue) {
                $paramValue = str_replace(array("&", " "), "", $paramValue);
                $invoke_params[$paramKey] = $paramValue;
            }

            $action_id = array_shift($invoke_params);

            parse_str(implode("&", $invoke_params), $action_params);

            $actionBroker = new Soulex_Invoke_Action();
            
            try {
                return $actionBroker->call($action_id, $action_params);
            } catch (Zend_Exception $e) {
                // error in call method
                return 'Error! ' . $e->getMessage();
            }

        }
    }
}
?>
