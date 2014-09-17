<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Soulex_Invoke_Action is used to invoke actions while parsing static
 * page's nodes.
 *
 * @author miholeus
 */
class Soulex_Invoke_Action
{
   /**
     *
     * @var Zend_Db_Table_Abstract
     */
    protected $_mapper;

    public function __construct()
    {
        $this->_mapper = new Soulex_Invoke_ActionMapper();
    }

    public function call($id, $params)
    {
        $row = $this->_mapper->findAction($id);
        // @TODO определить папку для контроллеров
        $controller = 'Soulex_Invoke_' . ucfirst($row->controller) . 'Controller';

        if($row->params !== null) {
            $registeredParams = unserialize($row->params);
        } else {
            $registeredParams = array();
        }

        array_walk($params, array($this, 'validateParams'), $registeredParams);

        $frontController = Zend_Controller_Front::getInstance();

        $actor = new $controller($frontController->getRequest(),
                $frontController->getResponse(), $params);

        try {
            $action = $row->action . 'Action';
            return call_user_func(array($actor, $action));
        } catch (Zend_Controller_Action_Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @todo Validate params in controller action
     *
     * @param <type> $key
     * @param <type> $value
     * @param <type> $registeredParams
     */
    private function validateParams($key, $value, $registeredParams)
    {
        
    }
}
?>
