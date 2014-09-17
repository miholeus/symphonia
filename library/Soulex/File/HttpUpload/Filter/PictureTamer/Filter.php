<?php

class Soulex_File_HttpUpload_Filter_PictureTamer_Filter implements Soulex_File_HttpUpload_Filter_Interface
{
    /*
      * Служит для записи ошибок при выполнении операций
      */
    public $errorMsg;
    /*
      * массив с информацией о файлах после применения фильтра
      */
    private $post_processed_file;
    /*
      * Содержит настройки класса
      */
    protected $settings;
    /*
      * сторонние библиотеки
      */
    private $_library;

    /**
     * Get degrees number by clockwise and flip param to rotate photo
     *
     * @param string $imagePath Путь к изображению
     *
     * @throws Soulex_File_HttpUpload_Filter_PictureTamer_Exception
     * @return array|false
     */
    public static function getRotation($imagePath)
    {
        if (!is_readable($imagePath)) {
            throw new Soulex_File_HttpUpload_Filter_PictureTamer_Exception("Cannot read '{$imagePath}' file!");
        }

        if (!function_exists('exif_read_data')) {
            return false;
        }

        $data  = exif_read_data($imagePath);
        $orientation = isset($data['Orientation'])
            ? (int)$data['Orientation']
            : 0;

        switch($orientation) {
        case 1:
            $rotate = 0;
            $flip = false;
            break;
        case 2:
            $rotate = 0;
            $flip = true;
            break;
        case 3:
            $rotate = 180;
            $flip = false;
            break;
        case 4:
            $rotate = 180;
            $flip = true;
            break;
        case 5:
            $rotate = 90;
            $flip = true;
            break;
        case 6:
            $rotate = 90;
            $flip = false;
            break;
        case 7:
            $rotate = 270;
            $flip = true;
            break;
        case 8:
            $rotate = 270;
            $flip = false;
            break;
        default:
            $rotate = 0;
            $flip = false;
        }

        return array($rotate, $flip);
    }
    /**
     * Make thumbs
     *
     * @param PhpThumbAdapter $adapter
     * @param array           $options [file_fullpath - path to original file to make
     *                                 thumb from, widht/height - width and height of new image,
     *                                 dstpathfile - new file path destination]
     *
     * @throws Soulex_File_HttpUpload_Filter_PictureTamer_Exception
     * @return string new file name path
     */
    public function makeThumb(PhpThumbAdapter $adapter, $options)
    {
        $thumb = $adapter->create($options['file_fullpath']);

        if(!isset($options['width']) || !isset($options['height'])) {
            throw new Soulex_File_HttpUpload_Filter_PictureTamer_Exception(
                'width and height parameters are required to make thumb');
        }

        //thumbnail creation
        if(isset($options['adaptive']) && $options['adaptive'] === true) {//Adaptive Resize?
            $thumb->adaptiveResize($options['width'], $options['height']);
        } else {
            $thumb->resize($options['width'], $options['height']);
        }

        if(true === $options['orientation']) {
            $rotateOptions = self::getRotation($options['file_fullpath']);
            if(false !== $rotateOptions) {
                $rotate = $rotateOptions[0];
                $flip = $rotateOptions[1];

                if(true === $flip) {
                    $rotate += 180;
                }

                $thumb->rotateImageNDegrees(-$rotate);
            }
        }

        $newfilename = isset($options['dstpathfile']) ? $options['dstpathfile']
            : $options['file_fullpath'];
        $dirname = dirname($newfilename);
        if(!is_dir($dirname)) {
            $oldumask = umask(0) ;
            mkdir($dirname, 0777, true);
            umask($oldumask);
        }
        $thumb->save($newfilename);

        return $newfilename;
    }

    public function __construct()
    {

        $this->post_processed_file = array();

    }

    /**
     * Наложение фильтра
     *
     * @return void
     *
     * @param object $filter
     * @param object $params[optional]
     *
     * @throws Soulex_File_HttpUpload_Filter_PictureTamer_Exception
     */
    public function apply($filter, $params = null)
    {
        try {

            if( !method_exists( $this, $filter ) ) {

                throw new Soulex_File_HttpUpload_Filter_PictureTamer_Exception(__CLASS__ . ' :' . $filter . ' doesn\'t exist');

            }

            $this->settings['upload_params'] = $params;

            call_user_func(array($this, $filter), $params);

        } catch (Soulex_File_HttpUpload_Filter_PictureTamer_Exception $e) {

            $this->errorMsg = $e->getMessage();

        }

    }

    /**
     * Получение информации об обработанных файлах
     *
     * @return array массив с указанием файлов после обработки фильтром
     */
    public function get_processed_file()
    {
        return $this->post_processed_file;
    }

    /**
     * Функция ресайза изображения
     *
     * @return void
     *
     * @param object $params
     */
    private function resize($params)
    {
        //default width/height values
        $_width = 100;
        $_height = 100;

        $this->loadLibrary('PhpThumbAdapter');

        $fileInfo = pathinfo( basename( $params['uploaded_file_fullpath'] ) );

        $dir = dirname( $params['uploaded_file_fullpath'] ) . '/';
//        $dir = $params['folder'];

        $adapter = $this->getInstanceFromLibrary('PhpThumbAdapter');

        if(isset($params['scales'])) {
            $rowCount = count($this->post_processed_file);
            foreach($params['scales'] as $index => $imgValues) {

                // name of thumbnail
                if(isset($params['suffix'])) {
                    $imgName = $fileInfo['filename'] . '_' . $params['suffix']
                        . '_' . $index . '.' . $fileInfo['extension'];
                } else {
                    $imgName = $fileInfo['filename'] . '_' . $index
                        . '.' . $fileInfo['extension'];
                }
                /**
                 * Place file into subfolders, useful if uploader random_filename
                 * option is set to TRUE, then filename like
                 * b026324c6904b2a9cb4b88d6d61c81d1 will be divided to
                 * b0/26/324c6904b2a9cb4b88d6d61c81d1 and we will get more
                 * structured folders for a lot number of files
                 */
                if(isset($params['foldersdivide'])) {

                    $imgName = md5($imgName) . time() . '.' . $fileInfo['extension'];
                    $fileNameLength = strlen($imgName) - 4; // extract dot and type - 4 symbols
                    $levels = 2;
                    $numberOfSplits = intval($fileNameLength / $levels);
                    if($numberOfSplits >= 2) {
                        $rootFolder = substr($imgName, 0, 2);
                        $subFolder = substr($imgName, 2, 2);
                        $imgName = $rootFolder . DIRECTORY_SEPARATOR
                            . $subFolder . DIRECTORY_SEPARATOR
                            . substr($imgName, 4, strlen($imgName) - 4);
                    }
                }
                $thumbname = $dir . $imgName;

                // thumbnail creation
                $width = isset($imgValues['width']) ? $imgValues['width'] : $_width;
                $height = isset($imgValues['height']) ? $imgValues['height'] : $_height;
                $adaptive = isset($imgValues['adaptive']) ? $imgValues['adaptive'] : $params['adaptive'];
                $orientation = isset($params['orientation']) ? $params['orientation'] : false;
                $thumbfile = $this->makeThumb($adapter, array(
                    'file_fullpath' => $params['uploaded_file_fullpath'],
                    'width' => $width,
                    'height' => $height,
                    'adaptive' => $adaptive,
                    'orientation' => $orientation,
                    'dstpathfile' => $thumbname
                ));

                $this->post_processed_file[$rowCount][$index] = array(
                    'name'         => $fileInfo['filename'],
                    'image'        => $thumbfile,
                    'image_size'   => filesize($thumbfile),
                    'mime_type'    => $params['uploaded_file_mime']
                );
            }
        }
        // destroy uploaded image after all operations
//        unlink($params['uploaded_file_fullpath']);
    }

    private function grayscaleImage($filename, $extension, $bwimage_path)
    {
        /*
           * Making grayscale image
           * Example from http://php.about.com/od/gdlibrary/ss/grayscale_gd.htm
           */

        // Get the dimensions
        list($width, $height) = getimagesize($filename);

        // Define our source image
        $_extension = strtolower($extension);

        switch($_extension) {
        case 'gif':
            $source = imagecreatefromgif($filename);
            break;
        case 'png':
            $source = imagecreatefrompng($filename);
            break;
        case 'jpg':
            $source = imagecreatefromjpeg($filename);
            break;
        default:
            $source = imagecreatefromjpeg($filename);
        }

        // Creating the Canvas
        $bwimage= imagecreate($width, $height);

        //Creates the 256 color palette
        for ($c=0;$c<256;$c++) {
            $palette[$c] = imagecolorallocate($bwimage,$c,$c,$c);
        }

        //Reads the origonal colors pixel by pixel
        for ($y=0;$y<$height;$y++)
        {
            for ($x=0;$x<$width;$x++)
            {
                $rgb = imagecolorat($source,$x,$y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                //This is where we actually use yiq to modify our rbg values, and then convert them to our grayscale palette
                $gs = $this->yiq($r,$g,$b);
                imagesetpixel($bwimage,$x,$y,$palette[$gs]);
            }
        }

        // Outputs image
        switch($_extension) {
        case 'gif':
            imagegif($bwimage, $bwimage_path);
            break;
        case 'png':
            imagepng($bwimage, $bwimage_path);
            break;
        case 'jpg':
            imagejpeg($bwimage, $bwimage_path);
            break;
        default:
            imagejpeg($bwimage, $bwimage_path);
        }
    }

    //Creates yiq function
    private function yiq($r,$g,$b)
    {
        return (($r*0.299)+($g*0.587)+($b*0.114));
    }

    /**
     * Загрузка библиотек для обработки картинок
     *
     * @return
     * @param object $library
     */
    private function loadLibrary($library)
    {
        if( !isset( $this->_library[$library] ) ) {

            require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'library' . DIRECTORY_SEPARATOR . $library . '.php');

            $this->_library[$library] = array('object'=> new $library);

        }

        return $this;
    }

    private function getInstanceFromLibrary($name)
    {
        try {
            if( !isset( $this->_library[$name] ) ) {

                throw new Soulex_File_HttpUpload_Filter_PictureTamer_Exception('Can not get library ' . $name . '. Library ' . $name . ' was not loaded.');

            }

            return $this->_library[$name]['object'];

        } catch( Soulex_File_HttpUpload_Filter_PictureTamer_Exception $e) {

            $this->errorMsg = $e->getMessage();

        }
    }
}
