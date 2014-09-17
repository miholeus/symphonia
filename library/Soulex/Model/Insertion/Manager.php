<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2011 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   New BSD {@link http://www.opensource.org/licenses/bsd-license.php}
 * @version    $Id: $
 */

/**
 * Manager of Insertions
 *
 * @author miholeus
 */
class Soulex_Model_Insertion_Manager
{
    /**
     *
     * @var Soulex_Model_Insertion_Manager
     */
    private static $_instance = null;

    private function __construct(){}

    public static function getInstance()
    {
        if(null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    /**
     * Get source of insertions by group identifier
     *
     * @param int $id group_id field
     * @return string content of insertions
     */
    public function getInsertionsByGroupId($id)
    {
        /**
         * @todo enable caching
         */
        $source = '';
        $mapper = new Soulex_Model_Insertion_Mapper();
        $insertions = $mapper->getItemsBy(array(
            'group_id' => $id,
            'published' => 1
        ));
        if(null !== $insertions) {
            foreach($insertions as $ins) {
                $source .= $ins->getSource();
            }
        }
        return $source;
    }
}