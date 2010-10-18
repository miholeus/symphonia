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

        if($this->_request->isPost()) {
            $post = $this->_request->getPost();
            if(is_array($post['cid'])
                    && count($post['cid']) == $post['boxchecked']) {
                $eventsService->deleteBulk($post['cid']);
            } else {
                if($this->_request->getParam('action') == 'index') {
                    throw new Exception('FCS  is not correct! Wrong request!');
                }
            }
            return $this->_redirect('/admin/events');
        }

        $this->view->events = $eventsService->fetchAll();
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
}
