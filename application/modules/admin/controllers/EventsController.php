<?php
/**
 * @package   Soulex
 * @copyright Copyright (C) 2010 - Present, miholeus
 * @author    miholeus <me@miholeus.com> {@link http://miholeus.com}
 * @license   http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version    $Id: $
 */

/**
 * Admin_EventController processes requests to events
 *
 * @author miholeus
 */
class Admin_EventsController extends Soulex_Controller_Abstract
{
    public function indexAction()
    {
        $eventsService = new Soulex_Components_Events_EventsService();

        $this->view->orderParams = $this->_getOrderParams();
        $order = join(' ', $this->view->orderParams);
        $this->view->filter = array();// view property for where statements
        $limit = $this->_getParam('limit', 20);

        if($this->_request->isPost()) {
            $post = $this->_request->getPost();

            $paginator = $eventsService->selectEnabled($post['filter_published'])
                                ->search($post['filter_search'])
                                ->order($order)->paginate();
            $this->view->filter['published'] = $post['filter_published'];

            try {
                if(is_array($post['cid'])
                        && count($post['cid']) == $post['boxchecked']) {
                    try {
                        $eventsService->deleteBulk($post['cid']);
                        return $this->_redirect('/admin/events');
                    } catch (Exception $e) {
                        throw new Exception($e->getMessage(), $e->getCode(), $e);
                    }
                } else {
                    throw new Exception("Checksum is not correct");
                }
            } catch (Exception $e) {
                $this->renderSubmenu(false);
                $this->renderError("Events deletion failed with the following error: "
                        . $e->getMessage());
            }
        } else {
            $paginator = $eventsService->order($order)->paginate();
        }

        // show items per page
        if($limit != 0) {
            $paginator->setItemCountPerPage($limit);
        } else {
            $paginator->setItemCountPerPage(-1);
        }

        $this->view->paginator = $paginator;
        Zend_Registry::set('pagination_limit', $limit);
        
        $this->view->render('events/index.phtml');
    }

    public function editAction()
    {
        $frmEvents = new Admin_Form_Events();


        if($this->getRequest()->isPost() &&
            $frmEvents->isValid($this->getRequest()->getPost())) {
            $data = array(
                'id' => $frmEvents->getValue('id'),
                'title' => $frmEvents->getValue('title'),
                'shortDescription' => $frmEvents->getValue('short_description'),
                'detailDescription' => $frmEvents->getValue('detail_description'),
                'img_preview' => $frmEvents->getValue('img_preview'),
                'published' => $frmEvents->getValue('published'),
                'updatedAt' => date("Y-m-d H:i:s"),
                'publishedAt' => $frmEvents->getValue('published_at')
            );
            $eventsService = new Soulex_Components_Events_EventsService($data);
            $eventsService->save();

            $this->disableContentRender();

            return $this->_forward('index');
        } else {
            $eventsService = new Soulex_Components_Events_EventsService();
            $currentEvents = $eventsService->findById($this->getRequest()->getParam('id'));
            $frmEvents->populate(array(
               'id' => $currentEvents->getId(),
                'title' => $currentEvents->getTitle(),
                'short_description' => $currentEvents->getShortDescription(),
                'detail_description' => $currentEvents->getDetailDescription(),
                'img_preview' => $currentEvents->getImgPreview(),
                'published' => $currentEvents->getPublished(),
                'published_at' => $currentEvents->getPublishedAt()
            ));
        }

        $this->view->form = $frmEvents;

        $this->renderSubmenu(false);
        $this->view->render('events/edit.phtml');
    }

    public function createAction()
    {
        $frmEvents = new Admin_Form_Events();

        if($this->getRequest()->isPost() &&
               $frmEvents->isValid($this->getRequest()->getPost()) ) {
            $data = array(
                'title' => $frmEvents->getValue('title'),
                'shortDescription' => $frmEvents->getValue('short_description'),
                'detailDescription' => $frmEvents->getValue('detail_description'),
                'published' => $frmEvents->getValue('published'),
                'img_preview' => $frmEvents->getValue('img_preview'),
                'createdAt' => date("Y-m-d H:i:s"),
                'publishedAt' => $frmEvents->getValue('published_at')
            );

            $eventsService = new Soulex_Components_Events_EventsService($data);
            $eventsService->save();

            $this->disableContentRender();

            return $this->_forward('index');
        }

        $this->view->form = $frmEvents;

        $this->renderSubmenu(false);
        $this->view->render('events/create.phtml');
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
