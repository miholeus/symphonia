<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * jQuery OneClickUpload Addon
 *
 * One-Click Upload is a jQuery plugin that replaces the standard file
 * input element, allowing you to use any link, image or element to be used
 * for the purpose of bringing up the "file browse" dialogue. It completes
 * the upload behind the scenes without refreshing the page, making it less
 * obtrusive and easier to style than a standard upload form.
 *
 * @see http://code.google.com/p/ocupload/
 *
 * @author miholeus
 */
class Soulex_View_Helper_JQuery_OneClickUpload extends Zend_View_Helper_HtmlElement
{
    protected $_enabled = false;
    protected $_defaultScript = '/_data/default/js/JQueryOcupload/jquery.ocupload-1.1.2.js';

    protected $_scriptPath;
    protected $_scriptFile;

    protected $_config = array('id' => '', 'value' => '', 'attribs' => array(),
        'options' => array(
    ));

    public function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            if(array_key_exists($key, $this->_config)) {
                $this->_config[$key] = $value;
            } else {
                $this->_config['options'][$key] = $value;
            }
        }
        return $this;
    }

    public function OneClickUpload($name)
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
        return $this;
    }

    public function render()
    {
        if (false === $this->_enabled) {
            $this->_renderScript();
            $this->_enabled = true;
        }
        return $this->_renderUploadFile();
    }

    protected function _renderUploadFile()
    {
        extract($this->_config);

		$disabled = '';
		if ($disable) {
			$disabled = ' disabled="disabled"';
		}

        if(!$id) {// default value for id
            $id = $name;
        }

		$xhtml = '<input type="button" name="' . $this->view->escape($name) . '"'
		. ' id="' . $this->view->escape($id) . '"'
		. $disabled
		. $this->_htmlAttribs($attribs) . ' value="'
		. $this->view->escape($value) . '" />';

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