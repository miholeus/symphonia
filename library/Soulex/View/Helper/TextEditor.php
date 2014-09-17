<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */


/**
 * Thanks to jurian sluiman for wonderful helper :)
 * More detailed info about integration of Zend Framework and tinyMce
 * can be found here:
 * {@link http://juriansluiman.nl/en/blog/article/100/improved-tinymce-solution-for-the-zend-framework}
 *
 * @author jurian
 */

/**
 * Update:
 * Added tinyBrowser support
 *
 * @author miholeus
 */
class Soulex_View_Helper_TextEditor extends Zend_View_Helper_Abstract
{
    public function TextEditor ($objects='',$objectType='object')
    {
		$paramConfig=$this->initEditor();
		$html='';
		foreach($paramConfig['js'] as $val)
		{
			$html.='<script type="text/javascript" src="'.$val.'"></script>';
		}
		foreach($paramConfig['css'] as $val)
		{
			$html.='<link rel="stylesheet" type="text/css" href="'.$val.'" />';
		}
		$html.='<script type="text/javascript">runEditorInitialise("'.$objects.'","'.$objectType.'");
			
			 function openNewWin(url) {
					$.get(url,function(html)
					{
						$.fancybox(html,{
						"transitionIn"  : "elastic",
						"centerOnScroll": false,
						"speedOut"   : 200, 
						"overlayColor"  : "#ccc",
						"scrolling" : "off"
						});	
					}
					);
						
			}
			</script>';		
			$html.='<style>.ui-widget-header{background: #fff;border:0px;}.ui-widget{color: #323232 !important;
    font: 13px/18px "Trebuchet MS",Arial,sans-serif !important;}</style><div id="meta_block" style="display:none;width:240px;height:auto;background:#fff;position:relative;margin:auto;"></div>';
		echo  $html;       
    }
	public function initEditor()
	{
		$configs=array('js'=>array('/_data/default/js/editor/editor/js/widgEditor.js?n50000000'),'css'=>array('/_data/default/js/editor/editor/css/widgEditor.css?new=99op'));
		return $configs;
	}

    
}