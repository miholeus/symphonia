<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * getUrlReverseSorting is used in href attributes to specify reverse
 * sorting url if sorting param was choosed
 * For example: /admin/user/list/order/firstname/direction/desc if
 * direction `asc` was chosen
 *
 * @author miholeus
 */
class Soulex_View_Helper_GetUrlReverseSorting extends Zend_View_Helper_Abstract
{
    /**
     *
     * @param array $urlParams include module, controller, action
     * @param string $orderField sort by field
     * @return string new url
     */
    public function getUrlReverseSorting($urlParams, $orderField)
    {
        $direction = 'desc';
        if($this->view->orderParams['direction'] == 'desc') {
            $direction = 'asc';
        }
        $uriPart = $urlParams['action'] == 'index' ?
                    '/' . $urlParams['module'] . '/' . $urlParams['controller']
                    . '/index' :
                    $this->view->url($urlParams, null, true);
        return $uriPart . '/order/'
                    . $orderField . '/direction/' . $direction;
    }
}
