<?php
class Advgroup_Model_Link extends Core_Model_Item_Abstract
{
  public function getHref($params = array())
  {
    $params = array_merge(array(
      'route' => 'group_profile',
      'reset' => true,
      'id' => $this->group_id,
    ), $params);
    $route = $params['route'];
    $reset = $params['reset'];
    unset($params['route']);
    unset($params['reset']);
    return Zend_Controller_Front::getInstance()->getRouter()
      ->assemble($params, $route, $reset);
  }
}
