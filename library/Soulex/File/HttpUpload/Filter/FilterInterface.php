<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version   $Id: $
 */
namespace Soulex\File\HttpUpload\Filter;

interface FilterInterface
{
	/**
     * Apply filter
     *
	 * @param $filter
	 * @param null $params
	 * @return mixed
	 */
	public function apply($filter, $params = null);
}