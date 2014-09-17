<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_NewsController processes requests to news
 *
 * @author miholeus
 */
class Admin_NewsController extends Soulex_Controller_Abstract
{
    public function indexAction()
    {
        $newsService = new Soulex_Components_News_NewsService();

        $this->view->orderParams = $this->_getOrderParams();
        $order = join(' ', $this->view->orderParams);
        $this->view->filter = array();// view property for where statements
        $limit = $this->_getParam('limit', 20);

        if($this->_request->isPost()) {
            $post = $this->_request->getPost();

            $paginator = $newsService->selectEnabled($post['filter_published'])
                                ->search($post['filter_search'])
                                ->order($order)->paginate();
            $this->view->filter['published'] = $post['filter_published'];

            try {
                if(is_array($post['cid'])
                        && count($post['cid']) == $post['boxchecked']) {
                    try {
                        $newsService->deleteBulk($post['cid']);
                        return $this->_redirect('/admin/news');
                    } catch (Exception $e) {
                        throw new Exception($e->getMessage(), $e->getCode(), $e);
                    }
                } else {
                    throw new Exception("Checksum is not correct");
                }
            } catch (Exception $e) {
                $this->renderSubmenu(false);
                $this->renderError("News deletion failed with the following error: "
                        . $e->getMessage());
            }
        } else {
            $paginator = $newsService->order($order)->paginate();
        }

        // show items per page
        if($limit != 0) {
            $paginator->setItemCountPerPage($limit);
        } else {
            $paginator->setItemCountPerPage(-1);
        }

        $this->view->paginator = $paginator;
        Zend_Registry::set('pagination_limit', $limit);

        $this->view->render('news/index.phtml');
    }

    public function editAction()
    {
        $frmNews = new Admin_Form_News();


        if($this->getRequest()->isPost() &&
            $frmNews->isValid($this->getRequest()->getPost())) {
                $data = array(
                    'id' => $frmNews->getValue('id'),
                    'title' => $frmNews->getValue('title'),
                    'shortDescription' => $frmNews->getValue('short_description'),
                    'detailDescription' => $frmNews->getValue('detail_description'),
                    'published' => $frmNews->getValue('published'),
                    'updatedAt' => date("Y-m-d H:i:s"),
                    'publishedAt' => $frmNews->getValue('published_at')
                );
                $newsService = new Soulex_Components_News_NewsService($data);
                $newsService->save();

                $this->disableContentRender();

                return $this->_forward('index');
        } else {
            $newsService = new Soulex_Components_News_NewsService();
            $currentNews = $newsService->findById($this->getRequest()->getParam('id'));
            $frmNews->populate(array(
               'id' => $currentNews->getId(),
                'title' => $currentNews->getTitle(),
                'short_description' => $currentNews->getShortDescription(),
                'detail_description' => $currentNews->getDetailDescription(),
                'published' => $currentNews->getPublished(),
                'published_at' => $currentNews->getPublishedAt()
            ));
        }

        $this->view->form = $frmNews;

        $this->renderSubmenu(false);
        $this->view->render('news/edit.phtml');
    }

    public function createAction()
    {
        $frmNews = new Admin_Form_News();

        if($this->getRequest()->isPost() &&
               $frmNews->isValid($this->getRequest()->getPost()) ) {
            $data = array(
                'title' => $frmNews->getValue('title'),
                'shortDescription' => $frmNews->getValue('short_description'),
                'detailDescription' => $frmNews->getValue('detail_description'),
                'published' => $frmNews->getValue('published'),
                'createdAt' => date("Y-m-d H:i:s"),
                'publishedAt' => $frmNews->getValue('published_at')
            );

            $newsService = new Soulex_Components_News_NewsService($data);
            $newsService->save();

            $this->disableContentRender();

            return $this->_forward('index');
        }

        $this->view->form = $frmNews;
        
        $this->renderSubmenu(false);
        $this->view->render('news/create.phtml');
    }

    private function _getOrderParams()
    {
        $order = $this->_getParam('order', 'title');
        $direction = $this->_getParam('direction', 'desc');
        /**
         * sets default order if model does not have proper field
         */
        if(!is_callable(array('Admin_Model_News',
            'get' . ucfirst($order)))) {
            $order = 'title';
        }

        if(!in_array(strtolower($direction), array('asc', 'desc'))) {
            $direction = 'desc';
        }

        return array('order' => $order, 'direction' => $direction);
    }
}
