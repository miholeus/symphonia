<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * TinyBrowser standalone element
 * TinyBrowser is a File manager based on Flash and Ajax
 *
 * @author miholeus
 */
class Soulex_View_Helper_TinyBrowser extends Zend_View_Helper_HtmlElement
{
    protected $_enabled = false;
    protected $_defaultScript = '/_data/default/js/tinybrowser/tb_standalone.js.php';

    protected $_config = array('id' => '', 'value' => '', 'attribs' => array(),
        'browserOptions' => array(
            'type' => 'button',// display button to open file manager
            'value' => 'Open TinyBrowser'
        ));

    protected $_scriptPath;
    protected $_scriptFile;

    public function __set($name, $value)
    {
        $method = 'set' . $name;
        if (!method_exists($this, $method)) {
            throw new Zend_Exception('Invalid tinyBrowser property');
        }
        $this->$method($value);
    }

    public function __get($name)
    {
        $method = 'get' . $name;
        if (!method_exists($this, $method)) {
            throw new Zend_Exception('Invalid tinyBrowser property');
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
                $this->_config[$key] = $value;
            }
        }
        return $this;
    }

    public function TinyBrowser($name)
    {
        $this->_config['name'] = $name;
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
    }

    public function setBrowserOptions(array $options)
    {
        foreach($options as $optionName => $optionValue) {
            $this->_config['browserOptions'][$optionName] = $optionValue;
        }
    }

    public function render()
    {
        if (false === $this->_enabled) {
            $this->_renderScript();
            $this->_enabled = true;
        }
        return $this->_renderTinyBrowser();
    }

    protected function _renderTinyBrowser()
    {
        extract($this->_config);

		$disabled = '';
		if ($disabled) {
			$disabled = ' disabled="disabled"';
		}

        if(!$id) {// default value for id
            $id = $name;
        }

        $browserOptions = $this->_config['browserOptions'];

        if(!isset($browserOptions['name'])) {
            $browserOptions['name'] = 'tinyBrowser-' . $name;
        }

		$xhtml = '<input type="text" name="' . $this->view->escape($name) . '"'
		. ' id="' . $this->view->escape($id) . '"'
		. $disabled
		. $this->_htmlAttribs($attribs) . ' value="'
		. $this->view->escape($value) . '" />';
        if($browserOptions['type'] == 'button') {// render button
            $xhtml .= '&nbsp;<input type="button" name="'
                . $this->view->escape($browserOptions['name']) . '" value="'
                . $this->view->escape($browserOptions['value'])
                . '" onclick="tinyBrowserPopUp(' . "'" . 'type' . "',"
                . "'" . $this->view->escape($name) . "'" . ');" />';
        } else {// render A tag
            $xhtml .= '&nbsp;<a href="javascript:{}" onclick="tinyBrowserPopUp('
                . "'" . 'type' . "',"
                . "'" . $this->view->escape($name) . "'" . ');">'
                . $this->view->escape($browserOptions['value']) . '</a>';
        }

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
}