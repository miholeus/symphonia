<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
  * Soulex_View_Helper_LoadSkin loads skins which are placed in /skins/
  * folder.
  *
  */
class Soulex_View_Helper_LoadSkin extends Zend_View_Helper_Abstract
{
     public function loadSkin ($skin)
     {
         // load the skin config file
         if(APPLICATION_ENV == 'testing') { // otherwise path can not be found during testing
             $skinPath = APPLICATION_PATH . '/../public/skins/';
         } else {
             $skinPath = './skins/';
         }
         $skinData = new Zend_Config_Xml($skinPath . $skin . '/skin.xml');
         $stylesheets = $skinData->stylesheets->stylesheet;
         // append each stylesheet
         if (!is_string($stylesheets)) {
         	 $stylesheets = $stylesheets->toArray();
             foreach ($stylesheets as $stylesheet) {
                 $this->view->headLink()->appendStylesheet('/skins/' . $skin .
                     '/css/' . $stylesheet);
             }
         } else {
     	    $this->view->headLink()->appendStylesheet('/skins/' . $skin .
                '/css/' . $stylesheets);
         }
     }
}
