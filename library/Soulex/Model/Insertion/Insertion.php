<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Insertions anemic model
 * Properties are defined as protected
 *
 * @method Soulex_Model_Insertion_Insertion setId()
 * @method Soulex_Model_Insertion_Insertion setName()
 * @method Soulex_Model_Insertion_Insertion setSource()
 * @method Soulex_Model_Insertion_Insertion setGroup_id()
 * @method Soulex_Model_Insertion_Insertion setGroup_position()
 * @method Soulex_Model_Insertion_Insertion setPublished()
 *
 * @method getId()
 * @method getName()
 * @method getSource()
 * @method getGroup_id()
 * @method getGroup_position()
 * @method getPublished()
 *
 * @author miholeus
 */
class Soulex_Model_Insertion_Insertion extends Soulex_Model_Abstract
{
    protected $id;
    protected $name;
    protected $source;
    protected $group_id;
    protected $group_position;
    protected $published;
    /**
     * Set id only once
     *
     * @param int $id
     * @return Soulex_Model_Insertion_Insertion
     */
    public function setId($id) {
        if(!$this->id) {
            $this->id = $id;
        }
        return $this;
    }
}