<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Service\Upload;

use Symfony\Component\HttpFoundation\File\File;
use Symphonia\CoreBundle\Service\Exception\UploadFileException;

/**
 * Uploads photo
 */
abstract class UploadPhoto implements UploadInterface
{
    /**
     * @var UploadFile
     */
    private $uploadService;

    /**
     * @var string
     */
    private $uploadPath;

    public function __construct(UploadFile $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected function getContainer()
    {
        return $this->getUploadService()->getContainer();
    }

    /**
     * Upload user profile images
     *
     * @param File $file
     * @param array $options
     * @return array ["path" => [path], "full_path" => [full_path]]
     * @throws UploadFileException
     */
    public function upload(File $file, $options = array())
    {
        if (!isset($options['path'])) {
            $options['path'] = $this->getUploadPath();
        }

        $this->getUploadService()->setUploadPath($options['path']);
        unset($options['path']);

        $uploaded = $this->getUploadService()->upload($file, $options);
        return $uploaded;
    }

    /**
     * @return UploadFile
     */
    public function getUploadService()
    {
        return $this->uploadService;
    }

    /**
     * Get upload directory path
     *
     * @return string
     */
    protected function getUploadPath()
    {
        if (null === $this->uploadPath) {
            return $this->getUploadDirectory();
        }
        return $this->uploadPath;
    }

    /**
     * Root upload directory
     *
     * @return mixed
     */
    abstract public function getUploadDirectory();

    /**
     * @param FilePathStrategyInterface $strategyInterface
     */
    public function setUploadStrategy(FilePathStrategyInterface $strategyInterface)
    {
        $this->uploadPath = $strategyInterface->getFilePath();
        $this->uploadPath = strtr($this->uploadPath, [':path' => trim($this->getUploadDirectory(), '/')]);
    }
}