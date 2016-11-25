<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2012 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */

namespace Soulex\File\HttpUpload\FilePath;
/**
 * File path interface
 *
 * @author miholeus
 */
interface FilePathInterface
{
    public function apply($method, $settings);
}
