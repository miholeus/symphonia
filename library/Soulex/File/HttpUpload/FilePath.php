<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2012 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

/**
 * File Path manager
 *
 * @author miholeus
 */
class Soulex_File_HttpUpload_FilePath implements Soulex_File_HttpUpload_FilePath_Interface
{
    protected $suffixCounter = null;
    public function apply($method, $settings)
    {
        if(!is_callable(array($this, $method), true)) {
            throw new InvalidArgumentException("Unknown method in " . __CLASS__);
        }
        return $this->{$method}($settings);
    }
    /**
     * Set new file's name in path
     *
     * @param $settings
     * @return null|string
     */
    protected function setName($settings)
    {
        if(!empty($settings['path']) && !empty($settings['file'])) {
            $path = $settings['path'];
            $directory = dirname($path);

            // rename file?
            $fileExtension= substr($settings['file'], strrpos($settings['file'], '.'));

            $fileName = $settings['file'];

            if(isset($settings['fileName'])) {
                if(isset($settings['suffix'])) {
                    $fileName = $settings['fileName'] . '_' . $settings['suffix'] . $fileExtension;
                } else {
                    $fileName = $settings['fileName'] . $fileExtension;
                }
            }

            $path = rtrim($directory, '/') . '/' . $fileName;

            return $path;
        }

        return null;
    }
    /**
     * Smart folder divider
     *
     * @param array $settings
     * @return string|null
     */
    protected function smartFolders($settings)
    {
        if(!empty($settings['path']) && !empty($settings['file'])) {
            $path = $settings['path'];
            $directory = dirname($path);

            $levels = isset($settings['sliceLength']) ? $settings['sliceLength'] : 2;

            // rename file?
            $fileExtension= substr($settings['file'], strrpos($settings['file'], '.'));

            $fileName = $settings['file'];

            if(isset($settings['fileName'])) {
                $fileName = $settings['fileName'];
            }

            $fileNameLength = strlen($fileName);

            $numberOfSplits = ceil($fileNameLength / $levels);
            if($numberOfSplits >= 2) {
                $chars = preg_split('//u',$fileName,-1,PREG_SPLIT_NO_EMPTY);
                $newString = '';
                for($i=1;$i<=count($chars);$i+=2) {
                    $newString .= $chars[$i-1];
                    if(isset($chars[$i])) {
                        $newString .= $chars[$i] . '/';
                    }
                }
                $fileName = rtrim($newString, '/');
            }

            if(isset($settings['suffix'])) {
                if(isset($settings['incSuffix']) && true === $settings['incSuffix']) {
                    if(null === $this->suffixCounter) {
                        $this->suffixCounter = $settings['suffix'];
                    } else {
                        $this->suffixCounter++;
                    }
                    $suffix = $this->suffixCounter;
                } else {
                    $suffix = $settings['suffix'];
                }
                $fileName = $fileName . '_' . $suffix . $fileExtension;
            } else {
                $fileName = $fileName . $fileExtension;
            }

            $path = rtrim($directory, '/') . '/' . $fileName;
            return $path;
        }

        return null;
    }

    public function getSmartPath($path, $file, $fileName, $suffix, $sliceLength = 2)
    {
        return $this->smartFolders(array(
                'path' => $path,
                'file' => $file,
                'fileName' => $fileName,
                'suffix' => $suffix,
                'sliceLength' => $sliceLength
            ));
    }
}