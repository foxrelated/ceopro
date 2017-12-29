<?php
class Ynmultilisting_AdminListingsController extends Core_Controller_Action_Admin
{

    public function indexAction()
    {
        $this->view->navigation = $navigation = Engine_Api::_()->getApi('menus', 'core')
            ->getNavigation('ynmultilisting_admin_main', array(), 'ynmultilisting_admin_main_listings');

        $this->view->form = $form = new Ynmultilisting_Form_Admin_Listings_Search();
		$listingtype_id = $this->_getParam('listingtype_id', 0);
		if ($listingtype_id) {
	        $categories = Engine_Api::_()->getItemTable('ynmultilisting_category')->getListingTypeCategories($listingtype_id);
	        unset($categories[0]);
	        foreach ($categories as $category) {
                $form->category_id->addMultiOption($category->getIdentity(), str_repeat("-- ", $category->level - 1).$category->getTitle());
            }
		}
        $form->populate($this->_getAllParams());
        $values = $form->getValues();
        $values['admin'] = 1;
        $this->view->formValues = $values;

        $page = $this->_getParam('page', 1);
        $this->view->paginator = $paginator = Engine_Api::_()->getItemTable('ynmultilisting_listing')->getListingsPaginator($values);
        $this->view->paginator->setItemCountPerPage(10);
        $this->view->paginator->setCurrentPageNumber($page);
    }

    public function multiselectedAction()
    {
        $action = $this->_getParam('select_action', 'Delete');
        $this->view->action = $action;
        $this->view->ids = $ids = $this->_getParam('ids', null);
        $confirm = $this->_getParam('confirm', false);

        // Check post
        if ($this->getRequest()->isPost() && $confirm == true) {
            $ids_array = explode(",", $ids);
            switch ($action) {
                case 'Delete':
                    foreach ($ids_array as $id) {
                        $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $id);
                        if ($listing && $listing->isDeletable()) {
                            $listing->delete();
                        }
                    }
                    break;

                case 'Approve':
                    foreach ($ids_array as $id) {
                        $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $id);
                        if ($listing) {
                            if ($listing->approved_status == 'pending' && $listing->status == 'open') {
                                $listing->setApproved();
                            }
                        }
                    }
                    break;

                case 'Deny':
                    foreach ($ids_array as $id) {
                        $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $id);
                        if ($listing) {
                            if ($listing->approved_status == 'pending' && $listing->status == 'open') {
                                $listing->approved_status = 'denied';
                                $listing->save();

                                $owner = $listing->getOwner();
                                $viewer = Engine_Api::_()->user()->getViewer();
                                $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                                $notifyApi->addNotification($owner, $viewer, $listing, 'ynmultilisting_listing_deny');
                            }
                        }
                    }
                    break;

                case 'Feature':
                    foreach ($ids_array as $id) {
                        $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $id);
                        if ($listing) {
                            $listing->featured = 1;
                            $listing->save();
                        }
                    }
                    break;

                case 'Unfeature':
                    foreach ($ids_array as $id) {
                        $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $id);
                        if ($listing) {
                            $listing->featured = 0;
                            $listing->save();
                        }
                    }
                    break;
                case 'Open':
                    foreach ($ids_array as $id) {
                        $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $id);
                        if ($listing) {
                            $changeStatus = 'open';
                            $statusNotification = 'opened';
                            $message = Zend_Registry::get('Zend_Translate')->_('Listing has been opened.');
                            $listing->status = $changeStatus;
                            $listing->save();
                            $notifyApi = Engine_Api::_()->getDbtable('notifications', 'activity');
                            $notifyApi->addNotification($listing->getOwner(), $listing, $listing, 'ynmultilisting_listing_status_change', array('status' => $statusNotification));
                            $this->view->message = $message;
                        }
                    }
                    break;
            }

            $this->_helper->redirector->gotoRoute(array('action' => ''));
        }
    }

    public function featureAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if ($id == null) return;
        $value = $this->_getParam('value');
        if ($value == null) return;
        $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $id);
        if ($listing) {
            $listing->featured = $value;
            $listing->feature_expiration_date = null;
            $listing->save();
        }
    }

    public function highlightAction()
    {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id = $this->_getParam('id');
        if ($id == null) return;
        $listtings = Engine_Api::_()->getItemTable('ynmultilisting_listing');
        $listtings->update(array('highlight' => 0), array());
        $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $id);
        if ($listing) {
            $listing->highlight = true;
            $listing->save();
        }
    }

    public function deleteAction()
    {
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->listing_id = $id;
        $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $id);
        if (!$listing->isDeletable()) {
            $this->view->error = true;
            $this->view->message = 'You don\'t have permission to delete this listing.';
            return;
        }

        if ($this->getRequest()->isPost()) {
            $db = Engine_Db_Table::getDefaultAdapter();
            $db->beginTransaction();

            try {
                if ($listing->photo_id) {
                    Engine_Api::_()->getItem('storage_file', $listing->photo_id)->remove();
                }
                $listing->delete();
                $db->commit();
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }

            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }

        // Output
        $this->renderScript('admin-listings/delete.tpl');
    }

    public function closeAction()
    {
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->listing_id = $id;
        $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $id);
        if (!$listing->isDeletable()) {
            $this->view->error = true;
            $this->view->message = 'You don\'t have permission to close this listing.';
            return;
        }

        if ($this->getRequest()->isPost()) {
            $listing->status = 'closed';
            $listing->save();
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }
        // Output
        $this->renderScript('admin-listings/close.tpl');
    }

    public function approveAction()
    {
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->listing_id = $id;
        $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $id);
        if (!$listing->isDeletable()) {
            $this->view->error = true;
            $this->view->message = 'You don\'t have permission to approve this listing.';
            return;
        }

        if ($this->getRequest()->isPost()) {
            $listing->setApproved();
            $listing->save();
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }
        // Output
        $this->renderScript('admin-listings/approve.tpl');
    }

    public function denyAction()
    {
        $this->_helper->layout->setLayout('admin-simple');
        $id = $this->_getParam('id');
        $this->view->listing_id = $id;
        $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $id);
        if (!$listing->isDeletable()) {
            $this->view->error = true;
            $this->view->message = 'You don\'t have permission to deny this listing.';
            return;
        }

        if ($this->getRequest()->isPost()) {
            $listing->approved_status = 'denied';
            $listing->save();
            $this->_forward('success', 'utility', 'core', array(
                'smoothboxClose' => 10,
                'parentRefresh' => 10,
                'messages' => array('')
            ));
        }
        // Output
        $this->renderScript('admin-listings/deny.tpl');
    }

    public function viewInfoAction()
    {
        ini_set('display_startup_errors', 1);
        ini_set('display_errors', 1);
        ini_set('error_reporting', -1);

        $id = $this->_getParam('id');
        $this -> view -> listing_id = $id;
        $this -> view -> listing = $listing = Engine_Api::_()->getItem('ynmultilisting_listing', $id);
    }


}