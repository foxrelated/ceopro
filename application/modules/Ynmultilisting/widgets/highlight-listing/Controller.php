<?php

class Ynmultilisting_Widget_HighlightListingController extends Engine_Content_Widget_Abstract
{
    public function indexAction()
    {
        $listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
        if ($listingtype) {
            $params = array(
                'publish' => 1,
                'limit' => 1,
                'highlight' => 1
            );
            $listings = $listingtype->getAllListings($params);
            if (count($listings) == 0) {
                $this->setNoRender(true);
            }
            $listing = null;
            foreach ($listings as $l) {
                $listing = $l;
                break;
            }
            $this->view->listing = $listing;
        } else {
            $this->setNoRender(true);
        }
    }
}