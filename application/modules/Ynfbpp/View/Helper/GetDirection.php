<?php

/**
 * Created by IntelliJ IDEA.
 * User: macpro
 * Date: 9/30/16
 * Time: 4:02 PM
 */
class Ynfbpp_View_Helper_GetDirection extends Zend_View_Helper_Abstract
{
    public function getDirection($subject, $viewer = null)
    {
        if (null === $viewer)
        {
            $viewer = Engine_Api::_() -> user() -> getViewer();
        }

        if (!($subject instanceof Core_Model_Item_Abstract)) {
            return '';
        }

        if (empty($subject->latitude)) {
            return '';
        }

        $xhtml = array();
        $xhtml[] = '<div class="uiynfbpp_get_localtion">';
        $xhtml[] = $this->view->htmlLink(
            array(
                'route' => 'ynfbpp_general',
                'action'=> 'direction',
                'item_type' => $subject->getType(),
                'item_id' => $subject->getIdentity()
            ),
            '<span class="ynicon yn-location-arrow"></span><span>' . $this->view->translate("Get direction") . '</span>',
            array(
                'class' => 'buttonlink smoothbox'
            ));
        $xhtml[] = '</div>';

        return implode(PHP_EOL, $xhtml);
    }
}