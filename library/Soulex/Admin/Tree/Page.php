<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Generates pages Tree
 * 
 * @author miholeus
 *
 */
class Soulex_Admin_Tree_Page
{
	/**
	 * Generates whole tree of pages
	 * @return string xml tree
	 */
	public function generateTree()
	{
		$html = '<tree id="0">
					<item text="Страницы" id="pages" open="1" child="0" im0="books_close.gif" im1="books_open.gif" im2="books_open.gif">';
		$mdlPage = new Model_Page();
		$pages = $mdlPage->fetchAll(null, 'lft');
		$html .= $this->generatePages($pages);
		$html .= '</item>' . "\n" . '</tree>';
		return $html;
	}
	/**
	 * 
	 * Generates pages section in tree
	 * 
	 * @param Zend_Db_Table_Abstract $pages
	 * @return string xml nodes
	 */
	private function generatePages($pages)
	{
		$tree = $this->prepareTree($pages->toArray());

		$html = $this->traverseTree($tree);
		
		return $html;
	}
	/**
	 * Forms data for further tree traversal
	 * 
	 * @param array $pages
	 * @return array tree
	 */
	private function prepareTree($pages)
	{
		$lvl = 1;
		$cnt = 0;
		$tree = array();
				
		foreach($pages as $page) {
			
			$pageData = new stdClass();
			$pageData->id = $page['id'];
			$pageData->title = $page['title'];
			$pageData->child = 'pages';
			
			if($page['level'] != $lvl) {
				$pageData->child = 'page-' . $tree[$cnt - 1]['element']->id;
				$tree[$cnt - 1]['childs']['element'] = $pageData;
			} else {
				$tree[$cnt] = array('element' => $pageData, 'childs' => array('element' => '', 'childs' => array()));
			}
			
			$lvl = $page['level'];
			$cnt++;
		}

		return $tree;
	}
	
	private function traverseTree($tree)
	{
		$html = '';

		foreach($tree as $node) {
			$page = $node['element'];
			$childs = $node['childs'];

			$html .= '<item text="' . htmlspecialchars($page->title) . '" id="page-' . $page->id . '" child="' . $page->child . '" im0="folderClosed.gif" im1="folderOpen.gif" im2="folderOpen.gif">';
			if($childs['element'] instanceof stdClass) {
				$html .= $this->traverseTree(array($childs));
			}
			$html .= '</item>';
			$html .= "\n\n";
		}
		return $html;
	}
}