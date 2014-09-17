<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Collection of insertions objects
 *
 * @author miholeus
 */
class Soulex_Model_Insertion_Collection extends Soulex_Model_DataMapper_Collection
{
    public function targetClass()
    {
        return 'Soulex_Model_Insertion_Insertion';
    }
}