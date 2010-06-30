<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */


/**
 * The rendering of the view element. Using the TinyMce view helper javascript
 *  initiazion.
 *
 * @author jurian ({@link http://juriansluiman.nl})
 */
/**
 * Update:
 * Added override options support. If you want to enable tinymce for exact element,
 * you can specifiy mode = exact in setOptions() method and point to that element
 * with elements = <element name> in the same method.
 * @author miholeus
 */
class Soulex_View_Helper_FormTinyMce extends Zend_View_Helper_FormTextarea
{
	protected $_tinyMce;
    /**
     * Used to override editor options
     * 
     * @var array
     */
    protected $_overridedOptions = array('mode', 'elements');

	public function FormTinyMce ($name, $value = null, $attribs = null)
	{
		$info = $this->_getInfo($name, $value, $attribs);
		extract($info); // name, value, attribs, options, listsep, disable

		$disabled = '';
		if ($disable) {
			$disabled = ' disabled="disabled"';
		}

		if (empty($attribs['rows'])) {
			$attribs['rows'] = (int) $this->rows;
		}
		if (empty($attribs['cols'])) {
			$attribs['cols'] = (int) $this->cols;
		}

		if (isset($attribs['editorOptions'])) {
			if ($attribs['editorOptions'] instanceof Zend_Config) {
				$attribs['editorOptions'] = $attribs['editorOptions']->toArray();
			}

            // override options
            foreach($this->_overridedOptions as $option) {
                if(isset($attribs[$option])) {
                    $attribs['editorOptions'][$option] = $attribs[$option];
                }
            }

			$this->view->tinyMce()->setOptions($attribs['editorOptions']);
			unset($attribs['editorOptions']);
		}
		$this->view->tinyMce()->render();

		$xhtml = '<textarea name="' . $this->view->escape($name) . '"'
		. ' id="' . $this->view->escape($id) . '"'
		. $disabled
		. $this->_htmlAttribs($attribs) . '>'
		. $this->view->escape($value) . '</textarea>';

		return $xhtml;
	}
}
