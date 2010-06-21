<?php

class ErrorController extends Zend_Controller_Action
{
    private $logRequestParams = null;

    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                // 404 error -- controller or action not found
                $this->getResponse()->setHttpResponseCode(404);
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = 'Application error';
                break;
        }

        // Log exception, if logger available
        if (false !== ($log = $this->getLog())) {
            $log_message = $this->view->message .': ' . $errors->exception;
            if($this->logRequestParams) {
                $log_message .= PHP_EOL . 'Request Params: ' . PHP_EOL
                    . var_export($errors->request->getParams(), true);
            }
            $log->crit($log_message);
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (null === $bootstrap || !$bootstrap->hasPluginResource('Log')) {
            return false;
        }
        $options = $bootstrap->getOptions();
        if(!empty($options['resources']['log']['stream']['writerParams']['logRequestParams'])) {
            $this->logRequestParams =
                    $options['resources']['log']['stream']['writerParams']['logRequestParams'];
        }
        $log = $bootstrap->getPluginResource('Log')->getLog();
        return $log;
    }

}

