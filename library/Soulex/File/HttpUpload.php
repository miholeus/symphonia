<?php
/* +------------------------------------------------------------------------+
 * | HttpUpload.php                                                        |
 * +------------------------------------------------------------------------+
 * | Copyright (c) Nigel. All rights reserved.				                |
 * | Version       0.3                                                      |
 * | Last modified 27/10/2009                                               |
 * | Email         nigel.webmaster@gmail.com                                |
 * +------------------------------------------------------------------------+
 * == BEGIN LICENSE ==
 *
 * Licensed under the new BSD license
 *
 * == END LICENSE ==
 */
/**
 * HttpUpload предназначен для загрузки файлов на сервер
 *
 * Возможности:
 * 1. Защита от перезаписи файлов.
 * 2. Возможность автоматического создания каталогов
 * 3. Возможность генерировать рэндомные имена файлов
 * 4. Возможность задавать маску для запрещенных файлов. По умолчанию запрещается закачка
 *    файлов .htaccess
 * 5. Возможность указывать список допустимых для загрузки расширений файлов
 * 6. Улучшенная система определения MIME типа файла для обеспечения большей безопасности.
 *    Используется расширение PECL, команда UNIX file(), MIME magic
 * 7. Поддержка фильтров для файлов. Фильтры позволяют обрабатывать загруженные вами файлы
 *    всевозможными способами. По умолчанию, доступен фильтр конвертации видео файлов.
 *    Правила создания фильтров:
 * 7a) Все фильтры должны храниться в папке filters относительно текущего
 *     класса.
 * 7b) Каждый фильтр должен находиться в отдельной папке.
 * 7c) Имя фильтра состоит из имени каталога, в котором он находится, и суффикса Filter.
 * 7d) Имена фильтра и файла, в котором он находится, должны совпадать.
 * 7e) Фильтр должен реализовывать интерфейс HttpUpload_Filter_Interface
 * 8.  Мультизагрузка файлов (возможность загружать много файлов с указанием ограничений
 * 	   на загрузку
 * 9.  Возможность указывать максимальный размер на загружаемые файлы
 *
 * @package HttpUpload
 * @author Nigel
 * @date 27.10.2009
 * @version 0.3
 */
class Soulex_File_HttpUpload
{
    const CHECK_MIME_ALLOWED = 1;
    const CHECK_MIME_FORBIDDEN = 2;

    /*
      * директория для загрузки файлов
      *
      * @var string
      */
    public $upload_dir;
    /*
      * загружен ли файл
      *
      * @var bool
      */
    public $is_uploaded;
    /*
      * полный путь последнего загруженного файла
      *
      * @var string
      */
    public $uploaded_file_fullpath;
    /**
     * Массив загруженных файлов
     *
     * @var array
     */
    public $uploaded_files = array();
    /*
      * включить автопереименование файла
      * По умолчанию, если загружаемый файл существует в папке, то он переписывается
      * Если TRUE, то в случае существования файла в папке, новому файлу будет присвоено
      * уникальное имя, несовпадающее с уже существующим
      *
      * @var bool
      */
    public $auto_rename;
    /*
      * включить автоматическое создание папок в случае если указанный путь для
      * загрузки не существует в системе
      * По умолчанию, опция включена
      *
      * @var bool
      */
    public $auto_create_dir;
    /*
      * включить рэндомную генерацию загружаемого файла
      * По умолчанию, опция выключена
      *
      * @var bool
      */
    public $random_filename;
    /*
      * Содержит ошибки во время выполнения различных операций
      *
      * @var string
      */
    public $errorMsg;
    /*
      * список разрешенных mime-тип файлов для загрузки
      * По умолчанию, разрешено все, кроме черного списка $forbidden
      *
      * @var array
      */
    public $allowed;
    /**
     * список запрещенных mime-тип файлов для загрузки
     *
     * @var type
     */
    public $forbidden;
    /*
      * включает опцию проверки имени файла по маске
      *
      * @var bool
      */
    public $forbidden_file_name_mask_enabled;
    /*
      * опция для проверки расширения файла на допустимые к загрузке
      * по умолчанию, включена
      *
      * @var bool
      */
    public $check_filetype;
    /*
      * список запрещенных к загрузке расширений файлов
      * По умолчанию, запрещены файлы .htaccess
      *
      * @var array
      */
    public $forbidden_filetypes;
    /*
      * максимально допустимый размер загружаемых файлов
      *
      * @var int
      */
    public $max_file_size;
    /*
      * маска для запрещенных к загрузке файлов
      *
      * @var string
      */
    protected $forbidden_file_name_mask;
    /*
      * имя поля формы в input type file
      *
      * @var string
      */
    protected $field_name;
    /*
      * Массив с информацией о загружаемых файлах
      *
      * @var array
      */
    protected $_storage;
    /*
      * файл для загрузки во временной директории
      *
      * @var string
      */
    protected $tmp_filename;
    /*
      * логи действий
      *
      * @var array
      */
    protected $log;
    /*
      * имя загружаемого файла
      *
      * @var string
      */
    protected $filename;
    /*
      * mime-тип загружаемого файла
      *
      * @var string
      */
    protected $file_mime;
    /*
      * размер загружаемого файла
      *
      * @var int
      */
    protected $file_size;
    /*
      * ошибки при загрузке файла
      *
      * @var string
      */
    protected $file_error;
    /*
      * сообщения об ошибках при загрузке
      *
      * @var array
      */
    protected $upload_err_messages;
    /*
      * загруженные фильтры
      *
      * @var array
      */
    protected $loaded_filters;
    /**
     * Настройки пути для загружаемых файлов
     *
     * @var array
     */
    protected $upload_path_settings;
    /*
      * Mime-типы файлов
      *
      * @var array
      */
    protected $mimes = array(
        'ai' => 'application/postscript',
        'aif' => 'audio/x-aiff',
        'aifc' => 'audio/x-aiff',
        'aiff' => 'audio/x-aiff',
        'avi' => 'video/x-msvideo',
        'bin' => 'application/macbinary',
        'bmp' => 'image/bmp',
        'class' => 'application/octet-stream',
        'cpt' => 'application/mac-compactpro',
        'css' => 'text/css',
        'csv' => 'text/csv',
        'dcr' => 'application/x-director',
        'dir' => 'application/x-director',
        'dll' => 'application/octet-stream',
        'dms' => 'application/octet-stream',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'dvi' => 'application/x-dvi',
        'dxr' => 'application/x-director',
        'eml' => 'message/rfc822',
        'eps' => 'application/postscript',
        'exe' => 'application/octet-stream',
        'gif' => 'image/gif',
        'gtar' => 'application/x-gtar',
        'gz' => 'application/x-gzip',
        'hqx' => 'application/mac-binhex40',
        'htm' => 'text/html',
        'html' => 'text/html',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'js' => 'application/x-javascript',
        'json' => 'application/json',
        'lha' => 'application/octet-stream',
        'log' => 'text/plain',
        'lzh' => 'application/octet-stream',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'mif' => 'application/vnd.mif',
        'mov' => 'video/quicktime',
        'movie' => 'video/x-sgi-movie',
        'mp2' => 'audio/mpeg',
        'mp3' => 'audio/mpeg',
        'mpe' => 'video/mpeg',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mpga' => 'audio/mpeg',
        'oda' => 'application/oda',
        'pdf' => 'application/pdf',
        'php' => 'application/x-httpd-php',
        'php3' => 'application/x-httpd-php',
        'php4' => 'application/x-httpd-php',
        'phps' => 'application/x-httpd-php-source',
        'phtml' => 'application/x-httpd-php',
        'png' => 'image/png',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pps' => 'application/vnd.ms-powerpoint',
        'ps' => 'application/postscript',
        'psd' => 'application/x-photoshop',
        'qt' => 'video/quicktime',
        'ra' => 'audio/x-realaudio',
        'ram' => 'audio/x-pn-realaudio',
        'rm' => 'audio/x-pn-realaudio',
        'rpm' => 'audio/x-pn-realaudio-plugin',
        'rtf' => 'text/rtf',
        'rtx' => 'text/richtext',
        'rv' => 'video/vnd.rn-realvideo',
        'sea' => 'application/octet-stream',
        'shtml' => 'text/html',
        'sit' => 'application/x-stuffit',
        'smi' => 'application/smil',
        'smil' => 'application/smil',
        'so' => 'application/octet-stream',
        'swf' => 'application/x-shockwave-flash',
        'tar' => 'application/x-tar',
        'text' => 'text/plain',
        'tgz' => 'application/x-tar',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'txt' => 'text/plain',
        'wav' => 'audio/x-wav',
        'wbxml' => 'application/wbxml',
        'wmlc' => 'application/wmlc',
        'word' => 'application/msword',
        'wmv'  => 'video/x-ms-wmv',
        'xht' => 'application/xhtml+xml',
        'xhtml' => 'application/xhtml+xml',
        'xl' => 'application/excel',
        'xls' => 'application/vnd.ms-excel',
        'xlt' => 'application/vnd.ms-excel',
        'xlm' => 'application/vnd.ms-excel',
        'xld' => 'application/vnd.ms-excel',
        'xla' => 'application/vnd.ms-excel',
        'xlc' => 'application/vnd.ms-excel',
        'xlw' => 'application/vnd.ms-excel',
        'xll' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'xml' => 'text/xml',
        'xsl' => 'text/xml',
        'zip' => 'application/zip',

    );
    /**
     * @var \Soulex_File_HttpUpload_FilePath
     */
    protected $pathManager;

    /**
     * Инициализация переменных класса
     *
     * @return \Soulex_File_HttpUpload
     *
     * @param object $name
     */
    public function __construct($name)
    {
        $this->field_name = $name;

        try {
            $this->init();
        } catch (Soulex_File_HttpUpload_Exception $e) {
            $this->errorMsg = $e->getMessage();
        }

    }
    /**
     * Возвращает размер загруженного файла в байтах
     *
     * @return int
     */
    public function getFileSize()
    {
        return $this->file_size;
    }
    /**
     * Возвращает название загруженного файла
     *
     * @return string
     */
    public function getFileName()
    {
        return $this->filename;
    }
    /**
     * Возвращает mime-тип загруженного файла
     *
     * @return string
     */
    public function getFileMime()
    {
        return $this->file_mime;
    }
    /**
     * Возвращает полный путь загруженного файла
     *
     * @return string
     */
    public function getFileFullpath()
    {
        return $this->uploaded_file_fullpath;
    }

    /**
     * Добавление фильтров (обработчиков) для файлов
     *
     * @return HttpUpload object
     *
     * @param object $filterName
     *
     * @throws Soulex_File_HttpUpload_Exception
     */
    public function addFilter($filterName)
    {
        $filterFile = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'HttpUpload'
            . DIRECTORY_SEPARATOR . 'Filter' . DIRECTORY_SEPARATOR
            . $filterName . DIRECTORY_SEPARATOR	. 'Filter.php';
        if( isset( $this->loaded_filters[$filterName] ) ) {
            return $this;
        }

        if ( !file_exists( $filterFile ) ) {
            throw new Soulex_File_HttpUpload_Exception('Filter ' . $filterFile . ' not found');
        }

        require_once($filterFile);

        $className = 'Soulex_File_HttpUpload_Filter_' . $filterName . '_Filter';

        if( !class_exists( $className ) ) {
            throw new Soulex_File_HttpUpload_Exception('Filter ' . $className . ' doesn\'t exist');
        }

        $filterObject = new $className;

        if(!($filterObject instanceof Soulex_File_HttpUpload_Filter_Interface)) {
            throw new Soulex_File_HttpUpload_Exception('Filter ' . $className . ' doesn\'t implements HttpUpload_Filter interface');
        }

        $this->loaded_filters[$filterName] = array(
            'object' => $filterObject,
            'actions' => array()
        );

        return $this;
    }

    /**
     * Применение фильтров
     *
     * @return HttpUpload object
     *
     * @param object $filterArray название фильтра/маска
     * @param object $filter      операция
     * @param object $params      [optional] параметры операции
     *
     * @throws Soulex_File_HttpUpload_Exception
     */
    public function applyFilter($filterArray, $filter, $params = null)
    {
        // были ли ошибки с фильтрами
        if( !empty($this->errorMsg) ) {
            return false;
        }

        if( !is_array( $filterArray ) ) {
            $filterArray = array($filterArray);
        }

        $filterName = $filterArray[0];

        if( !isset( $this->loaded_filters[$filterName] ) ) {
            throw new Soulex_File_HttpUpload_Exception('Apply filter error: filter ' . $filterName . ' is not loaded');
        }

        //маска для типов файлов
        $filterMask = null;

        if( isset( $filterArray[1] ) ) {
            $filterMask = $filterArray[1];
        }

        $this->loaded_filters[$filterName]['actions'][] = array(
            'filter' => $filter,
            'mask' => $filterMask,
            'params' => $params
        );


        return $this;
    }

    /**
     * Выбор указанного фильтра в виде объекта
     * @return HttpUpload_Filter object
     *
     * @param object $filterName название фильтра
     *
     * @throws Soulex_File_HttpUpload_Exception
     */
    public function filter($filterName)
    {

        if( !isset( $this->loaded_filters[$filterName] ) ) {
            throw new Soulex_File_HttpUpload_Exception('Can not get filter ' . $filterName);
        }

        return $this->loaded_filters[$filterName]['object'];

    }
    /**
     * Задаем список запрещенных файлов
     * @return HttpUpload object
     * @param object $masks
     */
    public function forbid_file_by_mask($masks)
    {
        //�������� ����� �������� �� �����, ���� ��� �� ����� ���� ���������
        $this->forbidden_file_name_mask_enabled = true;

        if( !is_array($masks) ) {
            $masks = array($masks);
        }

        $this->forbidden_file_name_mask = $masks;

        return $this;
    }
    /*
      * Осуществляет загрузку файлов.
      * Основной метод класса
      */
    public function doUpload()
    {
        //проверяем ошибки во время инициализации
        if( !empty($this->errorMsg) ) {
            return false;
        }

        if( is_array( $this->_storage ) && count( $this->_storage ) > 0 ) {
            foreach( $this->_storage as $key=>$fileArray ) {
                foreach( $fileArray as $index=>$info ) {
                    //устанавливаем параметры во временные переменные
                    $this->tmp_filename = $info['tmp_filename'];
                    $this->filename 	= $info['filename'];
                    $this->file_mime	= $info['file_mime'];
                    $this->file_size	= $info['file_size'];
                    $this->file_error	= $info['file_error'];
                    $this->doSingleUpload();
                }
            }
        }
    }

    /**
     * Сообщения, записанные в логе
     * @return string $logstring
     */
    public function getlog()
    {
        $logstring = '';
        if(count($this->log)>0) {
            foreach($this->log as $log) {
                $logstring .= $log.'<br />';
            }
        }
        return $logstring;
    }
    /*
      * Вспомогательный метод для загрузки файла
      * Загружает файлы в указанную папку, если папка не указана, то загружает в папку, которая
      * указана в конфиг. файле php.ini
      */
    private function doSingleUpload()
    {
        try {
            //проверка на ошибки при загрузке
            $this->check_upload_errors()->

            //проверка на запрещенные расширения файлов
                check_filetype()->

            //проверка на запрещенные файлы к загрузке
                check_forbidden_file_name_mask()->

            //Проверка на разрешенные к загрузке mime-типы файлов
                check_mime_allowed()->

            //включена ли опция рэндомных файлов
                check_for_random_name()->

            //допустимый ли размер файла
                check_max_file_size();

            if( empty($this->upload_dir) ) {
                if (!$file_to_upload = get_cfg_var('upload_tmp_dir')) {
                    $file_to_upload = dirname(tempnam('', ''));
                }
                $file_to_upload .= '/' . basename($this->filename);
            } else {
                $file_to_upload = $this->upload_dir . DIRECTORY_SEPARATOR . $this->filename;
            }

            // устанавливаем новое место для загрузки
            $file_to_upload = $this->getFilePath($file_to_upload);

            /*
                * Нужно ли переименовывать файл
                */
            if( $this->auto_rename === true ) {
                $i = 1;
                $tmp_file_to_upload = $file_to_upload;

                while(file_exists($tmp_file_to_upload)) {
                    $fileInfo = pathinfo($file_to_upload);
                    $filename = $fileInfo['filename'] . '_' . $i . '.' . $fileInfo['extension'];
                    $tmp_file_to_upload = $fileInfo['dirname'] . DIRECTORY_SEPARATOR . $filename;
                    $i++;
                }

                $file_to_upload = $tmp_file_to_upload;
                unset($tmp_file_to_upload);
            }

            //проверяем каталог на существование
            $this->check_dir($file_to_upload);

            //загружаем файл на сервер
            $this->upload_file($file_to_upload);

        } catch(Soulex_File_HttpUpload_Exception $e) {
            if(file_exists($this->tmp_filename)) {
                unlink($this->tmp_filename);
            }
            $this->errorMsg = $e->getMessage();
        }

    }
    /**
    /**
     * Устанавливаем настройки пути для загружаемого файла
     * Можно загружать файл без изменений, можно создавать вложенность папок
     * и прочие вещи
     *
     * @var Soulex_File_HttpUpload
     * @return \Soulex_File_HttpUpload
     */
    public function setPathSettings($settings)
    {
        $this->upload_path_settings = $settings;
        return $this;
    }
    /**
     * @return \Soulex_File_HttpUpload_FilePath
     */
    protected function getPathManager()
    {
        if(null === $this->pathManager) {
            $this->pathManager = new Soulex_File_HttpUpload_FilePath();
        }
        return $this->pathManager;
    }
    /**
     * Устанавливаем новый путь для загружаемого файла
     *
     * @param string $path
     * @return string
     */
    protected function getFilePath($path)
    {
        if(null !== $this->upload_path_settings && is_array($this->upload_path_settings)) {
            $pathManager = $this->getPathManager();
            $settings = $this->upload_path_settings;
            if(isset($settings['method'])) {
                $settings['path'] = $path;
                $settings['file'] = $this->filename;
                $path = $pathManager->apply($settings['method'], $settings);
            }
        }
        return $path;
    }
    /**
     * запись в лог
     * @param object $string
     * @return HttpUpload object
     */
    private function logit($string)
    {
        $this->log[] = $string;
        return $this;
    }
    /*
      * Инициализация переменных
      */
    private function init()
    {
        $this->upload_dir = null;
        $this->is_uploaded = false;
        $this->uploaded_file_fullpath = null;
        $this->auto_rename = false;
        $this->auto_create_dir = true;
        $this->random_filename = false;
        $this->allowed = array("*");
        $this->forbidden = array();
        $this->check_filetype = true;
        $this->forbidden_filetypes = array('php', 'php3', 'php4', 'phtml');
        $this->forbidden_file_name_mask_enabled = true;
        $this->forbidden_file_name_mask = array('^\.ht');
        $this->errorMsg = '';
        $this->max_file_size = null;
        $this->loaded_filters = array();

        if(!isset($_FILES[$this->field_name]) || !is_array($_FILES[$this->field_name])) {
            throw new Soulex_File_HttpUpload_Exception('Error while initializing: No data received');
        }

        $this->init_upload_err_messages();

        call_user_func_array( array($this, 'add_to_storage'),  array('tmp_filename', 'tmp_name') );
        call_user_func_array( array($this, 'add_to_storage'),  array('filename', 'name') );
        call_user_func_array( array($this, 'add_to_storage'),  array('file_mime', 'type') );
        call_user_func_array( array($this, 'add_to_storage'),  array('file_size', 'size') );
        call_user_func_array( array($this, 'add_to_storage'),  array('file_error', 'error') );

        $this->get_file_mime_all();

    }
    /**
     * Загружаем в хранилище информация о файлах
     * @param object $param
     */
    private function add_to_storage($keyName, $param)
    {
        //md5 key
        $_key = md5($this->field_name);
        $cnt = 0;

        if( is_array( $_FILES[$this->field_name][$param] ) && count( $_FILES[$this->field_name][$param] ) > 0 ) {
            foreach( $_FILES[$this->field_name][$param] as $index=>$value ) {
                $this->_storage[$_key][$cnt][$keyName] = $value;
                $cnt++;
            }
        } else {
            $this->_storage[$_key][$cnt][$keyName] = $_FILES[$this->field_name][$param];
        }

    }
    /**
     * Проверка существования каталога
     * Рекурсивное создание каталогов для загружаемого файла в случае включения опции
     * $auto_create_dir
     * @param object $file_to_upload Путь файла, выбранный при загрузке
     */
    private function check_dir($file_to_upload)
    {
        $dir_to_upload = dirname($file_to_upload);

        if( !is_dir($dir_to_upload) ) {
            /*
                * Нужно ли создавать папку, если ее нет
                */
            if($this->auto_create_dir === true) {
                $oldumask = umask(0) ;
                if(!@mkdir($dir_to_upload, 0777, true)) {
                    throw new Soulex_File_HttpUpload_Exception('Directory creation failed: ' . $dir_to_upload . '!');
                }
                umask( $oldumask ) ;
            } else {
                throw new Soulex_File_HttpUpload_Exception('Directory ' . $dir_to_upload . ' doesn\'t exist');
            }
        }

    }
    /**
     * Загружает выбранный файл в указанную директорию
     *
     * @param object $file_to_upload Загружаемый файл
     *
     * @throws Soulex_File_HttpUpload_Exception
     */
    protected function upload_file($file_to_upload)
    {
        //replace slashes to backslashes in path
        $file_to_upload = preg_replace("/\\\/", "/", $file_to_upload);

        if(move_uploaded_file($this->tmp_filename, $file_to_upload)) {
            $this->is_uploaded = true;
            $this->uploaded_file_fullpath = $file_to_upload;
            $this->uploaded_files[] = array(
                'path' => $this->getFileFullpath(),
                'mime' => $this->getFileMime(),
                'name' => $this->getFileName(),
                'size' => $this->getFileSize()
            );

            if( is_array( $this->loaded_filters ) && count( $this->loaded_filters ) > 0 ) {
                array_walk( $this->loaded_filters, array($this, 'perform_filter_actions') );
            }

        } else {
            throw new Soulex_File_HttpUpload_Exception('upload failed');
        }
    }
    /**
     * Нужно ли генерировать рэндомное имя файла
     * Проверяет, включена ли опция $random_filename
     *
     * @throws Soulex_File_HttpUpload_Exception
     * @return HttpUpload object
     */
    private function check_for_random_name()
    {
        if( empty($this->filename) ) {
            throw new Soulex_File_HttpUpload_Exception('No filename specified for randomizing');
        }

        if( $this->random_filename === true ) {
            $fileInfo = pathinfo($this->filename);
            $_name = md5(uniqid(rand(), true));
            $this->filename = $_name . '.' . $fileInfo['extension'];
        }

        return $this;

    }
    /**
     * Проверка на разрешенные/запрещенные к загрузке файлы
     * @return HttpUpload object
     */
    private function check_mime_allowed()
    {
        $allow = false;

        if( !is_array($this->allowed) ) {
            $this->allowed = array($this->allowed);
        }

        if( !is_array($this->forbidden) ) {
            $this->forbidden = array($this->forbidden);
        }

        if( empty($this->file_mime) ) {
            throw new Soulex_File_HttpUpload_Exception('File MIME type not specified');
        }

        foreach($this->allowed as $mime) {
            $allow = $this->is_mime_allowed($mime, self::CHECK_MIME_ALLOWED);
            if(false === $allow) {
                break;
            }

        }

        foreach($this->forbidden as $mime) {
            $allow = $this->is_mime_allowed($mime, self::CHECK_MIME_FORBIDDEN);
            if(false === $allow) {
                break;
            }

        }

        if( $allow === false ) {
            throw new Soulex_File_HttpUpload_Exception('File\'s MIME type is not allowed');
        }

        return $this;
    }
    /**
     * Функция предназначена для обхода массива разрешенных/запрещенных к загрузке mime-типов
     * @return bool TRUE/FALSE
     * @param object $mime разрешенные mime-типы (является элементом массива $this->allowed)
     * @param object $op массив содержащий тип проверки и результат проверки
     */
    private function is_mime_allowed($mime, $op)
    {
        $allow = false;
        //normalize $allowed mime string
        if( strpos($mime, '/') === false ) {
            $mime = $mime .'/*';
        }

        list($v1, $v2) = explode('/', $mime);
        list($m1, $m2) = explode('/', $this->file_mime);

        if( ($v1 == '*' && $v2 == '*') || ($v1 == $m1 && ($v2 == $m2 || $v2 == '*')) ) {
            if( $op == self::CHECK_MIME_ALLOWED )
                $allow = true;
            elseif( $op == self::CHECK_MIME_FORBIDDEN )
                $allow = false;
            else
                throw new Soulex_File_HttpUpload_Exception('Unknown check mime operation ' . $op);
        }

        return $allow;
    }
    /**
     * Проверка на запрещенные к загрузке имена файлов
     * В основном используется, чтобы предотвратить загрузку системных файлов
     * @return HttpUpload_Object
     */
    private function check_forbidden_file_name_mask()
    {
        $allow = true;

        //включена ли проверка
        if( $this->forbidden_file_name_mask_enabled === true ) {
            if( !is_array($this->forbidden_file_name_mask) ) {
                $this->forbidden_file_name_mask = array($this->forbidden_file_name_mask);
            }

            $allowRules = array_map(array($this, 'is_filename_forbidden'), $this->forbidden_file_name_mask);
            $isNotAllowed = array_key_exists(0, array_flip($allowRules));

            if($isNotAllowed) {
                $allow = false;
            }

            if( $allow === false ) {
                throw new Soulex_File_HttpUpload_Exception('File is not allowed to upload');
            }
        }
        return $this;
    }
    /**
     * Функция предназначена для обхода массива запрещенных к загрузке файлов
     * @return TRUE/FALSE
     * @param object $mask маска файла, заданная в виде регулярного выражения
     * @param object $index индекс массива
     * @param object $allow результат проверки
     */
    private function is_filename_forbidden($mask)
    {
        if(@preg_match('/' . $mask . '/', $this->filename)) {
            return 0;
        }
        return 1;
    }
    /**
     * Проверка на запрещенные к загрузке расширения файлов
     * @return HttpUpload object
     */
    private function check_filetype()
    {
        if( $this->check_filetype === true ) {

            $fileInfo = pathinfo($this->filename);
            $fileInfo['extension'] = strtolower($fileInfo['extension']);

            if( !is_array($this->forbidden_filetypes) ) {
                $this->forbidden_filetypes = array($this->forbidden_filetypes);
            }

            if( in_array($fileInfo['extension'], $this->forbidden_filetypes) ) {
                throw new Soulex_File_HttpUpload_Exception('Restricted extension');
            }
        }

        return $this;

    }
    /**
     * Устанавливаем mime-тип для всех загруженных файлов
     */
    private function get_file_mime_all()
    {
        if( is_array( $this->_storage ) && count( $this->_storage ) > 0 ) {
            foreach( $this->_storage as $key=>$fileArray ) {
                foreach( $fileArray as $index=>$info ) {
                    $this->_storage[$key][$index]['file_mime'] = $this->get_file_mime($info['filename'], $info['file_mime'], $info['tmp_filename']);
                }
            }
        }
    }
    /**
     * Более точное определение mime-типа файла
     * @return $this->file_mime (mime-тип файла)
     */
    private function get_file_mime($fileName, $_mime, $_tmp_filename)
    {

        //$fileInfo = pathinfo($this->filename);

        //$mime_from_browser = $this->file_mime;

        $fileInfo = pathinfo($fileName);
        $mime_from_browser = $_mime;

        $_mime = null;

        if (getenv('MAGIC') === FALSE && PHP_VERSION_ID < 50300) {
            if (substr(PHP_OS, 0, 3) == 'WIN') {
                putenv('MAGIC=' . realpath(ini_get('extension_dir') . '/../') . 'extras/magic.mime');
            } else {
                putenv('MAGIC=/usr/share/file/magic');
            }
        }
        if (function_exists('finfo_open')) {
            // In PHP 5.3 magic file is built-in into PHP
            if(PHP_VERSION_ID > 50300) {
                $f = @finfo_open(FILEINFO_MIME);
            } else {
                $f = @finfo_open(FILEINFO_MIME, getenv('MAGIC'));
            }
            if (is_resource($f)) {
                $mime = finfo_file($f, realpath($_tmp_filename));
                finfo_close($f);
                $_mime = $mime;
                $this->logit('- MIME type detected as ' . $_mime . ' by Fileinfo PECL extension');
            }
        } elseif (class_exists('finfo')) {
            $f = new finfo( FILEINFO_MIME );
            $_mime = $f->file(realpath($_tmp_filename));
            $this->logit('- MIME type detected as ' . $_mime . ' by Fileinfo PECL extension');
        }

        // checks MIME type with shell if unix access is authorized
        if (!$_mime || !is_string($_mime) || empty($_mime)) {
            if (substr(PHP_OS, 0, 3) != 'WIN' && strlen($mime = @shell_exec("file -bi ".escapeshellarg($_tmp_filename))) != 0) {
                $_mime = trim($mime);
                $this->logit('- MIME type detected as ' . $_mime . ' by UNIX file() command');
            }
        }
        // checks MIME type with mime_magic
        if (!$_mime || !is_string($_mime) || empty($_mime)) {
            if (function_exists('mime_content_type')) {
                $_mime = mime_content_type($_tmp_filename);
                $this->logit('- MIME type detected as ' . $_mime . ' by mime_content_type()');
            }
        }

        // default to MIME from browser (or Flash)
        if (!empty($mime_from_browser) && !$_mime || !is_string($_mime) || empty($_mime)) {
            $_mime =$mime_from_browser;
            $this->logit('- MIME type detected as ' . $_mime . ' by browser');
        }

        // we need to work some magic if we upload via Flash
        if ($_mime == 'application/octet-stream' || !$_mime || !is_string($_mime) || empty($_mime)) {
            //if ($_mime == 'application/octet-stream') '- Flash may be rewriting MIME as application/octet-stream';
            //Try to guess MIME type from file extension (' . $fileInfo['extension] . '): ';
            if( isset($this->mimes[$fileInfo['extension']]) ) {
                $_mime = $this->mimes[$fileInfo['extension']];
            }
            if ($_mime == 'application/octet-stream') {
                $this->logit('MIME doesn\t look like anything known');
            } else {
                $this->logit('MIME type set to $_mime');
            }
        }

        if (!$_mime || !is_string($_mime) || empty($_mime)) {
            $this->logit('MIME type couldn\'t be detected!');
        }

        return $_mime;

    }
    /**
     * Применяем выбранные фильтры на закачанные файлы
     * @param object $filterData
     * @param object $filter
     */
    private function perform_filter_actions($filterData, $filter)
    {

        if( is_array( $filterData['actions'] ) && count( $filterData['actions'] ) > 0 ) {

            //изначальные параметры для фильтра
            $params = array(
                'uploaded_file_fullpath'	=>	$this->uploaded_file_fullpath,
                'uploaded_file_orig_name'	=>	$this->filename,
                'uploaded_file_mime'		=>	$this->file_mime,
                'uploaded_file_size'		=>	$this->file_size
            );

            foreach( $filterData['actions'] as $action ) {

                $apply_filter = false;

                //проверяем маску для фильтра
                if( !empty($action['mask'] ) ) {
                    $mask = $action['mask'];
                    $apply_filter = $this->is_mime_allowed($mask, self::CHECK_MIME_ALLOWED);
                } else {
                    $apply_filter = true;
                }

                if( $apply_filter === true ) {
                    //если переданы параметры для фильтра, то склеиваем их с изначальными
                    if( is_array( $action['params'] ) ) {
                        $filterParams = array_merge( $action['params'], $params );
                    } else {
                        $filterParams = $params;
                    }
                    //применяем фильтр
                    $filterData['object']->apply( $action['filter'], $filterParams );
                }
            }
        }
    }
    /**
     * проверка на ошибки при загрузке файлов
     * @return HttpUpload_Object
     */
    private function check_upload_errors()
    {
        list($key, $storValue) = each($this->_storage);

        if( array_key_exists( $this->file_error, $this->upload_err_messages ) ) {
            $_err_array = $this->upload_err_messages[$this->file_error];
            if( $_err_array['err_msg'] != 'UPLOAD_ERR_OK' ) {
                throw new Soulex_File_HttpUpload_Exception($_err_array['err_msg'] . ': ' . $_err_array['err_desc']);
            }
        } else {
            throw new Soulex_File_HttpUpload_Exception('Upload failed: unknown error');
        }

        return $this;

    }
    /**
     * инициализация массива с сообщениями об ошибках при загрузке
     */
    private function init_upload_err_messages()
    {
        $this->upload_err_messages = array(
            0 => array(
                'err_msg' => 'UPLOAD_ERR_OK',
                'err_desc' => 'There is no error, the file uploaded with success'
            ),
            1 => array(
                'err_msg' => 'UPLOAD_ERR_INI_SIZE',
                'err_desc' => 'The uploaded file exceeds the upload_max_filesize directive in php.ini'
            ),
            2 => array(
                'err_msg' => 'UPLOAD_ERR_FORM_SIZE',
                'err_desc' => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form'
            ),
            3 => array(
                'err_msg' => 'UPLOAD_ERR_PARTIAL',
                'err_desc' => 'The uploaded file was only partially uploaded'
            ),
            4 => array(
                'err_msg' => 'UPLOAD_ERR_NO_FILE',
                'err_desc' => 'No file was uploaded',
            ),
            6 => array(
                'err_msg' => 'UPLOAD_ERR_NO_TMP_DIR',
                'err_desc' => 'Missing a temporary folder'
            ),
            7 => array(
                'err_msg' => 'UPLOAD_ERR_CANT_WRITE',
                'err_desc' => 'Failed to write file to disk'
            ),
            8 => array(
                'err_msg' => 'UPLOAD_ERR_EXTENSION',
                'err_desc' => 'File upload stopped by extension'
            )
        );
    }
    /**
     * проверка на максимальный размер файлов
     * @return HttpUpload_Object
     */
    private function check_max_file_size()
    {
        if( !empty($this->max_file_size) ) {
            if( (int)$this->max_file_size == 0 ) {
                throw new Soulex_File_HttpUpload_Exception('Wrong number specified for max_file_size');
            }

            $size = preg_replace_callback("/^([0-9]+)(b|kb|mb|gb)?/i", array($this, 'transform_size_string_to_int'), $this->max_file_size);

            if( $this->file_size >= $size ) {
                throw new Soulex_File_HttpUpload_Exception('File size can not be more than ' . $this->max_file_size);
            }
        }

        return $this;
    }
    /**
     * Конвертирование строковых представлений размера файла в числовые
     * @return int
     * @param object $match
     */
    private function transform_size_string_to_int($match)
    {
        $size = 0;
        $size_literal = array(
            'b' => 1,
            'kb' => 1024,
            'mb' => 1048576,
            'gb' => 1073741824
        );
        if( isset($match[2]) ) {//���� ������ ������ � ���� ������
            $prefix = strtolower($match[2]);
            if( isset($size_literal[$prefix]) ) {
                $size =  $match[1]*$size_literal[$prefix];
            }
        } elseif( isset( $match[1]) ) {//���� ������ ������ � ������
            $size = $match[1];
            $this->max_file_size = $this->max_file_size . 'b';
        }

        return $size;
    }

}