<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Service\Upload;

/**
 * Determine path to projects photos
 */
class FilePathStrategy implements FilePathStrategyInterface
{
    /**
     * @var IdentifiableInterface
     */
    protected $entity;

    /**
     * Project's path for photos
     *
     * @return string
     * @throws FilePathStrategyException
     */
    public function getFilePath()
    {
        if (null === $this->getEntity()) {
            throw new FilePathStrategyException("ProjectFilePathStrategy requires entity object, null given");
        }
        $path = sprintf('/:path/%d/', $this->getEntity()->getId());
        return $path;
    }

    /**
     * @return IdentifiableInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * @param IdentifiableInterface $entity
     */
    public function setEntity(IdentifiableInterface $entity)
    {
        $this->entity = $entity;
    }
}