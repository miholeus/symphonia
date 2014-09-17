<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * Locale plugin
 *
 * @author miholeus
 */
class Soulex_Controller_Plugin_Locale extends Zend_Controller_Plugin_Abstract
{
    /**
     * Sets the application locale and translation based on the lang param, if
     * one is not provided it defaults to english
     *
     * @todo Allow default locale to be set by the application config
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function routeShutdown(Zend_Controller_Request_Abstract $request)
    {
        $registry = Zend_Registry::getInstance();

        $locale = $registry->get('Zend_Locale');
        $translate = $registry->get('Zend_Translate');

        /*
         *  For admin panel lang is stored in Zend_Auth.
         *  In other cases we find the lang param in request.
         *  If not set, assign false
         */
        if('admin' == $this->getRequest()->getModuleName()) {
            $auth = Zend_Auth::getInstance();
            $identity = $auth->getIdentity();
            $localeParam = !empty($identity->lang) ? $identity->lang : false;
            /**
             * if user is not authorized or no lang variable exists
             * in Zend_Auth, then default language is assigned
             */
            if(false === $localeParam) {
                $localeParam = $this->_getDefaultLanguage($translate);
            }
        } else {
            $params = $this->getRequest()->getParams();
            $localeParam = isset($params['lang']) ? $params['lang'] : false;
        }
        // If the lang param is false, we'll get whatever the default language is
        if (false === $localeParam) {
            $localeParam = $locale->getLanguage();
        }

        /* As extra precaution, check if a language translation is available.
         * If not, then assign the application default.
         * It pulls a default language translation
         * from the application.ini and assigns en if no default language
         * was set
         */
        if (!$translate->isAvailable($localeParam)) {
            $localeParam = $this->_getDefaultLanguage($translate);
        }

        $locale->setLocale($localeParam);
        $translate->setLocale($locale);

        Zend_Form::setDefaultTranslator($translate);

        $cookieManager = new Soulex_Model_CookieManager();
        $cookieManager->set('lang', $locale->getLanguage(), null, '/');
    }

    protected function _getDefaultLanguage(Zend_Translate $translate)
    {
        $translateOptions = $translate->getOptions();
        $localeParam = !empty($translateOptions['defaultLanguage']) ?
                        $translateOptions['defaultLanguage'] : 'en';
        return $localeParam;
    }
}
