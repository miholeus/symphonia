<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
$s = 'resources.db.adapter';
$expected = array('resources' => array(
                'db' => array(
                    'adapter'
                )
            ));
$ex = array();
$items = explode(',', $s);
foreach($items as $item) {
    array_push($ex[0], $item);
}
?>
