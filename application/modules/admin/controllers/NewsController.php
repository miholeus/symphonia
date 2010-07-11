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

        if($this->_request->isPost()) {
            $post = $this->_request->getPost();
            if(is_array($post['cid'])
                    && count($post['cid']) == $post['boxchecked']) {
                $newsService->deleteBulk($post['cid']);
            } else {
                if($this->_request->getParam('action') == 'index') {
                    throw new Exception('FCS  is not correct! Wrong request!');
                }
            }
            return $this->_redirect('/admin/news');
        }

        $this->view->news = $newsService->fetchAll();
        $this->view->render('news/index.phtml');
    }

    public function editAction()
    {
        $frmNews = new Admin_Form_News();


        if($this->getRequest()->isPost()) {
            if($frmNews->isValid($this->getRequest()->getPost())) {
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
            }
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
}
