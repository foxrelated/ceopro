<?php

class Ynmultilisting_Widget_MostLikedListingController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $num_of_listings = $this->_getParam('num_of_listings', 3);
        $listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
        if ($listingtype) {
            $params = array(
                'publish' => 1,
                'limit' => $num_of_listings,
                'order' => 'like_count',
                'direction' => 'DESC'
            );
            $listings = $listingtype->getAllListings($params);
            if (count($listings) == 0) {
                $this->setNoRender(true);
            }
            $this->view->listings = $listings;
            $view_mode = $listingtype->most_liked_widget;
            $session = new Zend_Session_Namespace('mobile');
            if ($session->mobile) {
                $view_mode = '1';
            }
            $this->view->view_mode = $view_mode;
        } else {
            $this->setNoRender(true);
        }
    }
}