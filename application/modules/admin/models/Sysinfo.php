<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * System Information
 *
 * @author miholeus
 */
class Admin_Model_Sysinfo
{
    /**
     *
     * @var Admin_Model_SysinfoMapper
     */
    protected $_mapper;

    protected $_phpSettings;
    /**
     *
     * @var Zend_Config_Ini
     */
    protected $_configSettings;

    public function phpRunningOnInfo()
    {
        return php_uname();
    }

    public function phpVersion()
    {
        return PHP_VERSION;
    }

    public function webServer()
    {
        return $_SERVER['SERVER_SOFTWARE'];
    }

    public function userAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    public function phpSapiName()
    {
        return php_sapi_name();
    }

    public function getPhpInfo()
    {
        ob_start();
        phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
        $phpinfo = ob_get_contents();
        ob_end_clean();
        preg_match_all('#<body[^>]*>(.*)</body>#siU', $phpinfo, $output);
        $output = preg_replace('#<table#', '<table class="adminlist" ', $output[1][0]);
        $output = preg_replace(array(
            '#(\w),(\w)#',
            '#border="0" cellpadding="3" width="600"#',
            '#<hr />#'
            ), array(
                '\1, \2',
                'border="0" cellspacing="1" cellpadding="4" width="95%"',
                ''
            ),
            $output
        );

        $output = str_replace(array('<div class="center">', '</div>'), '', $output);

        return $output;
    }

    public function phpSetting($setting)
    {
        $settings = $this->getPhpSettings();

        if(isset($settings[$setting])) {
            if(is_bool($settings[$setting])) {
                return $settings[$setting] ? 'On' : 'Off';
            }
            return strlen($settings[$setting]) != 0 ? $settings[$setting] : 'None';
        }
        return false;
    }

    public function configSetting($setting)
    {
        $config = $this->getConfigSettings()->toArray();

        $configValue = $config;
        $items = explode('.', $setting);
        foreach($items as $item) {
            $configValue = $configValue[$item];
        }

        if(is_array($configValue)) {
            return implode(', ', $configValue);
        }

        return $configValue;
    }

    public function systemVersion()
    {
        return 'Soulex! 0.1.0 Beta [ Flight ] 30-May-2010 23:00 GMT';
    }

    public function dbVersion()
    {
        return $this->getMapper()->dbVersion();
    }

    public function databaseCollation()
    {
        return $this->getMapper()->databaseCollation();
    }

    public function serverCollation()
    {
        return $this->getMapper()->serverCollation();
    }

    public function connectionCollation()
    {
        return $this->getMapper()->connectionCollation();
    }

    public function setMapper($mapper)
    {
        $this->_mapper = $mapper;
        return $this;
    }

    public function getMapper()
    {
        if (null === $this->_mapper) {
            $this->setMapper(Admin_Model_SysinfoMapper::getInstance());
        }
        return $this->_mapper;
    }

    protected function getPhpSettings()
    {
        if(null === $this->_phpSettings) {
            $this->_phpSettings = array();
            $this->_phpSettings['safe_mode']			= ini_get('safe_mode') == '1';
            $this->_phpSettings['display_errors']		= ini_get('display_errors') == '1';
            $this->_phpSettings['short_open_tag']		= ini_get('short_open_tag') == '1';
            $this->_phpSettings['file_uploads']			= ini_get('file_uploads') == '1';
            $this->_phpSettings['magic_quotes_gpc']		= ini_get('magic_quotes_gpc') == '1';
            $this->_phpSettings['register_globals']		= ini_get('register_globals') == '1';
            $this->_phpSettings['output_buffering']		= (bool) ini_get('output_buffering');
            $this->_phpSettings['open_basedir']			= ini_get('open_basedir');
            $this->_phpSettings['session.save_path']	= ini_get('session.save_path');
            $this->_phpSettings['session.auto_start']	= ini_get('session.auto_start');
            $this->_phpSettings['disable_functions']	= ini_get('disable_functions');
            $this->_phpSettings['xml']					= extension_loaded('xml');
            $this->_phpSettings['zlib']					= extension_loaded('zlib');
            $this->_phpSettings['mbstring']				= extension_loaded('mbstring');
            $this->_phpSettings['iconv']				= function_exists('iconv');
        }
        return $this->_phpSettings;
    }

    protected function getConfigSettings()
    {
        if(is_null($this->_configSettings)) {
            $this->_configSettings = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
        }
        return $this->_configSettings;
    }
}
