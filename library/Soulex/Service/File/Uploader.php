<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */
use Soulex\File\HttpUpload;

/**
 * File Uploader uploads file to server using HTTP method
 *
 *  Usage:
 * ==============================================================
 *
 *  $uploader = new Soulex_Service_File_Uploader('photo', array(
 *      'auto_rename' => false,
 *      'allowed' => array('image/*'),
 *      'random_filename' => true,
 *      'upload_dir' =>
 *          $_SERVER['DOCUMENT_ROOT'] . '/upload/images/u'
 *  ));
 *  $uploader->filter('PictureTamer', 'resize', 'image/*', array(
 *      'adaptive' => true,
 *      'scales' => array(
 *          'u' => array(
 *              'width' => 580,
 *              'height' => 580
 *          ),
 *          's' => array(
 *              'width' => 36,
 *              'height' => 36
 *          ),
 *          'm' => array(
 *              'width' => 82,
 *              'height' => 82
 *          )
 *      ),
 *      'foldersdivide' => true
 *  ));
 *  $uploader->upload();
 *  $files = $uploader->getUploadedFiles();
 * 
 * ================================================================
 *
 * @author miholeus
 */
class Soulex_Service_File_Uploader
{
    /**
     * Uploader object
     *
     * @var HttpUpload
     */
    protected $_uploader;
    /**
     * Files that were uploaded and then changed by filters
     *
     * @var array
     */
    protected $_uploadedFiles;
    /**
     * Files that user uploaded
     *
     * @var array
     */
    protected $_originalFiles;
    /**
     * Filters that were applied to uploading files
     *
     * @var array
     */
    protected $_filters;
    /**
     * Error messages
     *
     * @var array
     */
    protected $_errors;
    /**
     * Loads uploader object
     *
     * @param string $fileName name of form's input type file
     * @param array $options options of uploader
     */
    public function __construct($fileName, $options = array())
    {
        $this->_uploader = new HttpUpload($fileName);
        if(count($options) > 0) {
            foreach($options as $propertyName => $propertyValue) {
                $this->_uploader->$propertyName = $propertyValue;
            }
        }
    }

    /**
     * Apply filter to uploaded files
     *
     * @param string      $name    filter name
     * @param string      $action  filter action
     * @param string      $mask    mask of files to apply a filter
     * @param arrat|array $options options of filter
     */
    public function filter($name, $action, $mask = null, $options = array())
    {
        $this->_uploader->addFilter($name);
        $this->_uploader->applyFilter(array($name, $mask), $action, $options);
        if(null === $this->_filters) {
            $this->_filters = array();
        }
        $this->_filters[$name] = array(
            'action' => $action,
            'mask' => $mask,
            'options' => $options
        );
    }
    /**
     * Starts uploading process
     * It checks if any filters were applied to files, then
     * filter's get_processed_file method returns that files,
     * Otherwise uploader's uploaded_file_fullpath property has uploaded files
     *
     * Uploaded files are stored in $this->_uploadedFiles property
     */
    public function upload()
    {
        try {
            $this->_uploader->doUpload();

            if(null === $this->_uploadedFiles) {
                $this->_uploadedFiles = array();
            }

            if($this->_uploader->is_uploaded) {
                if(is_array($this->_filters) && count($this->_filters) > 0) {
                    foreach($this->_filters as $filterName => $filterValues) {
                        $this->_uploadedFiles[] = $this->_uploader->filter($filterName)->get_processed_file();
                    }
                } else {
                    $this->_uploadedFiles[] = $this->_uploader->uploaded_file_fullpath;
                }
            } else {
                throw new RuntimeException($this->_uploader->errorMsg);
            }
        } catch (Exception $e) {
            //$this->_uploader->is_uploaded
            if( file_exists($this->_uploader->uploaded_file_fullpath)) {
                unlink($this->_uploader->uploaded_file_fullpath);
            }
            throw new RuntimeException($e->getMessage());
        }

        $this->setOriginalUploadedFiles($this->_uploader->uploaded_files);
    }
    /**
     * Return uploaded files
     *
     * @return array
     */
    public function getUploadedFiles()
    {
        return $this->_uploadedFiles[0];
    }

    protected function setOriginalUploadedFiles($files)
    {
        $this->_originalFiles = $files;
    }

    public function getOriginalUploadedFiles()
    {
        return $this->_originalFiles;
    }
}