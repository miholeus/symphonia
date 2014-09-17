<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * The rendering of the view element. Using the TinyBrowser view helper javascript
 *  initialization.
 *
 * @author miholeus
 */
class Soulex_View_Helper_FormTinyBrowser extends Zend_View_Helper_FormText
{
    public function FormTinyBrowser ($name, $value = null, $attribs = null)
    {
		$info = $this->_getInfo($name, $value, $attribs);
		extract($info); // name, value, attribs, options, listsep, disable

		$disabled = '';
		if ($disable) {
			$disabled = ' disabled="disabled"';
		}

        if(isset($attribs['browserOptions'])) {
            $this->view->tinyBrowser($name)->setOptions(array(
                'browserOptions' => $attribs['browserOptions']
            ));
            unset($attribs['browserOptions']);
        }

        return $this->view->tinyBrowser($name)->setOptions(array(
            'attribs' => $attribs, 'value' => $value))->render();
    }
}