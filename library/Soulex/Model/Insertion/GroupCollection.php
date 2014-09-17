<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Collection of insertion's groups
 *
 * @author miholeus
 */
class Soulex_Model_Insertion_GroupCollection extends Soulex_Model_DataMapper_Collection
{
    public function targetClass()
    {
        return 'Soulex_Model_Insertion_Group';
    }
}