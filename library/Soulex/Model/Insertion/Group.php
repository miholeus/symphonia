<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Insertion's group anemic model
 * Properties are defined as protected
 *
 * @method Soulex_Model_Insertion_Group setId()
 * @method Soulex_Model_Insertion_Group setName()
 *
 * @method getId()
 * @method getName()
 *
 * @author miholeus
 */
class Soulex_Model_Insertion_Group extends Soulex_Model_Abstract
{
    protected $id;
    protected $name;
    /**
     * Set id only once
     *
     * @param int $id
     * @return Soulex_Model_Insertion_Group
     */
    public function setId($id)
    {
        if(!$this->id) {
            $this->id = $id;
        }
        return $this;
    }
}