<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * GetSortImage used for returning image tag with corresponding
 * image (asc, des) when sorting something
 *
 * @author miholeus
 */
class Soulex_View_Helper_GetSortImage extends Zend_View_Helper_Abstract
{
    public function getSortImage($field)
    {
        if($this->view->orderParams['order'] == $field) {
            return $this->view->orderParams['direction'] == 'desc' ?
                '<img alt="" src="/_data/admin/system/images/sort_desc.png"/>' :
                '<img alt="" src="/_data/admin/system/images/sort_asc.png"/>';
        }
        return '';
    }
}
