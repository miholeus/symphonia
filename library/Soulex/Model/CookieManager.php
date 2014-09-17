<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2012 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Manage cookies
 *
 * @author miholeus
 */

/**
 * Manage cookies
 *
 * This class is mainly used to have chance to create mock objects in unit tests
 */
class Soulex_Model_CookieManager
{
    /**
     * Set cookie
     *
     * @param $name
     * @param null $value
     * @param null $expire
     * @param null $path
     * @param null $domain
     * @param null $secure
     * @param null $httponly
     */
    public function set($name, $value = null, $expire = null, $path = null, $domain = null, $secure = null, $httponly = null)
    {
        setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }
    /**
     * Get cookie
     *
     * @param $name
     *
     * @return null
     */
    public function get($name)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : null;
    }

}
