<?php

class Ynfbpp_IndexController extends Core_Controller_Action_Standard
{
    public function directionAction()
    {
        $itemId = $this->_getParam('item_id', 0);
        $itemType = $this->_getParam('item_type', '');
        if (!$itemId) {
            return $this->_helper->requireAuth()->forward();
        }

        $this->view->item = $item = Engine_Api::_()->getItem($itemType, $itemId);

        if (is_null($item)) {
            return $this->_helper->requireAuth()->forward();
        }
    }

    public function getMyLocationAction()
    {
        $latitude = $this->_getParam('latitude');
        $longitude = $this->_getParam('longitude');
        $values = file_get_contents("http://maps.googleapis.com/maps/api/geocode/json?latlng=$latitude,$longitude&sensor=true");
        echo $values;
        die;
    }
}
