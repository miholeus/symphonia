<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Service\Upload;

use Symfony\Component\HttpFoundation\File\File;

interface UploadInterface
{
    /**
     * Upload file
     *
     * @param File $file
     * @param array $options
     * @return mixed
     */
    public function upload(File $file, $options = array());
}