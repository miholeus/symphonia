<?php
/**
 * This file is part of Symphonia package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symphonia\CoreBundle\Service\Upload;

class UploadProfilePhoto extends UploadPhoto
{
    /**
     * Root upload directory
     *
     * @return mixed
     */
    public function getUploadDirectory()
    {
        return $this->getContainer()->getParameter('profile_upload_photo_dir');
    }
}