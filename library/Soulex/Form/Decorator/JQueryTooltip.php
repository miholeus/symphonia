<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Show tooltips based on JQuery Tools library
 *
 * @author miholeus
 */
class Soulex_Form_Decorator_JQueryTooltip extends Zend_Form_Decorator_Abstract
{
    protected $_toolsScriptPath = '/skins/frontend/js/jquery/tools/1.2.6/tooltip/jquery.tools.min.js';
    protected $_view;
    /*
     * Storage of tooltips
     * Selectors and their configs for tooltip
     * 
     * @var array
     */
    private $_tooltips = array();
    private $_enabled = false;
    
   
    public function render($content)
    {
        $element = $this->getElement();
        $name    = htmlentities($element->getFullyQualifiedName());
        $label   = $element->getLabel();
        $id      = htmlentities($element->getId());
//        $value   = htmlentities($element->getValue());
        $view = $element->getView();
        $this->_view = $view;
        
        if (null === $view) {
            return $content;
        }
        
        if(empty($this->_options['selector'])) {
            throw new InvalidArgumentException('JQueryTooltip selector is not defined');
        }
        
        $this->renderScripts();
        
        return $content;
    }
    
    protected function renderScripts()
    {
        if (false === $this->_enabled) {
            $this->_view->headScript()->appendFile($this->_toolsScriptPath);
            $style = '.tooltip {' . PHP_EOL
                   .    'background-color:#000;' . PHP_EOL
                   .    'border:1px solid #fff;' . PHP_EOL
                   .    'padding:10px 15px;' . PHP_EOL
                   .    'width:200px;' . PHP_EOL
                   .    'display:none;' . PHP_EOL
                   .    'color:#fff;' . PHP_EOL
                   .    'text-align:left;' . PHP_EOL
                   .    'font-size:12px;' . PHP_EOL

                   .    '/* outline radius for mozilla/firefox only */' . PHP_EOL
                   .    '-moz-box-shadow:0 0 10px #000;'
                   .    '-webkit-box-shadow:0 0 10px #000;'
                   . '}';
            $this->_view->headStyle()->appendStyle($style);
        }
        if (!isset($this->_tooltips[$this->_options['selector']])) {
            $config = array();
            if (isset($this->_options['config'])) {
                $config = $this->_options['config'];
            }
            $script = '$(function() {' . PHP_EOL
                    .   '$("' . $this->_options['selector'] . '").tooltip(' . PHP_EOL
                    .   json_encode($config) . ');' . PHP_EOL
                    . '});';
            $this->_view->headScript()->appendScript($script);
            $this->_tooltips[$this->_options['selector']] = $config;
        }
        $this->_enabled = true;
    }
}