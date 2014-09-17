<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * jQuery TimePicker Addon
 * View Helper invokes timepicker for selected text field
 *
 * @example http://trentrichardson.com/examples/timepicker/
 * @see http://github.com/trentrichardson/jQuery-Timepicker-Addon
 *
 * @author miholeus
 */
class Soulex_View_Helper_JQuery_TimePicker extends Zend_View_Helper_HtmlElement
{
    protected $_enabled = false;
    protected $_defaultScript = '/_data/default/js/jQueryTimepicker/jquery-ui-timepicker-addon.js';
    protected $_defaultStylesheet = '/_data/default/js/jQueryTimepicker/jquery-ui-timepicker-addon.css';

    protected $_defaultUIStylesheet = '/_data/default/js/jquery/ui/css/ui-lightness/jquery-ui-1.8.9.custom.css';

    protected $_supported = array(
        'type' => array(
            'date' => 'datetimepicker', 'time' => 'timepicker',
        )
    );

    protected $_config = array('id' => '', 'value' => '', 'attribs' => array(),
        'options' => array(
            'type' => 'datetimepicker'
        ));

    protected $_scriptPath;
    protected $_scriptFile;

    protected $_stylesheetPath;
    protected $_stylesheetFile;

    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (!method_exists($this, $method)) {
            throw new Zend_Exception('Invalid timepicker property');
        }
        $this->$method($value);
    }

    public function __get($name)
    {
        $method = 'get' . $name;
        if (!method_exists($this, $method)) {
            throw new Zend_Exception('Invalid timepicker property');
        }
        return $this->$method();
    }

    public function setOptions(array $options)
    {
        $methods = get_class_methods($this);
        foreach ($options as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (in_array($method, $methods)) {
                $this->$method($value);
            } else {
                if(array_key_exists($key, $this->_config)) {
                    $this->_config[$key] = $value;
                } else {
                    $this->_config['options'][$key] = $value;
                }
            }
        }
        return $this;
    }

    public function TimePicker($name)
    {
        $this->_config['name'] = $name;
        return $this;
    }
    /**
     * Set TimePicker types
     * TimePicker may be rendered in datetime and time
     *
     * @param string $type type of TimePicker (see supported types)
     * @return Soulex_View_Helper_JQuery_TimePicker
     */
    public function setType($type)
    {
        if(!array_key_exists($type, $this->_supported['type'])) {
            throw new InvalidArgumentException('Invalid timepicke type: ' . $type);
        }
        $this->_config['options']['type'] = $this->_supported['type'][$type];
        return $this;
    }

    public function setScriptPath ($path)
    {
        $this->_scriptPath = rtrim($path,'/');
        return $this;
    }

    public function setScriptFile ($file)
    {
        $this->_scriptFile = (string) $file;
        return $this;
    }

    public function setStylesheetPath ($path)
    {
        $this->_stylesheetPath = rtrim($path, '/');
        return $this;
    }

    public function setStylesheetFile ($file)
    {
        $this->_stylesheetFile = (string) $file;
        return $this;
    }

    public function render()
    {
        if (false === $this->_enabled) {
            $this->_renderScript();
            $this->_renderStylesheet();
            $this->_enabled = true;
        }
        return $this->_renderTimePicker();
    }

    protected function _renderTimePicker()
    {
        extract($this->_config);
        unset($options['type']);

		$disabled = '';
		if (isset($disable)) {
			$disabled = ' disabled="disabled"';
		}

        if(!$id) {// default value for id
            $id = $name;
        }

		$xhtml = '<input type="text" name="' . $this->view->escape($name) . '"'
		. ' id="' . $this->view->escape($id) . '"'
		. $disabled
		. $this->_htmlAttribs($attribs) . ' value="'
		. $this->view->escape($value) . '" />';

        $xhtml .= '<script type="text/javascript">'
                . '$("#' . $id . '").' . $this->_config['options']['type']
                . '(' . json_encode($options) . ');'
                . '</script>';

        return $xhtml;
    }

    protected function _renderScript ()
    {
        if (null === $this->_scriptFile) {
            $script = $this->_defaultScript;
        } else {
            $script = $this->_scriptPath . '/' . $this->_scriptFile;
        }

        $this->view->headScript()->appendFile($script);

        return $this;
    }

    protected function _renderStylesheet()
    {
        if(null === $this->_stylesheetPath) {
            $stylesheet = $this->_defaultStylesheet;
        } else {
            $stylesheet = $this->_stylesheetPath . '/' . $this->_stylesheetFile;
        }

        $this->view->headLink()->appendStylesheet($stylesheet);

        // append UI Stylesheet
        $this->view->headLink()->appendStylesheet($this->_defaultUIStylesheet);

        return $this;
    }
}