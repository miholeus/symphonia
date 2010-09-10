<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Soulex_Invoke_Plugin attaches widgets on pages.
 * It is attached as plugin to front controller.
 * Widgets are used as autonomous applications.
 *
 * @author miholeus
 */
class Soulex_Invoke_Plugin extends Zend_Controller_Plugin_Abstract
{
    /**
     * Set of widgets on page
     * 
     * @var array 
     */
    private $_widgets = array();
    /**
     * Adds Widget to global set
     *
     * @param string $action
     * @param string $controller
     * @param string $module
     * @param array $params
     */
    private function addWidget($action, $controller, $module, $params = null)
    {
        array_push($this->_widgets, array(
            'action' => $action,
            'controller' => $controller,
            'module' => $module,
            'params' => $params
        ));
    }
    /**
     * Action stack
     * @var Zend_Controller_Plugin_ActionStack
     */
    protected $_stack;
    /**
     * Page object
     *
     * @var array
     */
    protected $_page = null;
     /**
     * Called before Zend_Controller_Front enters its dispatch loop.
     *
     * @param  Zend_Controller_Request_Abstract $request
     * @return void
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        // enable plugin only for frontend
        if($request->getModuleName() != 'frontend') {
            return false;
        }
        try {
            $this->setPage();
        } catch (Exception $e) {
            $this->setPageNotFound($e->getMessage());
        }

        $stack = $this->_getStack();
        if(count($this->_widgets) > 0):
            foreach ($this->_widgets as $widget) :
                $r = new Zend_Controller_Request_Simple();
                $r->setModuleName($widget['module'])
                  ->setControllerName($widget['controller'])
                  ->setActionName($widget['action'])
                  ->setParams($widget['params']);
                $stack->pushStack($r);
            endforeach;
        endif;

    }
    /**
     * Returns page data
     * @return array
     */
    public function getPage()
    {
        return $this->_page;
    }
    /**
     * Finds page in database,
     * invoke actions if node is a widget
     *
     * @return void
     */
    private function setPage()
    {
        if($this->getRequest()->getParam('id')) {
            $request = $this->getRequest();

			$id = $request->getParam('id');

			$mdlPage = new Model_Page();
			$currentPage = $mdlPage->find($id, array('published' => 1));
            if(!$currentPage) {
				throw new Zend_Exception('Page not Found!');
			}

            if(isset($currentPage['_data'])) {

                $parser = Soulex_Invoke_Parser::getInstance();

                foreach($currentPage['_data'] as $nodeName => $nodeData) {
                    if($nodeData['isInvokable']) {
                        $invoke = unserialize($nodeData['value']);

                        // checks if method invocation has some params
                        if(is_array($nodeData['params'])) {
                            $nodeParams = $nodeData['params'];
                        } else {
                            $nodeParams = array();
                        }

                        $nodeParams['_responseSegment'] = $nodeName;

                        $this->addWidget(
                                $invoke['action'],
                                $invoke['controller'],
                                $invoke['module'],
                                $nodeParams
                        );

                        unset($currentPage['_data'][$nodeName]);
                    } else {
                        $currentPage['_data'][$nodeName]['value'] = $parser->parse($nodeData['value']);
                    }
                }
            }
            $this->_page = $currentPage;
        }
    }
    /**
     * If page was not found
     * we set 404 header and proper title
     *
     * @param string $message
     */
    private function setPageNotFound($message)
    {
        $this->getResponse()->setHttpResponseCode(404);
        $this->_page = array(
            '_data' => array(
                'content' => array('value' => $message)
            ),
            'title' => 'Page Not Found',
            'meta_keywords' => '',
            'meta_description' => ''
        );
    }
    /**
     * Return Action stack plugin object
     *
     * @return Zend_Controller_Plugin_ActionStack
     */
    protected function _getStack()
    {
        if (null === $this->_stack) {
            $front = Zend_Controller_Front::getInstance();
            if (!$front->hasPlugin('Zend_Controller_Plugin_ActionStack')) {
                $stack = new Zend_Controller_Plugin_ActionStack();
                $front->registerPlugin($stack);
            } else {
                $stack = $front->getPlugin('ActionStack');
            }
            $this->_stack = $stack;
        }
        return $this->_stack;
    }
}